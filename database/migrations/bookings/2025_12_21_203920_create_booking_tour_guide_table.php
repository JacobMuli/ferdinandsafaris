<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_tour_guide', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('tour_guide_id')->constrained()->onDelete('cascade');
            $table->boolean('is_lead_guide')->default(false);
            $table->timestamps();
            
            $table->unique(['booking_id', 'tour_guide_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_tour_guide');
    }
};