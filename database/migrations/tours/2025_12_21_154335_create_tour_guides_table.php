<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('employment_status')->default('freelancer'); // full_time, part_time, freelancer
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('license_number')->unique();
            $table->date('license_expiry_date');
            $table->text('bio')->nullable();
            $table->string('profile_photo')->nullable();
            $table->json('languages')->nullable(); // ['English', 'Swahili', 'French']
            $table->json('specializations')->nullable(); // ['Wildlife', 'Mountain', 'Cultural']
            $table->json('certifications')->nullable(); // ['First Aid', 'Wildlife Expert']
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_tours')->default(0);
            $table->integer('years_experience')->default(0);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('is_active');
            $table->index('is_available');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_guides');
    }
};