<?php

use App\Models\Project;

it('returns an empty list when no project is published, so the frontend hides the Work link', function () {
    Project::factory()->create(['is_published' => false]);

    $response = $this->getJson('/api/v1/public/projects');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(0);
});

it('shows the client name when not hidden', function () {
    Project::factory()->create([
        'client_name' => ['ar' => 'عميل', 'en' => 'Acme Co'],
        'hide_client_name' => false,
        'is_published' => true,
    ]);

    $response = $this->getJson('/api/v1/public/projects', ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data.0.title'))->toBe('Acme Co');
});

it('shows the generic description instead of the client name when hidden', function () {
    Project::factory()->create([
        'client_name' => ['ar' => 'عميل', 'en' => 'Acme Co'],
        'hide_client_name' => true,
        'generic_description' => ['ar' => 'وصف عام', 'en' => 'E-commerce store for a home-goods brand'],
        'is_published' => true,
    ]);

    $response = $this->getJson('/api/v1/public/projects', ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data.0.title'))->toBe('E-commerce store for a home-goods brand');
});

it('shows project detail with overview, challenge, solution and technologies', function () {
    $project = Project::factory()->create([
        'is_published' => true,
        'technologies' => ['Laravel', 'Vue'],
    ]);

    $response = $this->getJson("/api/v1/public/projects/{$project->id}", ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data.technologies'))->toBe(['Laravel', 'Vue']);
    expect($response->json('data.overview'))->not->toBeNull();
});

it('returns 404 for an unpublished project on the public endpoint', function () {
    $project = Project::factory()->create(['is_published' => false]);

    $response = $this->getJson("/api/v1/public/projects/{$project->id}");

    $response->assertStatus(404);
});
