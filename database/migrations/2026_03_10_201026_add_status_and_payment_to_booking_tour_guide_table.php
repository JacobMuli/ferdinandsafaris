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
        Schema::table('booking_tour_guide', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('tour_guide_id');
            $table->decimal('offered_payment', 10, 2)->nullable()->after('status');
            $table->text('notes')->nullable()->after('offered_payment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_tour_guide', function (Blueprint $table) {
            $table->dropColumn(['status', 'offered_payment', 'notes']);
        });
    }
};
