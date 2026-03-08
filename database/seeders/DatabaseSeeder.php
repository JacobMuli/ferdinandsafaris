<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            // Tours managed via Admin upload
            BookingSeeder::class,
            ReviewSeeder::class,

            TourGuideSeeder::class,
            TourGuideSeeder::class,
            UpdateCustomerDetailsSeeder::class,
        ]);
        
        $this->command->info('Database seeded successfully using Factories and Seeders!');
    }
}