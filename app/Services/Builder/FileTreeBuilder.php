<?php

namespace App\Services\Builder;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FileTreeBuilder extends DataBuilder
{
    const AVAILABLE_FORMATS = ['md'];

    /**
     * @var string
     */
    protected string $root;

    /**
     * @var string
     */
    protected string $current;

    /**
     * @var string
     */
    protected string $format;

    /**
     * @var array
     */
    protected array $tree;

    /**
     * @return $this
     *
     * @throws InvalidArgumentException if 'root' resolves outside the format's base directory.
     */
    public function load(): static
    {
        $this->format = $this->input['format'];
        $this->current = $this->input['root'] ?? '';

        $baseDir = rtrim(Storage::disk('private')->path($this->format), DIRECTORY_SEPARATOR);
        $requestedDir = rtrim(Storage::disk('private')->path($this->format . '/' . $this->current), DIRECTORY_SEPARATOR);

        // Security: canonicalize both paths and verify the requested root is
        // the base directory itself or a real descendant of it. realpath()
        // also collapses '..' segments, so this closes the traversal that a
        // raw string concatenation would otherwise allow.
        $realBase = realpath($baseDir);
        $realRequested = realpath($requestedDir);

        if (
            !$realBase
            || !$realRequested
            || ($realRequested !== $realBase && !Str::startsWith($realRequested, $realBase . DIRECTORY_SEPARATOR))
        ) {
            throw new InvalidArgumentException('Invalid root path.');
        }

        $this->root = $realRequested;

        return $this;
    }

    /**
     * @return $this
     */
    public function build(): static
    {
        $this->tree = $this->recursiveReader(
            $this->root,
            $this->root
        );

        return $this;
    }

    /**
     * @return static
     */
    public function terminate(): static
    {
        $this->output = [
            'root' => $this->input['root'],
            'tree' => $this->tree,
            'hash' => md5(json_encode($this->tree)),
        ];
        return $this;
    }

    /**
     * @param string $root
     * @param string $current
     * @param bool $isParent
     * @return array
     */
    protected function recursiveReader(string $root, string $current, bool $isParent = true): array
    {
        $result = [];

        if(!is_dir($current)) return $result;

        $entries = scandir($current);

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $fullPath = $root . DIRECTORY_SEPARATOR . $entry;
            $relativePath = str_replace(storage_path('app' . DIRECTORY_SEPARATOR . 'private'), '', $fullPath);

            if (is_dir($fullPath)) {
                $result[] = [
                    'name' => $entry,
                    'type' => 'folder',
                    'path' => $relativePath,
                    'children' => $this->recursiveReader($fullPath, $fullPath, false),
                    'isParent' => $isParent,
                ];
            } else if (is_file($fullPath) && pathinfo($entry, PATHINFO_EXTENSION) === $this->format) {
                $result[] = [
                    'name' => pathinfo($entry, PATHINFO_FILENAME),
                    'type' => 'file',
                    'path' => $relativePath,
                ];
            }
        }

        return $result;
    }
}
