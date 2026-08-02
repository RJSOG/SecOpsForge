<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Creates/updates the admin account from environment variables.
     *
     * Nothing is hard-coded here on purpose: ADMIN_EMAIL / ADMIN_PASSWORD
     * must be set in the local .env (never committed) before running this
     * seeder. If ADMIN_PASSWORD is left empty, a random password is
     * generated and printed once so it can be stored in a password manager.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');

        if (!$email) {
            $this->command?->warn('AdminSeeder: ADMIN_EMAIL is not set in .env, skipping admin account creation.');
            return;
        }

        $password = env('ADMIN_PASSWORD');
        $generated = false;

        if (!$password) {
            $password = Str::password(24);
            $generated = true;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        // is_admin is intentionally not mass-assignable (see User::casts()
        // doc comment), so it's set here via direct property assignment.
        $user->is_admin = true;
        $user->save();

        if ($generated) {
            $this->command?->warn("AdminSeeder: ADMIN_PASSWORD was not set, generated one for {$email}: {$password}");
            $this->command?->warn('Store this password in a password manager now — it will not be shown again.');
        }
    }
}
