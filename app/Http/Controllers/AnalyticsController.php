<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentAnalytics;
use App\Models\ContentInsight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AnalyticsController extends Controller
{
    public function index()
    {
        $contents = Content::latest()->get()->map(function ($content) {
            return $this->buildContentAnalyticsRow($content);
        });

        $totalContents = $contents->count();
        $totalViews = $contents->sum('views');
        $totalLikes = $contents->sum('likes');
        $totalShares = $contents->sum('shares');
        $engagementRate = $this->calculateEngagementRate($totalViews, $totalLikes, $totalShares);

        $topPerformingContent = $contents->sortByDesc('engagement_score')->first();
        $lowestPerformingContent = $contents->sortBy('engagement_score')->first();

        $chartLabels = $contents->pluck('title')->map(fn($title) => \Illuminate\Support\Str::limit($title, 18))->values();
        $viewsChartData = $contents->pluck('views')->values();
        $likesChartData = $contents->pluck('likes')->values();
        $sharesChartData = $contents->pluck('shares')->values();
        $topFiveContents = $contents->sortByDesc('engagement_score')->take(5)->values();

        return view('admin.analytics.index', compact(
            'contents',
            'totalContents',
            'totalViews',
            'totalLikes',
            'totalShares',
            'engagementRate',
            'topPerformingContent',
            'lowestPerformingContent',
            'chartLabels',
            'viewsChartData',
            'likesChartData',
            'sharesChartData',
            'topFiveContents'
        ));
    }

    public function track(Request $request, $contentId)
    {
        $validator = Validator::make($request->all(), [
            'event_type' => 'required|in:view,like,share',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        Content::findOrFail($contentId);
        $analytics = $this->findOrCreateAnalytics($contentId);

        if ($request->event_type === 'view') {
            $analytics->views = (string) ((int) $analytics->views + 1);
        } elseif ($request->event_type === 'like') {
            $analytics->likes = (string) ((int) $analytics->likes + 1);
        } else {
            $analytics->shares = (string) ((int) $analytics->shares + 1);
        }

        $analytics->save();

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->event_type) . ' tracked successfully.',
            'data' => [
                'views' => (int) $analytics->views,
                'likes' => (int) $analytics->likes,
                'shares' => (int) $analytics->shares,
                'engagement_rate' => $this->calculateEngagementRate(
                    (int) $analytics->views,
                    (int) $analytics->likes,
                    (int) $analytics->shares
                ),
            ],
        ]);
    }

    public function generateInsight($contentId)
    {
        $content = Content::findOrFail($contentId);
        $row = $this->buildContentAnalyticsRow($content);

        $prompt = "You are a content performance strategist.\n"
            . "Analyze the content performance data and provide one concise, practical improvement insight.\n"
            . "Return only the final insight text.\n\n"
            . "Title: {$content->title}\n"
            . "Status: {$content->status}\n"
            . "Content length (characters): " . strlen($content->content) . "\n"
            . "Views: {$row['views']}\n"
            . "Likes: {$row['likes']}\n"
            . "Shares: {$row['shares']}\n"
            . "Engagement rate: {$row['engagement_rate']}%\n\n"
            . "Examples of good output:\n"
            . "- Your engagement is low compared to views; strengthen the title and opening hook.\n"
            . "- The content is long and may reduce retention; shorten paragraphs and add clearer sections.\n"
            . "- Likes are healthy but shares are weak; add a stronger CTA and more shareable takeaways.\n";

        try {
            $insightText = $this->callAI($prompt, 120, 0.3);
            $insightText = trim(preg_replace('/^\s*[-•]\s*/u', '', $insightText));

            $insight = ContentInsight::create([
                'content_id' => (string) $content->id,
                'insight_text' => $insightText,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'AI insight generated successfully.',
                'data' => $insight,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function insightsList()
    {
        $insights = ContentInsight::latest()->get()->map(function ($insight) {
            $content = $insight->content_id ? Content::find($insight->content_id) : null;
            $insight->content_title = $content?->title ?? 'Unknown Content';
            return $insight;
        });

        return view('admin.analytics.insights_list', compact('insights'));
    }

    public function updateInsight(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'insight_text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $insight = ContentInsight::findOrFail($id);
        $insight->update([
            'insight_text' => $request->insight_text,
        ]);

        $content = $insight->content_id ? Content::find($insight->content_id) : null;
        $payload = $insight->fresh()->toArray();
        $payload['content_title'] = $content?->title ?? 'Unknown Content';

        return response()->json([
            'success' => true,
            'message' => 'Insight updated successfully.',
            'data' => $payload,
        ]);
    }

    public function destroyInsight($id)
    {
        $insight = ContentInsight::findOrFail($id);
        $insight->delete();

        return response()->json([
            'success' => true,
            'message' => 'Insight deleted successfully.',
        ]);
    }

    private function buildContentAnalyticsRow(Content $content): array
    {
        $analytics = $this->findOrCreateAnalytics($content->id);

        $views = (int) $analytics->views;
        $likes = (int) $analytics->likes;
        $shares = (int) $analytics->shares;
        $engagementRate = $this->calculateEngagementRate($views, $likes, $shares);

        return [
            'id' => $content->id,
            'title' => $content->title,
            'status' => $content->status,
            'views' => $views,
            'likes' => $likes,
            'shares' => $shares,
            'engagement_rate' => $engagementRate,
            'engagement_score' => $likes + ($shares * 2),
            'updated_at' => $content->updated_at,
        ];
    }

    private function findOrCreateAnalytics($contentId): ContentAnalytics
    {
        return ContentAnalytics::firstOrCreate(
            ['content_id' => (string) $contentId],
            ['views' => '0', 'likes' => '0', 'shares' => '0']
        );
    }

    private function calculateEngagementRate(int $views, int $likes, int $shares): float
    {
        if ($views <= 0) {
            return 0;
        }

        return round((($likes + $shares) / $views) * 100, 2);
    }

    private function callAI(string $prompt, int $maxTokens = 300, float $temperature = 0.6): string
    {
        $apiUrl = 'https://router.huggingface.co/featherless-ai/v1/completions';
        $apiKey = env('HF_API_KEY');

        if (! $apiKey) {
            throw new \RuntimeException('HF_API_KEY is not configured.');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post($apiUrl, [
            'model' => 'Qwen/Qwen2.5-7B-Instruct',
            'prompt' => $prompt,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                $response->json('error.message') ?? $response->json('message') ?? 'AI API failed. Try again.'
            );
        }

        $text = trim($response->json('choices.0.text') ?? '');

        if (! $text) {
            throw new \RuntimeException('No insight was returned from the AI service.');
        }

        return $text;
    }
}
