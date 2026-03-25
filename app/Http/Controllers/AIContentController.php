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
}
