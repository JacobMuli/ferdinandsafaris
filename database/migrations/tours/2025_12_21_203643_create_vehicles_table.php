<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types')->onDelete('cascade');
            $table->string('registration_number')->unique();
            $table->integer('year')->nullable();
            $table->string('color')->nullable();
            
            // Status & Maintenance
            $table->string('condition')->nullable(); // excellent, good, fair
            $table->integer('mileage')->default(0);
            $table->date('last_service_date')->nullable();
            $table->date('next_service_due')->nullable();
            $table->text('maintenance_notes')->nullable();
            
            // Documents
            $table->string('insurance_company')->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->date('road_tax_expiry_date')->nullable();
            $table->date('inspection_expiry_date')->nullable();

            // Availability
            $table->string('status')->default('available'); // available, in_use, maintenance, retired
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->date('available_from')->nullable();
            $table->date('available_to')->nullable();
            
            // Operations
            $table->foreignId('current_booking_id')->nullable(); 
            $table->foreignId('assigned_driver_id')->nullable()->constrained('tour_guides')->nullOnDelete();
            
            // Costs overrides (optional)
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->decimal('fuel_consumption', 8, 2)->nullable(); // L/100km
            $table->decimal('cost_per_km', 8, 2)->nullable();

            // Tracking (Simple)
            $table->string('current_location')->nullable();
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->string('home_base')->nullable();

            // Media
            $table->string('main_image')->nullable();
            $table->json('gallery_images')->nullable();
            
            $table->text('description')->nullable();
            $table->text('special_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
