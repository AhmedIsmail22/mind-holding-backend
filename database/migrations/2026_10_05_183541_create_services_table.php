<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->enum('group', ['software', 'marketing']);
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description');
            $table->json('deliverables'); // [{ar, en}, ...] "what the client receives"
            $table->json('delivery_steps'); // [{ar, en}, ...] ordered
            $table->boolean('is_published')->default(false);
            $table->boolean('is_draft')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
