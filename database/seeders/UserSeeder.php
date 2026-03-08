<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Users
        User::updateOrCreate(
            ['email' => 'jacobmwalughs@gmail.com'],
            [
                'name' => 'Jacob Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => true,
                'remember_token' => Str::random(10),
            ]
        );

        User::factory()->count(5)->create();
    }
}
