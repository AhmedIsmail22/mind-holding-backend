<?php

use App\Models\SolutionIndustry;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
});

it('lists industries publicly', function () {
    SolutionIndustry::factory()->create(['name' => ['ar' => 'تجارة', 'en' => 'Commerce'], 'slug' => 'commerce']);

    $response = $this->getJson('/api/v1/public/solution-industries', ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data.0.name'))->toBe('Commerce');
});

it('allows a content editor to create an industry', function () {
    $editor = User::factory()->create();
    $editor->assignRole('Content editor');

    $response = $this->actingAs($editor, 'sanctum')->postJson('/api/v1/admin/solution-industries', [
        'name' => ['ar' => 'تعليم', 'en' => 'Education'],
        'slug' => 'education',
        'order' => 1,
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.name.en', 'Education');
    $this->assertDatabaseHas('solution_industries', ['slug' => 'education']);
});

it('validates industry creation', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/solution-industries', []);

    $response->assertStatus(422);
    expect($response->json('errors.name'))->not->toBeNull();
    expect($response->json('errors.slug'))->not->toBeNull();
});

it('rejects a duplicate slug', function () {
    SolutionIndustry::factory()->create(['slug' => 'commerce']);

    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/solution-industries', [
        'name' => ['ar' => 'تجارة', 'en' => 'Commerce'],
        'slug' => 'commerce',
    ]);

    $response->assertStatus(422);
    expect($response->json('errors.slug'))->not->toBeNull();
});

it('allows updating an industry keeping its own slug', function () {
    $industry = SolutionIndustry::factory()->create(['slug' => 'commerce']);

    $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/v1/admin/solution-industries/{$industry->id}", [
        'name' => ['ar' => 'تجارة محدثة', 'en' => 'Updated Commerce'],
        'slug' => 'commerce',
        'order' => 2,
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.name.en', 'Updated Commerce');
});

it('soft deletes an industry', function () {
    $industry = SolutionIndustry::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/solution-industries/{$industry->id}");

    $response->assertOk();
    $this->assertSoftDeleted('solution_industries', ['id' => $industry->id]);
});

it('forbids sales from managing industries', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this->actingAs($sales, 'sanctum')->getJson('/api/v1/admin/solution-industries')->assertStatus(403);
});
