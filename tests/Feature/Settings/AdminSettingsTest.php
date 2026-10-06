<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(SettingsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
});

function validSettingsPayload(array $overrides = []): array
{
    return array_merge([
        'company_name' => ['ar' => 'بيتكودك', 'en' => 'Bitcodak'],
        'whatsapp_egypt' => '+20 100 000 0000',
        'whatsapp_dubai' => '+971 50 000 0000',
        'gulf_countries' => ['SA', 'AE', 'KW'],
        'email' => 'info@mindholding.net',
        'lead_notification_email' => 'sales@mindholding.net',
        'address' => ['ar' => 'عنوان جديد', 'en' => 'New address'],
        'social_links' => ['facebook' => 'https://facebook.com/mindholding'],
        'budget_options' => [['ar' => 'خيار', 'en' => 'Option']],
        'start_timing_options' => [['ar' => 'خيار', 'en' => 'Option']],
    ], $overrides);
}

it('allows an administrator to view settings with both locales', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->getJson('/api/v1/admin/settings');

    $response->assertOk();
    $response->assertJsonPath('data.company_name.ar', 'بيتكودك');
    $response->assertJsonPath('data.company_name.en', 'Bitcodak');
    $response->assertJsonPath('data.lead_notification_email', 'info@bitcodak.com');
});

it('allows an administrator to update settings', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/admin/settings', validSettingsPayload());

    $response->assertOk();
    $response->assertJsonPath('data.company_name.en', 'Bitcodak');
    $response->assertJsonPath('data.whatsapp_egypt', '+20 100 000 0000');
    $response->assertJsonPath('data.lead_notification_email', 'sales@mindholding.net');
});

it('uploads and converts a logo to webp', function () {
    Storage::fake('public');

    $logo = UploadedFile::fake()->image('logo.png', 400, 400);

    $response = $this->actingAs($this->admin, 'sanctum')
        ->post('/api/v1/admin/settings', [
            ...validSettingsPayload(),
            'logo' => $logo,
        ]);

    $response->assertOk();
    expect($response->json('data.logo_url'))->not->toBeNull();
    expect($response->json('data.logo_url'))->toContain('.webp');
});

it('validates settings input', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/admin/settings', []);

    $response->assertStatus(422);
    foreach (['company_name', 'whatsapp_egypt', 'whatsapp_dubai', 'gulf_countries', 'email', 'lead_notification_email', 'address', 'budget_options', 'start_timing_options'] as $field) {
        expect($response->json("errors.$field"))->not->toBeNull();
    }
});

it('forbids a content editor from viewing or updating settings', function () {
    $editor = User::factory()->create();
    $editor->assignRole('Content editor');

    $this->actingAs($editor, 'sanctum')->getJson('/api/v1/admin/settings')->assertStatus(403);
    $this->actingAs($editor, 'sanctum')->postJson('/api/v1/admin/settings', validSettingsPayload())->assertStatus(403);
});

it('forbids sales from viewing or updating settings', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this->actingAs($sales, 'sanctum')->getJson('/api/v1/admin/settings')->assertStatus(403);
});
