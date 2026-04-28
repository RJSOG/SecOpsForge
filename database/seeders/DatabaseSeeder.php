<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => env('FRONT_SERVICE_EMAIL'),
            'name' => env('APP_NAME'), 'password' => bcrypt(env('FRONT_SERVICE_SECRET'))
        ]);
    }
}
