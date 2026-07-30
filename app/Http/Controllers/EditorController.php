<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

class EditorController extends Controller
{
    private const TEAMS = ['redteam', 'blueteam', 'automation'];

    /**
     * Show the editor page for creating or editing a note.
     */
    public function edit(Request $request): Response
    {
        $team = $request->input('team', 'redteam');
        $path = $request->input('path', '');

        if (!in_array($team, self::TEAMS)) {
            abort(404);
        }

        $content = '';
        $isNew = true;

        if ($path) {
            $filePath = $this->resolveFilePath($team, $path);
            if ($filePath && file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $isNew = false;
            }
        }

        return Inertia::render('EditorPage', [
            'team' => $team,
            'path' => $path,
            'content' => $content,
            'isNew' => $isNew,
            'teams' => self::TEAMS,
        ]);
    }

    /**
     * Save (create or update) a note.
     */
    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'team' => 'required|in:redteam,blueteam,automation',
            'path' => 'required|string|max:500',
            'content' => 'required|string',
        ]);

        $team = $validated['team'];
        $path = $validated['path'];
        $content = $validated['content'];

        // Ensure path ends with .md
        if (!Str::endsWith($path, '.md')) {
            $path .= '.md';
        }

        // Security: no traversal
        if (Str::contains($path, ['..', "\0"])) {
            return response()->json(['error' => 'Invalid path'], 400);
        }

        $basePath = Storage::disk('private')->path('md/' . $team);
        $filePath = $basePath . '/' . $path;

        // Ensure directory exists
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($filePath, $content);

        $urlPath = preg_replace('/\.md$/', '', $path);

        return response()->json([
            'success' => true,
            'url' => '/' . $team . '/' . $urlPath,
        ]);
    }

    /**
     * Delete a note.
     */
    public function delete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'team' => 'required|in:redteam,blueteam,automation',
            'path' => 'required|string',
        ]);

        $filePath = $this->resolveFilePath($validated['team'], $validated['path']);

        if (!$filePath || !file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        unlink($filePath);

        // Clean up empty directories
        $dir = dirname($filePath);
        $basePath = Storage::disk('private')->path('md/' . $validated['team']);
        while ($dir !== $basePath && is_dir($dir) && count(scandir($dir)) === 2) {
            rmdir($dir);
            $dir = dirname($dir);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Preview markdown as HTML.
     */
    public function preview(Request $request): JsonResponse
    {
        $content = $request->input('content', '');

        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new TaskListExtension());

        $converter = new MarkdownConverter($environment);
        $html = $converter->convert($content)->getContent();

        return response()->json(['html' => $html]);
    }

    private function resolveFilePath(string $team, string $path): ?string
    {
        // Security: no traversal
        if (Str::contains($path, ['..', "\0"])) {
            return null;
        }

        $basePath = Storage::disk('private')->path('md/' . $team);

        $filePath = $basePath . '/' . $path . '.md';
        if ($this->isWithinBasePath($filePath, $basePath) && file_exists($filePath)) return $filePath;

        $filePath = $basePath . '/' . $path;
        if ($this->isWithinBasePath($filePath, $basePath) && file_exists($filePath)) return $filePath;

        return null;
    }

    /**
     * Defense in depth: confirm the resolved real path is still inside the team's base path,
     * catching traversal attempts that survive the '..' string check (symlinks, encoded paths, etc.).
     */
    private function isWithinBasePath(string $filePath, string $basePath): bool
    {
        $realBase = realpath($basePath);
        $realFile = realpath($filePath);

        // File may not exist yet; if so, fall back to a normalized-path comparison.
        if ($realFile === false) {
            $normalized = str_replace('\\', '/', $filePath);
            return $realBase !== false && str_starts_with($normalized, str_replace('\\', '/', $realBase) . '/');
        }

        return $realBase !== false && str_starts_with($realFile, $realBase . DIRECTORY_SEPARATOR);
    }
}
