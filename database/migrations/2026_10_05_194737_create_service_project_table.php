<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_project', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->primary(['service_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_project');
    }
};
