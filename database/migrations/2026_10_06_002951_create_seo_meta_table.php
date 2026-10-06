<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_meta', function (Blueprint $table) {
            $table->id();
            // Either an entity (service, solution, page) or a fixed route key (home, services, ...).
            $table->nullableMorphs('seoable');
            $table->string('route_key')->nullable()->unique();
            $table->json('title');
            $table->json('description');
            $table->boolean('is_draft')->default(true);
            $table->timestamps();

            $table->unique(['seoable_type', 'seoable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_meta');
    }
};
