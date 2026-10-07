<?php

use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
});

function validProjectPayload(array $overrides = []): array
{
    return array_merge([
        'slug' => 'client-project',
        'client_name' => ['ar' => 'عميل', 'en' => 'Client'],
        'hide_client_name' => false,
        'overview' => ['ar' => 'نظرة عامة', 'en' => 'Overview'],
        'challenge' => ['ar' => 'التحدي', 'en' => 'Challenge'],
        'solution' => ['ar' => 'الحل', 'en' => 'Solution'],
        'technologies' => ['Laravel'],
        'is_published' => true,
        'order' => 0,
    ], $overrides);
}

it('allows a content editor to create a project', function () {
    $editor = User::factory()->create();
    $editor->assignRole('Content editor');

    $response = $this->actingAs($editor, 'sanctum')->postJson('/api/v1/admin/projects', validProjectPayload());

    $response->assertCreated();
    $response->assertJsonPath('data.client_name.en', 'Client');
});

it('requires a generic description when the client name is hidden', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/projects', validProjectPayload([
        'hide_client_name' => true,
    ]));

    $response->assertStatus(422);
    expect($response->json('errors.generic_description'))->not->toBeNull();
});

it('accepts hidden client name when a generic description is given', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/projects', validProjectPayload([
        'hide_client_name' => true,
        'generic_description' => ['ar' => 'وصف', 'en' => 'Generic description'],
    ]));

    $response->assertCreated();
    $response->assertJsonPath('data.hide_client_name', true);
});

it('validates project creation input', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/projects', []);

    $response->assertStatus(422);
    foreach (['slug', 'client_name', 'overview', 'challenge', 'solution', 'technologies'] as $field) {
        expect($response->json("errors.$field"))->not->toBeNull();
    }
});

it('syncs related services', function () {
    $service = Service::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/projects', validProjectPayload([
        'related_service_ids' => [$service->id],
    ]));

    $response->assertCreated();
    $response->assertJsonPath('data.related_service_ids', [$service->id]);
});

it('uploads project images converted to webp', function () {
    Storage::fake('public');

    $response = $this->actingAs($this->admin, 'sanctum')->post('/api/v1/admin/projects', [
        ...validProjectPayload(),
        'images' => [UploadedFile::fake()->image('project1.png', 600, 400)],
    ]);

    $response->assertCreated();
    expect($response->json('data.images'))->toHaveCount(1);
    expect($response->json('data.images.0'))->toContain('.webp');
});

it('updates a project via post', function () {
    $project = Project::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')->post("/api/v1/admin/projects/{$project->id}", validProjectPayload([
        'is_published' => false,
    ]));

    $response->assertOk();
    $response->assertJsonPath('data.is_published', false);
});

it('soft deletes a project', function () {
    $project = Project::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/projects/{$project->id}");

    $response->assertOk();
    $this->assertSoftDeleted('projects', ['id' => $project->id]);
});

it('forbids sales from managing projects', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this->actingAs($sales, 'sanctum')->postJson('/api/v1/admin/projects', validProjectPayload())->assertStatus(403);
});
