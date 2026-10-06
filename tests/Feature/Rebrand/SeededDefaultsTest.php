<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;

it('seeds no MIND Holding references into any default content', function () {
    $this->seed(DatabaseSeeder::class);

    $tables = ['settings', 'services', 'solutions', 'solution_industries', 'faqs', 'home_content', 'pages', 'seo_meta', 'home_sections', 'users'];

    foreach ($tables as $table) {
        foreach (DB::table($table)->get() as $row) {
            $json = json_encode((array) $row, JSON_UNESCAPED_UNICODE);

            expect(strtolower($json))->not->toContain('mind holding')
                ->and($json)->not->toContain('مايند')
                ->and(strtolower($json))->not->toContain('mindholding');
        }
    }
});

it('seeds the Bitcodak company name and placeholder email', function () {
    $this->seed(DatabaseSeeder::class);

    $setting = DB::table('settings')->first();

    expect(json_decode($setting->company_name, true))->toBe(['ar' => 'بيتكودك', 'en' => 'Bitcodak']);
    expect($setting->email)->toBe('info@bitcodak.com');
    expect($setting->lead_notification_email)->toBe('info@bitcodak.com');
    expect($setting->whatsapp_dubai)->toBe('+971 50 336 5403');
});
