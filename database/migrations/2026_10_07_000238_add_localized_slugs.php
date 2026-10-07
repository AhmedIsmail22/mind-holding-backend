<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The existing `slug` column is the English slug. `slug_ar` is the Arabic
     * slug and is null until one is entered. Projects had no slug, so they get
     * both columns.
     */
    public function up(): void
    {
        foreach (['services', 'solutions', 'solution_industries', 'pages'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('slug_ar')->nullable()->unique()->after('slug');
            });
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('id');
            $table->string('slug_ar')->nullable()->unique()->after('slug');
        });
    }

    public function down(): void
    {
        foreach (['services', 'solutions', 'solution_industries', 'pages'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropUnique(['slug_ar']);
                $table->dropColumn('slug_ar');
            });
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropUnique(['slug_ar']);
            $table->dropColumn(['slug', 'slug_ar']);
        });
    }
};
