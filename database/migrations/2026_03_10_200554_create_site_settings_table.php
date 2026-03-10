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
        Schema::create('site_settings', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('key')->unique();
            $blueprint->text('value')->nullable();
            $blueprint->string('group')->default('general');
            $blueprint->string('type')->default('string'); // string, boolean, integer, decimal
            $blueprint->string('label')->nullable();
            $blueprint->text('description')->nullable();
            $blueprint->timestamps();
        });

        // Seed default settings
        DB::table('site_settings')->insert([
            [
                'key' => 'tax_rate',
                'value' => '0.16',
                'group' => 'financial',
                'type' => 'decimal',
                'label' => 'Tax/VAT Rate',
                'description' => 'Standard tax rate applied to all bookings (e.g. 0.16 for 16%)',
            ],
            [
                'key' => 'currency',
                'value' => 'USD',
                'group' => 'financial',
                'type' => 'string',
                'label' => 'Default Currency',
                'description' => 'Default currency code to display (e.g. USD, KES)',
            ],
            [
                'key' => 'support_email',
                'value' => 'info@ferdinandsafaris.com',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Support Email',
                'description' => 'Primary contact email for customer support',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
