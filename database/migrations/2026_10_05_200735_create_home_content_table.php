<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_content', function (Blueprint $table) {
            $table->id();
            $table->json('hero_headline');
            $table->json('hero_subheadline');
            $table->json('stats'); // [{label: {ar,en}, value: string}, ...] — real numbers only, never invented
            $table->json('differentiators'); // [{title: {ar,en}, description: {ar,en}}, ...]
            $table->json('process_steps'); // [{title: {ar,en}, description: {ar,en}, duration: {ar,en}}, ...]
            $table->json('closing_cta_headline');
            $table->json('closing_cta_subheadline')->nullable();
            $table->boolean('is_draft')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_content');
    }
};
