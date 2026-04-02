<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ContentEdit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AIContentEditController extends Controller
{
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

            $contentEdit = ContentEdit::create([
                'original_content' => $content,
                'edited_content'   => $edited,
                'edit_type'        => $type,
                'user_id'          => Auth::id(),
            ]);

            return response()->json([
                'data'           => $contentEdit,
                'success'        => true,
                'message'        => 'Content improved successfully.',
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

    public function list()
    {
        $contentEdits = ContentEdit::where('user_id', Auth::id())->latest()->get();
        return view('admin.ai_edit_contents_list', compact('contentEdits'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'original_content' => 'required|string',
            'edited_content'   => 'required|string',
            'edit_type'        => 'required|in:grammar,tone,seo,rewrite',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $contentEdit = ContentEdit::where('user_id', Auth::id())->findOrFail($id);

        $contentEdit->update([
            'original_content' => $request->original_content,
            'edited_content'   => $request->edited_content,
            'edit_type'        => $request->edit_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Edited content updated successfully',
            'data'    => $contentEdit,
        ]);
    }

    public function destroy($id)
    {
        $contentEdit = ContentEdit::where('user_id', Auth::id())->findOrFail($id);
        $contentEdit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Edited content deleted successfully',
        ]);
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
