<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('park_location_tour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->foreignId('park_location_id')->references('id')->on('parks_locations')->onDelete('cascade');
            $table->integer('day_number')->nullable(); // Which day of the tour
            $table->integer('duration_hours')->nullable(); // How long at this location
            $table->integer('days')->default(1); // Added from complementary
            $table->boolean('is_primary_location')->default(false);
            $table->timestamps();
            
            $table->index('tour_id');
            $table->index('park_location_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('park_location_tour');
    }
};