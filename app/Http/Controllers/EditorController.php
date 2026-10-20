<?php

namespace App\Http\Controllers;

use App\Enum\TeamEnum;
use App\Support\Seo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

class EditorController extends Controller
{
    public function edit(Request $request): Response
    {
        $team = $request->input('team', TeamEnum::RED_TEAM->value);
        $path = $request->input('path', '');

        if (! is_string($team) || ! TeamEnum::has($team)) {
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

        $seo = Seo::make(title: 'Editor', index: false);

        return Inertia::render('EditorPage', [
            'team' => $team,
            'path' => $path,
            'content' => $content,
            'isNew' => $isNew,
            'teams' => TeamEnum::getAllValues(),
            'seo' => $seo,
        ])->withViewData(['seo' => $seo]);
    }

    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'team' => ['required', Rule::enum(TeamEnum::class)],
            'path' => 'required|string|max:500',
            'content' => 'required|string',
        ]);

        $team = $validated['team'];
        $path = $validated['path'];
        $content = $validated['content'];

        if (! Str::endsWith($path, '.md')) {
            $path .= '.md';
        }

        if (Str::contains($path, ['..', "\0"])) {
            return response()->json(['error' => 'Invalid path'], 400);
        }

        $basePath = Storage::disk('notes')->path($team);
        $filePath = $basePath.'/'.$path;

        $dir = dirname($filePath);
        $written = (is_dir($dir) || @mkdir($dir, 0755, true)) && @file_put_contents($filePath, $content) !== false;

        if (! $written) {
            // Usually the mounted notes folder is owned by another user than
            // the one PHP runs as (see DEPLOY.md, "Droits sur les articles").
            Log::error('Editor: cannot write note', ['file' => $filePath, 'error' => error_get_last()['message'] ?? null]);

            return response()->json([
                'error' => "Impossible d'écrire la note : le dossier des articles n'est pas accessible en écriture par le serveur.",
            ], 500);
        }

        $urlPath = preg_replace('/\.md$/', '', $path);

        return response()->json([
            'success' => true,
            'url' => '/'.$team.'/'.$urlPath,
        ]);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'team' => ['required', Rule::enum(TeamEnum::class)],
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ]);

        $team = $validated['team'];
        $file = $request->file('image');

        // Extension guessed from the file content (constrained by the mimes
        // rule), never the client-supplied one: a PNG named x.html would
        // otherwise be served as HTML from /storage.
        $extension = strtolower($file->extension() ?: 'png');
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($name) ?: 'image';
        $filename = $slug.'-'.Str::random(8).'.'.$extension;

        $path = $file->storeAs('articles/'.$team, $filename, 'public');
        $url = '/storage/'.$path;

        return response()->json([
            'success' => true,
            'url' => $url,
            'alt' => $slug,
            'markdown' => '!['.$slug.']('.$url.')',
        ]);
    }

    public function delete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'team' => ['required', Rule::enum(TeamEnum::class)],
            'path' => 'required|string',
        ]);

        $filePath = $this->resolveFilePath($validated['team'], $validated['path']);

        if (! $filePath || ! file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        unlink($filePath);

        $dir = dirname($filePath);
        $basePath = Storage::disk('notes')->path($validated['team']);
        while ($dir !== $basePath && is_dir($dir) && count(scandir($dir)) === 2) {
            rmdir($dir);
            $dir = dirname($dir);
        }

        return response()->json(['success' => true]);
    }

    /**
     * @throws CommonMarkException
     */
    public function preview(Request $request): JsonResponse
    {
        $content = $request->input('content', '');

        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new TableExtension);
        $environment->addExtension(new TaskListExtension);

        $converter = new MarkdownConverter($environment);
        $html = $converter->convert($content)->getContent();

        return response()->json(['html' => $html]);
    }

    private function resolveFilePath(string $team, string $path): ?string
    {
        if (Str::contains($path, ['..', "\0"])) {
            return null;
        }

        $basePath = Storage::disk('notes')->path($team);

        $filePath = $basePath.'/'.$path.'.md';
        if ($this->isWithinBasePath($filePath, $basePath) && file_exists($filePath)) {
            return $filePath;
        }

        $filePath = $basePath.'/'.$path;
        if ($this->isWithinBasePath($filePath, $basePath) && file_exists($filePath)) {
            return $filePath;
        }

        return null;
    }

    private function isWithinBasePath(string $filePath, string $basePath): bool
    {
        $realBase = realpath($basePath);
        $realFile = realpath($filePath);

        if ($realFile === false) {
            $normalized = str_replace('\\', '/', $filePath);

            return $realBase !== false && str_starts_with(
                $normalized,
                str_replace('\\', '/', $realBase).'/'
            );
        }

        return $realBase !== false && str_starts_with($realFile, $realBase.DIRECTORY_SEPARATOR);
    }
}
