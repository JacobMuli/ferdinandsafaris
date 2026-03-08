<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_guide_vehicle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_guide_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');

            $table->boolean('is_primary')->default(false); // Main assigned vehicle
            $table->timestamps();

            $table->unique(['tour_guide_id', 'vehicle_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_guide_vehicle');
    }
};
