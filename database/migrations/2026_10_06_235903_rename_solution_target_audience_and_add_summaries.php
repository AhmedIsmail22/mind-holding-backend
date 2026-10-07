<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solutions', function (Blueprint $table) {
            $table->renameColumn('target_audience', 'audience');
            $table->json('summary')->nullable()->after('audience');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->json('summary')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('summary');
        });

        Schema::table('solutions', function (Blueprint $table) {
            $table->dropColumn('summary');
            $table->renameColumn('audience', 'target_audience');
        });
    }
};
