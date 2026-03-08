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
        Schema::create('tour_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->foreignId('accommodation_id')->constrained()->onDelete('cascade');
            $table->integer('night_number')->nullable();
            $table->integer('nights')->default(1);
            $table->string('room_type')->nullable();
            $table->string('board_basis')->nullable(); // FB, HB, BB
            $table->decimal('price_per_night', 10, 2)->nullable();
            $table->boolean('included_in_tour_price')->default(false);
            $table->text('notes')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_accommodations');
    }
};
