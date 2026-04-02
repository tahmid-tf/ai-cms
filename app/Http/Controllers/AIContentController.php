<?php
namespace App\Http\Controllers;

use App\Models\AIContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AIContentController extends Controller
{
    public function index()
    {
        return view('admin.ai_content');
    }

    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content_type' => 'required|string',
            'prompt'       => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $contentType = $request->content_type;
        $prompt      = $request->prompt;

        $apiUrl = 'https://router.huggingface.co/featherless-ai/v1/completions';
        $apiKey = env('HF_API_KEY');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'HF_API_KEY is not configured.',
            ], 500);
        }

        $body = [
            'model'  => 'Qwen/Qwen2.5-7B-Instruct',
            'prompt' => "Write a high-quality {$contentType} about: {$prompt}\n\nMake it engaging, clear, and ready to publish.",
            'max_tokens'  => 300,
            'temperature' => 0.6,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(60)->post($apiUrl, $body);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => $response->json('error.message') ?? $response->json('message') ?? 'AI API failed. Try again.',
                ], $response->status() ?: 500);
            }

            $generatedText = trim($response->json('choices.0.text') ?? '');

            if (! $generatedText) {
                return response()->json([
                    'success' => false,
                    'message' => 'No content was generated.',
                ], 500);
            }

            return response()->json([
                'success'        => true,
                'message'        => 'Content generated successfully.',
                'generated_text' => $generatedText,
                'content_type'   => $contentType,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while generating content.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content_type'   => 'required|string',
            'prompt'         => 'required|string|max:500',
            'generated_text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $content = AIContent::create([
                'user_id'        => Auth::id(),
                'content_type'   => $request->content_type,
                'prompt'         => $request->prompt,
                'generated_text' => $request->generated_text,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Content saved successfully!',
                'data'    => $content,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save content.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $content = AIContent::where('user_id', auth()->id())->findOrFail($id);

        $content->update([
            'content_type'   => $request->content_type,
            'prompt'         => $request->prompt,
            'generated_text' => $request->generated_text,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Content updated successfully',
            'data'    => $content,
        ]);
    }

    public function list()
    {
        $contents = AIContent::where('user_id', Auth::id())->get();
        return view('admin.ai_contents_list', compact('contents'));
    }

    public function destroy($id)
    {
        $content = AIContent::where('user_id', auth()->id())->findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content deleted successfully',
        ]);
    }

    public function editorPage()
    {
        return view('admin.ai.editor');
    }

    public function processEditor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'type'    => 'required|in:grammar,tone,seo,rewrite',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $content = $request->content;
        $type    = $request->type;

        if ($type == 'grammar') {
            $prompt = "Fix grammar, spelling, and punctuation while preserving the original meaning:\n\n" . $content;
        } elseif ($type == 'tone') {
            $prompt = "Rewrite this content in a professional and polished tone while keeping the meaning intact:\n\n" . $content;
        } elseif ($type == 'seo') {
            $prompt = "Optimize this content for SEO while keeping it natural, readable, and engaging:\n\n" . $content;
        } else {
            $prompt = "Rewrite this content to make it clearer, smoother, and more engaging:\n\n" . $content;
        }

        try {
            $edited = $this->callAI($prompt, 500, 0.5);

            return response()->json([
                'success' => true,
                'message' => 'Content improved successfully.',
                'edited_content' => $edited,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
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
            'Content-Type'  => 'application/json',
        ])->timeout(60)->post($apiUrl, [
            'model'       => 'Qwen/Qwen2.5-7B-Instruct',
            'prompt'      => $prompt,
            'max_tokens'  => $maxTokens,
            'temperature' => $temperature,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                $response->json('error.message') ?? $response->json('message') ?? 'AI API failed. Try again.'
            );
        }

        $generatedText = trim($response->json('choices.0.text') ?? '');

        if (! $generatedText) {
            throw new \RuntimeException('No content was returned from the AI service.');
        }

        return $generatedText;
    }
}
