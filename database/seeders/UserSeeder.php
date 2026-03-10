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
        // Main Admin: Developer
        User::updateOrCreate(
            ['email' => 'jacobmwalughs@gmail.com'],
            [
                'name' => 'Jacob Mwalugho',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_super_admin' => true,
                'is_admin' => true,
                'remember_token' => Str::random(10),
            ]
        );

        // Main Admin: Owner
        User::updateOrCreate(
            ['email' => 'ferdimwalugho@hotmail.com'],
            [
                'name' => 'Ferdinand Mwalugho',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_super_admin' => true,
                'is_admin' => true,
                'remember_token' => Str::random(10),
            ]
        );
    }
}
