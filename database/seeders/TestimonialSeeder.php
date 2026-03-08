<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific featured testimonials
        \App\Models\Testimonial::factory()->create([
            'name' => 'Sarah M.',
            'country' => 'USA',
            'message' => 'An unforgettable safari experience. The guides were incredible!',
            'rating' => 5,
            'is_featured' => true,
            'source' => 'google',
        ]);

        \App\Models\Testimonial::factory()->create([
            'name' => 'James K.',
            'country' => 'UK',
            'message' => 'Everything exceeded expectations. Highly recommended!',
            'rating' => 5,
            'is_featured' => true,
            'source' => 'internal',
        ]);

        // Generate more random testimonials
        \App\Models\Testimonial::factory()->count(20)->create();

    }
}
