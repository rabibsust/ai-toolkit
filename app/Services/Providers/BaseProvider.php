<?php

namespace App\Services\Providers;

use App\Contracts\LLMProviderInterface;

abstract class BaseProvider implements LLMProviderInterface
{
    // Shared analysis parsing methods
    protected function extractSuggestions(string $analysis): array
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

        return empty($suggestions) ? $this->getDefaultSuggestions() : $suggestions;
    }

    protected function parseBulletPoints(string $text): array
    {
        $points = [];
        $lines = explode("\n", $text);
        $currentPoint = '';
        $inCodeBlock = false;
        $collectingPoint = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            if (str_starts_with($trimmedLine, '* ') || str_starts_with($trimmedLine, '- ')) {
                if (!empty($currentPoint) && $collectingPoint) {
                    $points[] = trim($currentPoint);
                }

                $currentPoint = $this->cleanBulletPoint($trimmedLine);
                $collectingPoint = true;
                $inCodeBlock = false;
            }
            elseif ($collectingPoint) {
                if (str_contains($trimmedLine, '```')) {
                    $inCodeBlock = !$inCodeBlock;
                    $currentPoint .= "\n" . $line;
                }
                elseif ($inCodeBlock || !empty($trimmedLine)) {
                    $currentPoint .= "\n" . $line;
                }
                elseif ($inCodeBlock) {
                    $currentPoint .= "\n" . $line;
                }
            }
        }

        if (!empty($currentPoint) && $collectingPoint) {
            $points[] = trim($currentPoint);
        }

        return array_filter($points);
    }

    protected function cleanBulletPoint(string $line): string
    {
        $cleanLine = preg_replace('/^\*\s*\*\*(.*?)\*\*:?\s*/', '$1: ', $line);
        $cleanLine = preg_replace('/^-\s*\*\*(.*?)\*\*:?\s*/', '$1: ', $cleanLine);
        $cleanLine = preg_replace('/^\*\s*/', '', $cleanLine);
        $cleanLine = preg_replace('/^-\s*/', '', $cleanLine);

        return trim($cleanLine);
    }

    protected function calculateScore(string $analysis): int
    {
        if (preg_match('/CODE QUALITY SCORE:\s*(\d+)/', $analysis, $matches)) {
            return (int)$matches[1];
        }

        // Fallback scoring based on keywords
        $lowercaseAnalysis = strtolower($analysis);
        $score = 7; // Default score

        if (strpos($lowercaseAnalysis, 'sql injection') !== false) $score -= 2;
        if (strpos($lowercaseAnalysis, 'mass assignment') !== false) $score -= 1;
        if (strpos($lowercaseAnalysis, 'security') !== false) $score -= 1;
        if (strpos($lowercaseAnalysis, 'validation') !== false) $score -= 1;

        if (strpos($lowercaseAnalysis, 'eloquent') !== false) $score += 1;
        if (strpos($lowercaseAnalysis, 'middleware') !== false) $score += 1;

        return max(1, min(10, $score));
    }

    protected function buildStandardPrompt(string $code, array $options = []): string
    {
        $focusArea = $options['focus'] ?? 'general';
        $detail = $options['detail'] ?? 'standard';

        return "You are an expert full-stack developer with expertise in PHP/Laravel, JavaScript, React, Vue.js, Node.js, React Native, and modern web development.
            **INSTRUCTIONS:**
            1. First, automatically detect the programming language/framework from the code
            2. Apply language-specific best practices and analysis
            3. Focus on: {$focusArea}
            4. Detail level: {$detail}

            **CODE TO ANALYZE:**
            {$code}

            **ANALYSIS FORMAT:**

            ## DETECTED LANGUAGE/FRAMEWORK: [Auto-detected language/framework]

            ## CODE QUALITY SCORE: [1-10]

            ## SUGGESTIONS:
            - [Language/framework-specific improvements]
            - [Performance optimizations]
            - [Modern patterns and best practices]

            ## ISSUES FOUND:
            - [Language-specific anti-patterns]
            - [Logic and syntax problems]
            - [Framework-specific issues]

            ## BEST PRACTICES:
            - [Language/framework conventions]
            - [Modern development patterns]
            - [Code organization improvements]

            ## SECURITY CONCERNS:
            - [Language-specific vulnerabilities]
            - [Framework security issues]
            - [Input validation and sanitization]

            **LANGUAGE-SPECIFIC GUIDELINES:**

            **For PHP/Laravel:**
            - Focus on Laravel conventions, Eloquent best practices, security (SQL injection, mass assignment)
            - Route optimization, middleware usage, validation patterns

            **For React:**
            - Hook optimization, component patterns, performance (re-renders, memoization)
            - Props validation, accessibility, modern React patterns

            **For Vue.js:**
            - Composition API vs Options API, reactivity patterns, template optimization
            - Component communication, state management, Vue 3 features

            **For Node.js:**
            - Async/await patterns, error handling, Express.js best practices
            - Package security, environment configuration, performance

            **For React Native:**
            - Mobile-specific patterns, platform differences, performance optimization
            - Navigation patterns, state management, native module integration

            **For JavaScript (general):**
            - Modern ES6+ features, browser compatibility, performance
            - Code organization, module patterns, error handling

            Provide specific, actionable suggestions with code examples when helpful.";
    }

    protected function getDefaultSuggestions(): array
    {
        return [
            'Consider adding proper validation to your methods',
            'Implement proper error handling and response codes',
            'Follow Laravel naming conventions and best practices'
        ];
    }

    // Abstract methods that each provider must implement
    // abstract public function getName(): string;
    // abstract public function isAvailable(): bool;
    // abstract public function getAvailableModels(): array;
    // abstract public function analyzeCode(string $code, array $options = []): array;
    // abstract public function getUsageStats(): array;
    // abstract public function getCostPerRequest(): float;
    // abstract public function getMaxTokens(): int;
    // abstract public function supportsStreaming(): bool;
}
