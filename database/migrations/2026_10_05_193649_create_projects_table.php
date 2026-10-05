<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->json('client_name'); // always stored for admin reference
            $table->boolean('hide_client_name')->default(false);
            $table->json('generic_description')->nullable(); // shown publicly instead of client_name when hidden
            $table->json('overview');
            $table->json('challenge');
            $table->json('solution');
            $table->json('technologies'); // flat array of strings, e.g. ["Laravel", "React"]
            $table->string('live_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
