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
        try {
            $prompt = $this->buildTranslationPrompt($content, $targetLanguage, $sourceLanguage);
            $translated = $this->callAI($prompt, 650, 0.05);
            $translated = $this->cleanTranslationOutput($translated, $targetLanguage);

            if ($this->containsUnexpectedScript($translated, $targetLanguage)) {
                $retryPrompt = $this->buildTranslationPrompt($content, $targetLanguage, $sourceLanguage, true);
                $translated = $this->callAI($retryPrompt, 650, 0.0);
                $translated = $this->cleanTranslationOutput($translated, $targetLanguage);
            }

            if ($this->containsUnexpectedScript($translated, $targetLanguage)) {
                throw new \RuntimeException('Translation output contained text outside the selected language. Please try again.');
            }

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

    private function getTargetLanguageLabel(string $targetLanguage): string
    {
        return match ($targetLanguage) {
            'bangla' => 'Bangla written in Bengali script',
            'english' => 'English',
            'hindi' => 'Hindi written in Devanagari script',
            'arabic' => 'Arabic written in Arabic script',
            'spanish' => 'Spanish',
            default => ucfirst($targetLanguage),
        };
    }

    private function buildTranslationPrompt(
        string $content,
        string $targetLanguage,
        ?string $sourceLanguage,
        bool $strict = false
    ): string {
        $targetLanguageLabel = $this->getTargetLanguageLabel($targetLanguage);

        $prompt = "You are a professional translator.\n"
            . "Translate the user's content into {$targetLanguageLabel}.\n"
            . "Rules:\n"
            . "1. Return only the final translated text.\n"
            . "2. Do not add explanations, notes, headings, labels, or commentary.\n"
            . "3. Do not include the source text.\n"
            . "4. Use only {$targetLanguageLabel} in the answer.\n"
            . "5. Do not mix any other language into the final output.\n"
            . "6. Preserve dates, names, and formatting where appropriate.\n"
            . "7. Keep the translation natural, fluent, and publication-ready.\n";

        if ($strict) {
            $prompt .= "8. The response must be written only in the target language/script.\n"
                . "9. If any word cannot be translated, transliterate it into the target script when appropriate.\n"
                . "10. Never explain the translation.\n";
        }

        if ($sourceLanguage) {
            $prompt .= "\nSource language: {$sourceLanguage}\n";
        }

        $prompt .= "\nText to translate:\n\"\"\"\n{$content}\n\"\"\"";

        return $prompt;
    }

    private function cleanTranslationOutput(string $text, string $targetLanguage): string
    {
        $cleaned = trim($text);

        $patterns = [
            '/^translated text in .*?:\s*/iu',
            '/^translation:\s*/iu',
            '/^translated content:\s*/iu',
            '/^here is the translated.*?:\s*/iu',
            '/^please note.*$/imu',
            '/^以下是.*$/imu',
            '/^翻译结果[:：]?.*$/imu',
            '/^translated text[:：]?.*$/imu',
        ];

        $cleaned = preg_replace($patterns, '', $cleaned);
        $cleaned = trim($cleaned);

        $lines = preg_split('/\R/u', $cleaned);
        $filteredLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            if (! $this->containsUnexpectedScript($trimmed, $targetLanguage)) {
                $filteredLines[] = $trimmed;
            }
        }

        if (!empty($filteredLines)) {
            $cleaned = implode("\n", $filteredLines);
        }

        return trim($cleaned);
    }

    private function containsUnexpectedScript(string $text, string $targetLanguage): bool
    {
        if (preg_match('/[\x{4E00}-\x{9FFF}]/u', $text)) {
            return true;
        }

        $pattern = match ($targetLanguage) {
            'bangla' => '/[^\s\d\p{P}\p{S}\x{0980}-\x{09FF}]/u',
            'hindi' => '/[^\s\d\p{P}\p{S}\x{0900}-\x{097F}]/u',
            'arabic' => '/[^\s\d\p{P}\p{S}\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}]/u',
            'english', 'spanish' => '/[^\s\d\p{P}\p{S}A-Za-zÀ-ÿ]/u',
            default => null,
        };

        return $pattern ? preg_match($pattern, $text) === 1 : false;
    }
}
