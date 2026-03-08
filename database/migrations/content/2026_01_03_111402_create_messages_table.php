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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The customer
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // Who sent it (Customer or Admin)
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_admin_message')->default(false); // Helper to quickly check direction
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
