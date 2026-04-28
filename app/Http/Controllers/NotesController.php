<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

class NotesController extends Controller
{
    public function redteam(?string $path = null): Response
    {
        return $this->renderNotes('redteam', 'RedTeamPage', $path);
    }

    public function blueteam(?string $path = null): Response
    {
        return $this->renderNotes('blueteam', 'BlueTeamPage', $path);
    }

    private function renderNotes(string $team, string $page, ?string $path): Response
    {
        $storagePath = Storage::disk('private')->path('md/' . $team);

        // Ensure directory exists
        if (!is_dir($storagePath)) {
            @mkdir($storagePath, 0755, true);
        }

        // Build file tree
        $tree = $this->buildTree($storagePath);

        // Render markdown if a path is given
        $note = null;

        if ($path) {
            $note = $this->renderMarkdown($storagePath, $path);
        }

        return Inertia::render($page, [
            'tree' => $tree,
            'note' => $note,
            'team' => $team,
        ]);
    }

    private function renderMarkdown(string $storagePath, string $path): ?array
    {
        $filePath = $storagePath . '/' . $path . '.md';

        // Security: prevent directory traversal
        $realFile = realpath($filePath);
        $realBase = realpath($storagePath);

        if (!$realFile || !$realBase || !Str::startsWith($realFile, $realBase)) {
            abort(404);
        }

        if (!file_exists($filePath)) {
            abort(404);
        }

        $raw = file_get_contents($filePath);
        $content = $this->getConverter()->convert($raw)->getContent();

        return [
            'title' => pathinfo($filePath, PATHINFO_FILENAME),
            'content' => $content,
            'path' => $path,
        ];
    }

    private function getConverter(): MarkdownConverter
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new TaskListExtension());

        return new MarkdownConverter($environment);
    }

    private function buildTree(string $basePath, string $relativeTo = ''): array
    {
        $result = [];

        if (!is_dir($basePath)) {
            return $result;
        }

        $entries = scandir($basePath);

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..' || Str::startsWith($entry, '.')) {
                continue;
            }

            $fullPath = $basePath . DIRECTORY_SEPARATOR . $entry;
            $relative = $relativeTo ? $relativeTo . '/' . $entry : $entry;

            if (is_dir($fullPath)) {
                $children = $this->buildTree($fullPath, $relative);
                if (!empty($children)) {
                    $result[] = [
                        'name' => $entry,
                        'type' => 'folder',
                        'path' => $relative,
                        'children' => $children,
                    ];
                }
            } elseif (pathinfo($entry, PATHINFO_EXTENSION) === 'md') {
                $cleanRelative = preg_replace('/\.md$/', '', $relative);
                $result[] = [
                    'name' => pathinfo($entry, PATHINFO_FILENAME),
                    'type' => 'file',
                    'path' => $cleanRelative,
                ];
            }
        }

        usort($result, function ($a, $b) {
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'folder' ? -1 : 1;
            }
            return strcasecmp($a['name'], $b['name']);
        });

        return $result;
    }
}
