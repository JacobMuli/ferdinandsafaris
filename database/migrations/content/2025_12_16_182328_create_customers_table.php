<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('avatar')->nullable(); // Added from complementary
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('country');
            $table->string('customer_type'); // individual, family, group, corporate
            $table->string('company_name')->nullable();
            $table->string('tax_id')->nullable();
            $table->text('special_requirements')->nullable();
            $table->boolean('newsletter_subscribed')->default(false);
            $table->integer('total_bookings')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->string('preferred_contact_method')->default('email'); // email, phone, whatsapp
            
            // Merged columns
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};