<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AITranslationController extends Controller
{
    public function translationPage()
    {
        return view('admin.ai.translation');
    }

    public function processTranslation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content'         => 'required|string',
            'target_language' => 'required|in:bangla,english,hindi,arabic,spanish',
            'source_language' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $content = $request->content;
        $targetLanguage = $request->target_language;
        $sourceLanguage = $request->source_language;

        $prompt = "Translate the following content into {$targetLanguage}. "
            . "Preserve the original meaning, tone, and formatting as much as possible. "
            . "Return only the translated text.\n\n";

        if ($sourceLanguage) {
            $prompt .= "Source language: {$sourceLanguage}\n\n";
        }

        $prompt .= "Content:\n{$content}";

        try {
            $translated = $this->callAI($prompt, 500, 0.3);

            $translation = Translation::create([
                'original_content'   => $content,
                'translated_content' => $translated,
                'source_language'    => $sourceLanguage,
                'target_language'    => $targetLanguage,
            ]);

            return response()->json([
                'success'            => true,
                'message'            => 'Content translated successfully.',
                'translated_content' => $translated,
                'data'               => $translation,
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
        $translations = Translation::latest()->get();
        return view('admin.translation_list', compact('translations'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'original_content'   => 'required|string',
            'translated_content' => 'required|string',
            'source_language'    => 'nullable|string|max:100',
            'target_language'    => 'required|in:bangla,english,hindi,arabic,spanish',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $translation = Translation::findOrFail($id);
        $translation->update([
            'original_content'   => $request->original_content,
            'translated_content' => $request->translated_content,
            'source_language'    => $request->source_language,
            'target_language'    => $request->target_language,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Translation updated successfully',
            'data'    => $translation,
        ]);
    }

    public function destroy($id)
    {
        $translation = Translation::findOrFail($id);
        $translation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Translation deleted successfully',
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

        $translatedText = trim($response->json('choices.0.text') ?? '');

        if (! $translatedText) {
            throw new \RuntimeException('No translated content was returned from the AI service.');
        }

        return $translatedText;
    }
}
