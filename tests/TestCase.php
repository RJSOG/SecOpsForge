<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    /**
     * Temporary notes folder used instead of ./content during tests.
     */
    protected string $notesPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->notesPath = sys_get_temp_dir() . '/secopsforge-notes-' . uniqid();
        File::makeDirectory($this->notesPath, 0755, true);
        config(['filesystems.disks.notes.root' => $this->notesPath]);
        Storage::forgetDisk('notes');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->notesPath);

        parent::tearDown();
    }

    /**
     * Write a Markdown note at "<team>/<path>.md" in the test notes folder.
     */
    protected function makeNote(string $team, string $path, string $content): void
    {
        $file = "{$this->notesPath}/{$team}/{$path}.md";
        File::ensureDirectoryExists(dirname($file));
        File::put($file, $content);
    }
}
