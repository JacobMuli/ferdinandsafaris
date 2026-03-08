<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('highlights')->nullable();
            $table->text('itinerary')->nullable();
            $table->integer('duration_days');
            $table->string('difficulty_level')->default('moderate'); // easy, moderate, challenging
            $table->decimal('price_per_person', 10, 2);
            $table->decimal('child_price', 10, 2)->nullable();
            $table->decimal('group_discount_percentage', 5, 2)->default(0);
            $table->integer('min_group_size')->default(5);
            $table->decimal('corporate_discount_percentage', 5, 2)->default(0);
            $table->integer('max_participants')->default(20);
            $table->string('category'); // wildlife, cultural, adventure, beach
            $table->string('location');
            $table->json('included_items')->nullable(); // meals, transport, accommodation
            $table->json('excluded_items')->nullable();
            $table->json('requirements')->nullable(); // fitness level, age restrictions
            $table->string('featured_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('available_from')->nullable();
            $table->date('available_to')->nullable();
            $table->integer('views')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};