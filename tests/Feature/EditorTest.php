<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EditorTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/editor')->assertRedirect('/login');
        $this->postJson('/api/editor/save', [])->assertUnauthorized();
    }

    public function test_non_admin_users_are_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/editor')->assertForbidden();
        $this->actingAs($user)->postJson('/api/editor/save', [
            'team' => 'redteam', 'path' => 'x', 'content' => 'x',
        ])->assertForbidden();
    }

    public function test_is_admin_cannot_be_mass_assigned(): void
    {
        $user = User::create([
            'name' => 'x', 'email' => 'x@example.com', 'password' => 'password', 'is_admin' => true,
        ]);

        $this->assertFalse((bool) $user->fresh()->is_admin);
    }

    public function test_editor_opens_without_parameters(): void
    {
        $this->actingAs($this->admin())
            ->get('/editor')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('EditorPage')
                ->where('team', 'redteam')
                ->where('isNew', true));
    }

    public function test_editor_loads_an_existing_note(): void
    {
        $this->makeNote('blueteam', 'detection/Sigma', '# Sigma');

        $this->actingAs($this->admin())
            ->get('/editor?team=blueteam&path=detection/Sigma')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('content', '# Sigma')
                ->where('isNew', false));
    }

    public function test_unknown_team_returns_404(): void
    {
        $this->actingAs($this->admin())->get('/editor?team=purple')->assertNotFound();
    }

    public function test_admin_can_save_and_delete_a_note(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->postJson('/api/editor/save', [
            'team' => 'automation', 'path' => 'scripts/hello', 'content' => '# Hello',
        ])->assertOk()->assertJson(['success' => true, 'url' => '/automation/scripts/hello']);

        $this->assertFileExists("{$this->notesPath}/automation/scripts/hello.md");

        $this->actingAs($admin)->postJson('/api/editor/delete', [
            'team' => 'automation', 'path' => 'scripts/hello',
        ])->assertOk();

        $this->assertFileDoesNotExist("{$this->notesPath}/automation/scripts/hello.md");
        $this->assertDirectoryDoesNotExist("{$this->notesPath}/automation/scripts");
    }

    public function test_save_reports_an_unwritable_notes_folder(): void
    {
        // A file where the folder should be: mkdir fails even when running as root.
        $this->makeNote('redteam', 'placeholder', 'x');
        file_put_contents("{$this->notesPath}/redteam/blocked", 'not a folder');

        $this->actingAs($this->admin())->postJson('/api/editor/save', [
            'team' => 'redteam', 'path' => 'blocked/New note', 'content' => '# New',
        ])->assertStatus(500)->assertJsonPath('error', fn (string $error) => str_contains($error, 'écriture'));
    }

    public function test_save_rejects_path_traversal_and_unknown_teams(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->postJson('/api/editor/save', [
            'team' => 'redteam', 'path' => '../../evil', 'content' => 'x',
        ])->assertStatus(400);

        $this->actingAs($admin)->postJson('/api/editor/save', [
            'team' => 'purple', 'path' => 'x', 'content' => 'x',
        ])->assertUnprocessable();

        $this->assertFileDoesNotExist(dirname($this->notesPath) . '/evil.md');
    }

    public function test_preview_strips_raw_html(): void
    {
        $this->actingAs($this->admin())
            ->postJson('/api/editor/preview', ['content' => "# Hi\n\n<script>alert(1)</script>"])
            ->assertOk()
            ->assertJsonPath('html', fn (string $html) => str_contains($html, '<h1>Hi</h1>')
                && !str_contains($html, '<script'));
    }
}
