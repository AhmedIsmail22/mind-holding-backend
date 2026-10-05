<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('related_solutions', function (Blueprint $table) {
            $table->foreignId('solution_id')->constrained('solutions')->cascadeOnDelete();
            $table->foreignId('related_solution_id')->constrained('solutions')->cascadeOnDelete();
            $table->primary(['solution_id', 'related_solution_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('related_solutions');
    }
};
