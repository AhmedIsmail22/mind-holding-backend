<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->json('company_name'); // translatable {ar, en}
            $table->string('phone_landline')->nullable();
            $table->string('phone_mobile_egypt')->nullable();
            $table->string('whatsapp_egypt');
            $table->string('whatsapp_dubai');
            $table->json('gulf_countries'); // ISO 3166-1 alpha-2 codes, e.g. ["SA","AE"]
            $table->string('email');
            $table->string('lead_notification_email');
            $table->json('address'); // translatable {ar, en}
            $table->json('social_links'); // {platform: url}
            $table->json('budget_options'); // [{ar, en}, ...]
            $table->json('start_timing_options'); // [{ar, en}, ...]
            $table->string('google_analytics_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
