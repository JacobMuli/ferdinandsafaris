<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parks_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type'); // national_park, reserve, conservation_area, beach, mountain, city
            $table->string('country');
            $table->string('region')->nullable();
            $table->text('description')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('gallery_images')->nullable();
            
            // Location details
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('area_size_km2')->nullable();
            $table->integer('elevation_meters')->nullable();
            
            // Visit information
            $table->decimal('entry_fee_adult', 8, 2)->nullable();
            $table->decimal('entry_fee_child', 8, 2)->nullable();
            $table->string('best_time_to_visit')->nullable(); // e.g., "June - October"
            $table->json('opening_hours')->nullable(); // {"open": "06:00", "close": "18:00"}
            
            // Features and facilities
            $table->json('wildlife')->nullable(); // ['Lions', 'Elephants', 'Rhinos']
            $table->json('activities')->nullable(); // ['Game Drives', 'Hiking', 'Bird Watching']
            $table->json('facilities')->nullable(); // ['Lodges', 'Campsites', 'Visitor Center']
            $table->json('highlights')->nullable(); // ['Big Five', 'Great Migration']
            
            // Administrative
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            // $table->string('website')->nullable(); // Removed
            $table->text('access_info')->nullable(); // How to get there
            $table->text('rules_regulations')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('popularity_rank')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('type');
            $table->index('country');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parks_locations');
    }
};