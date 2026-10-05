<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            // Nullable morph: no owner = a general FAQ (shown on Home).
            $table->nullableMorphs('faqable');
            $table->json('question');
            $table->json('answer');
            $table->boolean('is_published')->default(true);
            $table->boolean('is_draft')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
