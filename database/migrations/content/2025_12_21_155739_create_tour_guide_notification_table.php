<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_guide_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_guide_id')->unique()->constrained()->onDelete('cascade');
            
            // Email preferences
            $table->boolean('email_enabled')->default(true);
            $table->string('email')->nullable(); // Can override main email
            
            // SMS/Phone preferences
            $table->boolean('sms_enabled')->default(true);
            $table->string('sms_phone')->nullable(); // Can override main phone
            
            // WhatsApp preferences
            $table->boolean('whatsapp_enabled')->default(true);
            $table->string('whatsapp_phone')->nullable(); // Can override main phone
            
            // Push notification (for app)
            $table->boolean('push_enabled')->default(true);
            $table->string('device_token')->nullable();
            
            // Notification timing preferences
            $table->time('quiet_hours_start')->nullable(); // Don't disturb start time
            $table->time('quiet_hours_end')->nullable();   // Don't disturb end time
            
            // Notification types
            $table->boolean('notify_new_assignments')->default(true);
            $table->boolean('notify_assignment_reminders')->default(true);
            $table->boolean('notify_tour_updates')->default(true);
            $table->boolean('notify_payments')->default(true);
            $table->boolean('notify_reviews')->default(true);
            
            // Preferred language
            $table->string('preferred_language')->default('en');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_guide_notification_preferences');
    }
};