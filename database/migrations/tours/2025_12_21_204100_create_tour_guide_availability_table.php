<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_guide_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_guide_id')->constrained()->onDelete('cascade');
            
            // Date range for unavailability
            $table->date('unavailable_from');
            $table->date('unavailable_to');
            
            // Reason
            $table->enum('reason', [
                'booked',           // Already booked
                'vacation',         // Personal time off
                'sick_leave',       // Medical leave
                'training',         // Training/certification
                'other'
            ])->default('other');
            
            $table->text('notes')->nullable();
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null'); // If booked
            
            $table->timestamps();
            
            $table->index('tour_guide_id');
            $table->index(['unavailable_from', 'unavailable_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_guide_availability');
    }
};