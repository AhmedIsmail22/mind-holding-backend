<?php

use Database\Seeders\SettingsSeeder;

beforeEach(function () {
    $this->seed(SettingsSeeder::class);
});

it('returns settings in the resolved locale', function () {
    $response = $this->getJson('/api/v1/public/settings', ['Accept-Language' => 'en']);

    $response->assertOk();
    $response->assertJsonPath('data.company_name', 'Bitcodak');
    $response->assertJsonPath('data.address', '8 Mohammed Tawfik Diab, Nasr City, Cairo, Egypt');
    $response->assertJsonPath('data.whatsapp_egypt', '+20 111 564 6730');
    $response->assertJsonPath('data.whatsapp_dubai', '+971 50 336 5403');
    expect($response->json('data.gulf_countries'))->toContain('SA', 'AE');
    expect($response->json('data.budget_options.0'))->toBe('Less than $1,000');
});

it('defaults to arabic content', function () {
    $response = $this->withServerVariables(['HTTP_ACCEPT_LANGUAGE' => ''])->getJson('/api/v1/public/settings');

    $response->assertOk();
    $response->assertJsonPath('data.company_name', 'بيتكودك');
    expect($response->json('data.address'))->toContain('القاهرة');
});

it('does not expose the lead notification email publicly', function () {
    $response = $this->getJson('/api/v1/public/settings');

    $response->assertOk();
    expect($response->json('data'))->not->toHaveKey('lead_notification_email');
});
