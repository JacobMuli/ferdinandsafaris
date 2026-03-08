<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleTypesTable extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->unsignedTinyInteger('default_capacity');
            $table->json('features')->nullable();
            $table->decimal('base_daily_rate', 8, 2);
            $table->decimal('base_cost_per_km', 6, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_types');
    }
}
