<?php

use App\Models\Technology;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
});

it('returns an empty list when no technology has been added', function () {
    $response = $this->getJson('/api/v1/public/technologies');

    $response->assertOk();
    expect($response->json('data'))->toBe([]);
});

it('groups technologies by category in the resolved locale', function () {
    Technology::create(['name' => 'Laravel', 'category' => ['ar' => 'خلفية', 'en' => 'Backend'], 'order' => 0]);
    Technology::create(['name' => 'Vue', 'category' => ['ar' => 'واجهة', 'en' => 'Frontend'], 'order' => 1]);
    Technology::create(['name' => 'Symfony', 'category' => ['ar' => 'خلفية', 'en' => 'Backend'], 'order' => 2]);

    $response = $this->getJson('/api/v1/public/technologies', ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('data.0.category'))->toBe('Backend');
    expect($response->json('data.0.items'))->toHaveCount(2);
});

it('creates a technology with a webp logo', function () {
    Storage::fake('public');

    $response = $this->actingAs($this->admin, 'sanctum')->post('/api/v1/admin/technologies', [
        'name' => 'Laravel',
        'category' => ['ar' => 'خلفية', 'en' => 'Backend'],
        'logo' => UploadedFile::fake()->image('laravel.png', 200, 200),
    ]);

    $response->assertCreated();
    expect($response->json('data.logo_url'))->toContain('.webp');
});

it('validates technology creation', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/technologies', []);

    $response->assertStatus(422);
    expect($response->json('errors.name'))->not->toBeNull();
    expect($response->json('errors.category'))->not->toBeNull();
});

it('updates and soft deletes a technology', function () {
    $technology = Technology::create(['name' => 'Old', 'category' => ['ar' => 'أ', 'en' => 'A'], 'order' => 0]);

    $this->actingAs($this->admin, 'sanctum')->postJson("/api/v1/admin/technologies/{$technology->id}", [
        'name' => 'New',
        'category' => ['ar' => 'أ', 'en' => 'A'],
    ])->assertOk();
    expect($technology->refresh()->name)->toBe('New');

    $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/technologies/{$technology->id}")->assertOk();
    $this->assertSoftDeleted('technologies', ['id' => $technology->id]);
});

it('forbids sales from managing technologies', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this->actingAs($sales, 'sanctum')->getJson('/api/v1/admin/technologies')->assertStatus(403);
});
