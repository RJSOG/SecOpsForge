<?php

namespace App\Http\Controllers;

use App\Enum\TeamEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

class NotesController extends Controller
{
    /**
     * @param int $perSection
     * @return array
     */
    public static function getLatestNotes(int $perSection = 3): array
    {
        $latest = [];

        foreach (TeamEnum::getAllValues() as $team) {
            $basePath = Storage::disk('notes')->path($team);

            if (!is_dir($basePath)) continue;

            $files = [];
            self::collectFiles($basePath, $basePath, $team, $files);

            usort($files, fn($a, $b) => $b['mtime'] - $a['mtime']);

            $latest[$team] = array_slice($files, 0, $perSection);
        }

        return $latest;
    }

    /**
     * @param string $basePath
     * @param string $dir
     * @param string $team
     * @param array $files
     * @return void
     */
    private static function collectFiles(string $basePath, string $dir, string $team, array &$files): void
    {
        foreach (scandir($dir) as $entry) {
            if ($entry === '.' || $entry === '..' || str_starts_with($entry, '.')) continue;

            $fullPath = $dir . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($fullPath)) {
                self::collectFiles($basePath, $fullPath, $team, $files);
            } elseif (pathinfo($entry, PATHINFO_EXTENSION) === 'md') {
                $relative = substr($fullPath, strlen($basePath) + 1);
                $cleanPath = preg_replace('/\.md$/', '', $relative);
                $files[] = [
                    'title' => pathinfo($entry, PATHINFO_FILENAME),
                    'path' => $cleanPath,
                    'url' => '/' . $team . '/' . $cleanPath,
                    'team' => $team,
                    'mtime' => filemtime($fullPath),
                    'date' => date('d M Y', filemtime($fullPath)),
                ];
            }
        }
    }

    /**
     * @param string|null $path
     * @return Response
     * @throws CommonMarkException
     */
    public function redteam(?string $path = null): Response
    {
        return $this->renderNotes(TeamEnum::RED_TEAM->value, 'RedTeamPage', $path);
    }

    /**
     * @param string|null $path
     * @return Response
     * @throws CommonMarkException
     */
    public function blueteam(?string $path = null): Response
    {
        return $this->renderNotes(TeamEnum::BLUE_TEAM->value, 'BlueTeamPage', $path);
    }

    /**
     * @param string|null $path
     * @return Response
     * @throws CommonMarkException
     */
    public function automation(?string $path = null): Response
    {
        return $this->renderNotes(TeamEnum::AUTOMATION->value, 'AutomationPage', $path);
    }

    /**
     * @param string $team
     * @return string
     */
    private function getStoragePath(string $team): string
    {
        return Storage::disk('notes')->path($team);
    }

    /**
     * @param string $team
     * @param string $page
     * @param string|null $path
     * @return Response
     * @throws CommonMarkException
     */
    private function renderNotes(string $team, string $page, ?string $path): Response
    {
        $storagePath = $this->getStoragePath($team);

        if (!is_dir($storagePath)) {
            @mkdir($storagePath, 0755, true);
        }

        $tree = $this->buildTree($storagePath);

        Log::debug("NotesController: team={$team}, storagePath={$storagePath}, path={$path}, treeCount=" . count($tree));

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

    /**
     * @param string $storagePath
     * @param string $path
     * @return array|null
     * @throws CommonMarkException
     */
    private function renderMarkdown(string $storagePath, string $path): ?array
    {
        $filePath = $storagePath . '/' . $path . '.md';

        if (!file_exists($filePath)) {
            $filePath = $storagePath . '/' . $path;
        }

        Log::debug("NotesController: trying to load file at {$filePath}, exists=" . (file_exists($filePath) ? 'yes' : 'no'));

        if (!file_exists($filePath)) {
            abort(404);
        }

        $realFile = realpath($filePath);
        $realBase = realpath($storagePath);

        if (!$realFile || !$realBase || !Str::startsWith($realFile, $realBase . DIRECTORY_SEPARATOR)) {
            abort(404);
        }

        $raw = file_get_contents($filePath);
        $content = $this->getConverter()->convert($raw)->getContent();

        return [
            'title' => Str::headline(pathinfo($filePath, PATHINFO_FILENAME)),
            'content' => $content,
            'path' => $path,
        ];
    }

    /**
     * @return MarkdownConverter
     */
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

    /**
     * @param string $basePath
     * @param string $relativeTo
     * @return array
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
