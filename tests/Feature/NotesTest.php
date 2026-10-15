<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NotesTest extends TestCase
{
    public function test_home_lists_latest_notes_per_section(): void
    {
        $this->makeNote('redteam', 'ad/Kerberoasting', '# Kerberoasting');
        $this->makeNote('blueteam', 'Sigma', '# Sigma');

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('HomePage')
                ->where('latest.redteam.0.url', '/redteam/ad/Kerberoasting')
                ->where('latest.blueteam.0.url', '/blueteam/Sigma')
                ->missing('latest.automation'));
    }

    public function test_section_shows_the_tree_without_a_note(): void
    {
        $this->makeNote('redteam', 'ad/Kerberoasting', '# Kerberoasting');

        $this->get('/redteam')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('RedTeamPage')
                ->where('note', null)
                ->where('tree.0.name', 'ad')
                ->where('tree.0.children.0.path', 'ad/Kerberoasting'));
    }

    public function test_note_is_rendered_to_html(): void
    {
        $this->makeNote('blueteam', 'detection/Sigma', "# Sigma\n\n| a | b |\n|---|---|\n| 1 | 2 |");

        $this->get('/blueteam/detection/Sigma')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('BlueTeamPage')
                ->where('note.title', 'Sigma')
                ->where('note.content', fn (string $html) => str_contains($html, '<h1>Sigma</h1>')
                    && str_contains($html, '<table>')));
    }

    public function test_raw_html_and_javascript_links_are_stripped(): void
    {
        $this->makeNote('automation', 'xss', "<script>alert(1)</script>\n\n[click](javascript:alert(1))");

        $this->get('/automation/xss')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('note.content', fn (string $html) => !str_contains($html, '<script')
                    && !str_contains($html, 'javascript:')));
    }

    public function test_missing_note_returns_404(): void
    {
        $this->get('/redteam/does-not-exist')->assertNotFound();
    }

    public function test_path_traversal_returns_404(): void
    {
        $this->makeNote('redteam', 'ok', '# ok');
        file_put_contents($this->notesPath . '/secret.md', 'top secret');

        $this->get('/redteam/..%2Fsecret')->assertNotFound();
        $this->get('/redteam/../secret')->assertNotFound();
        $this->get('/blueteam/..%2F..%2F.env')->assertNotFound();
    }

    public function test_whoami_page_renders(): void
    {
        $this->get('/whoami')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('WhoamiPage'));
    }
}
