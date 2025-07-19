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
        $lines = explode("\n", $text);
        $currentPoint = '';
        $inCodeBlock = false;
        $collectingPoint = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            // Check if this is a new bullet point
            if (str_starts_with($trimmedLine, '* ') || str_starts_with($trimmedLine, '- ')) {
                // Save previous point if exists
                if (!empty($currentPoint) && $collectingPoint) {
                    $points[] = trim($currentPoint);
                }

                // Start new point
                $currentPoint = $this->cleanBulletPoint($trimmedLine);
                $collectingPoint = true;
                $inCodeBlock = false;
            }
            // If we're collecting a point, add this line to it
            elseif ($collectingPoint) {
                // Check for code block markers
                if (str_contains($trimmedLine, '```')) {
                    $inCodeBlock = !$inCodeBlock;
                    $currentPoint .= "\n" . $line; // Keep original indentation for code
                }
                // If we're in a code block or this line has content, add it
                elseif ($inCodeBlock || !empty($trimmedLine)) {
                    $currentPoint .= "\n" . $line; // Keep original indentation
                }
                // Empty line - add it if we're in a code block
                elseif ($inCodeBlock) {
                    $currentPoint .= "\n" . $line;
                }
            }
        }

        // Don't forget the last point
        if (!empty($currentPoint) && $collectingPoint) {
            $points[] = trim($currentPoint);
        }

        return array_filter($points); // Remove any empty points
    }

    private function cleanBulletPoint(string $line): string
    {
        // Remove bullet point markers and clean up formatting
        $cleanLine = preg_replace('/^\*\s*\*\*(.*?)\*\*:?\s*/', '$1: ', $line);
        $cleanLine = preg_replace('/^-\s*\*\*(.*?)\*\*:?\s*/', '$1: ', $cleanLine);
        $cleanLine = preg_replace('/^\*\s*/', '', $cleanLine);
        $cleanLine = preg_replace('/^-\s*/', '', $cleanLine);

        return trim($cleanLine);
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
