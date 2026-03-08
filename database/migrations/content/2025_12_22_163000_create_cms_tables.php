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
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_page_id')->constrained()->onDelete('cascade');
            $table->string('section_key'); // e.g., 'hero', 'stats', 'about_us'
            $table->string('title')->nullable(); // Section specific title
            $table->json('content')->nullable(); // Flexible content structure
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['cms_page_id', 'section_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_sections');
        Schema::dropIfExists('cms_pages');
    }
};
