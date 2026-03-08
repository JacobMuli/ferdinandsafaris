<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('customer_type'); // individual, family, group, corporate
            
            // Merged columns
            $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types')->onDelete('set null');
            $table->foreignId('accommodation_id')->nullable()->constrained('accommodations')->onDelete('set null'); 
            
            $table->date('tour_date');
            $table->integer('adults_count')->default(1);
            $table->integer('children_count')->default(0);
            $table->integer('total_participants');
            $table->decimal('base_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('discount_type')->nullable(); // group, corporate, promo
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('actual_price', 10, 2)->nullable(); // Added from complementary
            $table->json('pricing_breakdown')->nullable(); // Added from complementary
            
            $table->string('status')->default('pending'); // pending, confirmed, paid, cancelled, completed
            $table->string('payment_status')->default('pending'); // pending, partial, paid, refunded
            $table->text('special_requests')->nullable();
            $table->json('participant_details')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['tour_date', 'status']);
            $table->index(['customer_id']);
            $table->index(['booking_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};