<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIContentController extends Controller
{
    public function index()
    {
        return view('admin.ai_content');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'content_type' => 'required|string',
            'prompt'       => 'required|string|max:500',
        ]);

        $content_type = $request->content_type;
        $prompt       = $request->prompt;

        $apiUrl = 'https://router.huggingface.co/featherless-ai/v1/completions';
        $apiKey = env('HF_API_KEY');

        $body = [
            'model'  => 'Qwen/Qwen2.5-7B-Instruct',
            'prompt' => "Write a high-quality {$content_type} about: {$prompt}\n\nMake it engaging, clear, and ready to publish.",
            'max_tokens'  => 300,
            'temperature' => 0.6,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->post($apiUrl, $body);

        if ($response->failed()) {
            $errorMessage = $response->json('error.message') ?? 'AI API failed. Try again.';
            return back()->withInput()->with('error', $errorMessage);
        }

        $result = $response->json();

        $generated_text = trim($result['choices'][0]['text'] ?? '');

        if ($generated_text === '') {
            $generated_text = 'No content generated.';
        }

        return back()->withInput()->with([
            'generated_text' => $generated_text,
            'content_type'   => $content_type,
        ]);
    }

}
