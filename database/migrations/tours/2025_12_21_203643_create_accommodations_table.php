<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('type')->default('lodge'); // lodge, camp, hotel
            $table->string('category')->default('standard'); // budget, standard, luxury
            $table->text('description')->nullable();
            $table->json('amenities')->nullable();
            $table->json('room_types')->nullable();
            
            // Location
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignId('park_location_id')->nullable()->constrained('parks_locations')->nullOnDelete();

            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            // $table->string('website')->nullable(); // Removed

            // Capacity & Pricing
            $table->integer('total_rooms')->default(0);
            $table->integer('max_guests')->nullable();
            $table->decimal('price_per_night_budget', 10, 2)->nullable();
            $table->decimal('price_per_night_standard', 10, 2)->nullable();
            $table->decimal('price_per_night_deluxe', 10, 2)->nullable();
            $table->decimal('price_per_night_suite', 10, 2)->nullable();

            // Inclusions (Booleans)
            $table->boolean('breakfast_included')->default(false);
            $table->boolean('lunch_included')->default(false);
            $table->boolean('dinner_included')->default(false);
            $table->string('board_basis')->nullable(); // BB, HB, FB, AI

            // Ratings & Media
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->string('main_image')->nullable();
            $table->json('gallery_images')->nullable();

            // Features (Booleans)
            $table->boolean('family_friendly')->default(false);
            $table->boolean('pet_friendly')->default(false);
            $table->boolean('wheelchair_accessible')->default(false);
            $table->boolean('airport_transfer_available')->default(false);
            $table->boolean('is_available')->default(true);
            $table->boolean('requires_deposit')->default(false);
            
            // Rules
            $table->integer('min_nights')->default(1);
            $table->integer('max_nights')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->text('house_rules')->nullable();
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
