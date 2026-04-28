<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;

class NotesController extends Controller
{
    private function getConverter(): MarkdownConverter
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new \League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new TaskListExtension());

        return new MarkdownConverter($environment);
    }

    /**
     * Build a file tree from the storage directory.
     */
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
                $result[] = [
                    'name' => pathinfo($entry, PATHINFO_FILENAME),
                    'type' => 'file',
                    'path' => $relative,
                ];
            }
        }

        // Sort: folders first, then files, alphabetical
        usort($result, function ($a, $b) {
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'folder' ? -1 : 1;
            }
            return strcasecmp($a['name'], $b['name']);
        });

        return $result;
    }

    /**
     * Show the Red Team or Blue Team notes index / note page.
     */
    public function show(string $team, ?string $path = null): Response
    {
        $team = strtolower($team);

        if (!in_array($team, ['redteam', 'blueteam'])) {
            abort(404);
        }

        $storagePath = Storage::disk('private')->path('md/' . $team);

        // Build file tree
        $tree = $this->buildTree($storagePath);

        // Render markdown if a path is given
        $content = null;
        $title = null;

        if ($path) {
            $filePath = $storagePath . '/' . $path;

            // Security: prevent directory traversal
            if (!Str::startsWith(realpath($filePath) ?: '', realpath($storagePath))) {
                abort(404);
            }

            if (!file_exists($filePath)) {
                // Try appending .md
                $filePath .= '.md';
            }

            if (file_exists($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'md') {
                $raw = file_get_contents($filePath);
                $content = $this->getConverter()->convert($raw)->getContent();
                $title = pathinfo($filePath, PATHINFO_FILENAME);
            } else {
                abort(404);
            }
        }

        $pageName = $team === 'redteam' ? 'RedTeamPage' : 'BlueTeamPage';

        return Inertia::render($pageName, [
            'tree' => $tree,
            'note' => $content ? [
                'title' => $title,
                'content' => $content,
                'path' => $path,
            ] : null,
            'team' => $team,
        ]);
    }
}
