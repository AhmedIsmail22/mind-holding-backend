<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solution_industry_id')->constrained('solution_industries')->restrictOnDelete();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('target_audience'); // e.g. "For restaurants and cafés"
            $table->json('problem_points'); // [{ar, en}, ...]
            $table->json('features'); // {customer: [{ar,en}], business_owner: [...], staff: [...]}
            $table->json('deliverables'); // [{ar, en}, ...]
            $table->string('demo_url')->nullable();
            $table->text('demo_credentials')->nullable();
            $table->boolean('is_flagship')->default(false);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_draft')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solutions');
    }
};
