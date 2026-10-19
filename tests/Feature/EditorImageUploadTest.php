<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EditorImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    public function test_guests_and_non_admins_cannot_upload(): void
    {
        Storage::fake('public');
        $payload = ['team' => 'redteam', 'image' => UploadedFile::fake()->image('shot.png')];

        $this->postJson('/api/editor/upload-image', $payload)->assertUnauthorized();
        $this->actingAs(User::factory()->create())
            ->postJson('/api/editor/upload-image', $payload)
            ->assertForbidden();
    }

    public function test_admin_can_upload_an_image(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin())->postJson('/api/editor/upload-image', [
            'team' => 'redteam',
            'image' => UploadedFile::fake()->image('My Screenshot.png'),
        ]);

        $response->assertOk()->assertJson(['success' => true, 'alt' => 'my-screenshot']);
        $this->assertStringContainsString('/storage/articles/redteam/', $response->json('markdown'));
        $this->assertStringStartsWith('![my-screenshot](', $response->json('markdown'));

        $path = substr($response->json('url'), strlen('/storage/'));
        Storage::disk('public')->assertExists($path);
    }

    public function test_non_image_file_is_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->postJson('/api/editor/upload-image', [
            'team' => 'redteam',
            'image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
        ])->assertUnprocessable()->assertJsonValidationErrors('image');
    }

    public function test_extension_comes_from_content_not_client_name(): void
    {
        Storage::fake('public');

        $png = UploadedFile::fake()->image('evil.png');
        $file = new UploadedFile($png->getPathname(), 'evil.html', 'image/png', null, true);

        $response = $this->actingAs($this->admin())->postJson('/api/editor/upload-image', [
            'team' => 'redteam',
            'image' => $file,
        ]);

        // Either rejected by the mimes rule or stored with the real extension.
        if ($response->status() === 200) {
            $this->assertStringEndsWith('.png', $response->json('url'));
        } else {
            $response->assertUnprocessable();
        }
    }
}
