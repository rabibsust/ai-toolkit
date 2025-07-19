<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;

class AiAnalyzerService
{
    public function __construct()
    {
        //
    }

    public function analyzeController(string $code): array
    {
        $prompt = $this->buildControllerAnalysisPrompt($code);

        try {
            $result = Gemini::generativeModel('gemini-2.0-flash')
                ->generateContent($prompt);

            $analysisText = $result->text();

            return [
                'status' => 'success',
                'analysis' => $analysisText,
                'suggestions' => $this->extractSuggestions($analysisText),
                'score' => $this->calculateScore($analysisText)
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Builds the prompt for controller analysis.
     *
     * @param string $code
     * @return string
     */
    private function buildControllerAnalysisPrompt(string $code): string
    {
        return "
            You are an expert Laravel developer. Analyze this Laravel controller code and provide detailed feedback.

            CONTROLLER CODE:
            ```php
            {$code}
            Please provide analysis in this exact format:
            CODE QUALITY SCORE: [1-10]
            ISSUES FOUND:

            [List specific issues]

            SUGGESTIONS:

            [Specific improvements with code examples]

            BEST PRACTICES:

            [Laravel best practices to follow]

            SECURITY CONCERNS:

            [Any security issues found]

            Keep responses practical and actionable.```";
    }

    /**
     * Extracts suggestions from analysis text.
     *
     * @param string $analysis
     * @return array
     */
    private function extractSuggestions(string $analysis): array
    {
        $suggestions = [];

        // Extract from SUGGESTIONS section
        if (preg_match('/SUGGESTIONS:(.*?)(?=BEST PRACTICES:|ISSUES FOUND:|SECURITY CONCERNS:|$)/s', $analysis, $matches)) {
            $suggestionsText = $matches[1];
            $suggestions = array_merge($suggestions, $this->parseBulletPoints($suggestionsText));
        }

        // Extract from BEST PRACTICES section
        if (preg_match('/BEST PRACTICES:(.*?)(?=SUGGESTIONS:|ISSUES FOUND:|SECURITY CONCERNS:|$)/s', $analysis, $matches)) {
            $practicesText = $matches[1];
            $bestPractices = $this->parseBulletPoints($practicesText);
            $suggestions = array_merge($suggestions, $bestPractices);
        }

        // Fallback if nothing found
        if (empty($suggestions)) {
            $suggestions = [
                'Consider adding proper validation to your methods',
                'Implement proper error handling and response codes',
                'Follow Laravel naming conventions and best practices'
            ];
        }

        return $suggestions;
    }

    private function parseBulletPoints(string $text): array
    {
        $points = [];
        $lines = explode("\n", trim($text));

        foreach ($lines as $line) {
            $line = trim($line);

            // Handle both * and - bullet points
            if (str_starts_with($line, '* ') || str_starts_with($line, '- ')) {
                // Remove the bullet and **bold** formatting
                $cleanLine = preg_replace('/^\*\s*\*\*(.*?)\*\*:?\s*/', '$1: ', $line);
                $cleanLine = preg_replace('/^-\s*\*\*(.*?)\*\*:?\s*/', '$1: ', $cleanLine);
                $cleanLine = preg_replace('/^\*\s*/', '', $cleanLine);
                $cleanLine = preg_replace('/^-\s*/', '', $cleanLine);

                // Remove any remaining ** formatting
                $cleanLine = str_replace('**', '', $cleanLine);

                // Clean up extra spaces
                $cleanLine = trim($cleanLine);

                if (!empty($cleanLine)) {
                    $points[] = $cleanLine;
                }
            }
        }

        return $points;
    }

    /**
     * Calculates a score based on the analysis.
     *
     * @param string $analysis
     * @return int
     */
    private function calculateScore(string $analysis): int
    {
        preg_match('/CODE QUALITY SCORE:\s*(\d+)/', $analysis, $matches);
        return isset($matches[1]) ? (int)$matches[1] : 7;
    }
}
