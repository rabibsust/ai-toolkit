<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;

class AiAnalyzerService
{
    public function __construct()
    {
        //
    }

    public function analyzeCode(string $code): array
    {
        $prompt = "Analyze this PHP/Laravel code and suggest improvements:\n\n" . $code;

        $result = Gemini::generativeModel('gemini-2.0-flash')
            ->generateContent($prompt);

        return [
            'analysis' => $result->text(),
            'suggestions' => $this->parseSuggestions($result->text())
        ];
    }

    private function parseSuggestions(string $analysis): array
    {
        // We'll implement this logic tomorrow
        return [];
    }
}
