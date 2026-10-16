<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_user_can_log_in_and_out(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertNoContent();
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertNoContent();
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong']);
        $this->assertGuest();
    }

    public function test_login_is_throttled_after_five_failures(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/login', ['email' => $user->email, 'password' => 'wrong'])->assertUnprocessable();
        }

        $this->postJson('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
        $this->assertGuest();
    }

    public function test_registration_is_disabled(): void
    {
        $this->post('/register', [])->assertNotFound();
    }

    public function test_shared_user_props_do_not_leak_the_email(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/whoami')
            ->assertInertia(fn ($page) => $page
                ->where('auth.user.id', $user->id)
                ->missing('auth.user.email'));
    }
}
