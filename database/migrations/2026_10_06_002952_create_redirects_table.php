<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            // Path only (e.g. /old-page), without the domain. Served as 301.
            $table->string('old_path', 500);
            $table->string('new_path', 500);
            $table->timestamps();
            $table->softDeletes();

            // NULL deleted_at values are distinct in MySQL, so this index does not
            // block two active rows with the same path; the request validation does.
            $table->unique(['old_path', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
