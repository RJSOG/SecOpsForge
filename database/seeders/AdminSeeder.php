<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'eliott.donatien@gmail.com'],
            [
                'name' => 'Eliott',
                'password' => Hash::make('Ax?bRk9kxSfQhHtS'),
                'email_verified_at' => now(),
            ]
        );
    }
}
