<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    private const TEAMS = ['redteam', 'blueteam', 'automation'];

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        foreach (self::TEAMS as $team) {
            $basePath = Storage::disk('private')->path('md/' . $team);

            if (!is_dir($basePath)) continue;

            $this->searchDir($basePath, $basePath, $team, $query, $results);
        }

        // Sort by relevance (title matches first)
        usort($results, fn($a, $b) => $b['score'] - $a['score']);

        return response()->json([
            'results' => array_slice($results, 0, 20),
        ]);
    }

    private function searchDir(string $basePath, string $dir, string $team, string $query, array &$results): void
    {
        if (!is_dir($dir)) return;

        foreach (scandir($dir) as $entry) {
            if ($entry === '.' || $entry === '..' || Str::startsWith($entry, '.')) continue;

            $fullPath = $dir . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($fullPath)) {
                $this->searchDir($basePath, $fullPath, $team, $query, $results);
            } elseif (pathinfo($entry, PATHINFO_EXTENSION) === 'md') {
                $this->searchFile($basePath, $fullPath, $team, $query, $results);
            }
        }
    }

    private function searchFile(string $basePath, string $filePath, string $team, string $query, array &$results): void
    {
        $content = file_get_contents($filePath);
        $filename = pathinfo($filePath, PATHINFO_FILENAME);
        $relativePath = Str::after($filePath, $basePath . DIRECTORY_SEPARATOR);
        $relativePath = preg_replace('/\.md$/', '', $relativePath);

        $queryLower = mb_strtolower($query);
        $contentLower = mb_strtolower($content);
        $filenameLower = mb_strtolower($filename);

        $score = 0;

        // Title match = high score
        if (str_contains($filenameLower, $queryLower)) {
            $score += 10;
        }

        // Content match
        $contentMatches = substr_count($contentLower, $queryLower);
        if ($contentMatches > 0) {
            $score += min($contentMatches, 5);
        }

        if ($score === 0) return;

        // Extract snippet around first match
        $snippet = '';
        $pos = mb_stripos($content, $query);
        if ($pos !== false) {
            $start = max(0, $pos - 60);
            $end = min(mb_strlen($content), $pos + strlen($query) + 60);
            $snippet = ($start > 0 ? '...' : '') . mb_substr($content, $start, $end - $start) . ($end < mb_strlen($content) ? '...' : '');
            $snippet = trim(preg_replace('/\s+/', ' ', $snippet));
        }

        $results[] = [
            'title' => $filename,
            'team' => $team,
            'path' => $relativePath,
            'url' => '/' . $team . '/' . $relativePath,
            'snippet' => $snippet,
            'score' => $score,
        ];
    }
}
