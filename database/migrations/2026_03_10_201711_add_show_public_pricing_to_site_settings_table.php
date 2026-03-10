<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('site_settings')->insert([
            'key' => 'show_public_pricing',
            'value' => '0',
            'group' => 'financial',
            'type' => 'boolean',
            'label' => 'Show Public Pricing',
            'description' => 'Toggle whether tourists can see tour prices and calculators on public pages.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('site_settings')->where('key', 'show_public_pricing')->delete();
    }
};
