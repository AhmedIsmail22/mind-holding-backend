<?php

use App\Models\User;
use Database\Seeders\PagesSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(PagesSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
});

it('shows a public page in the resolved locale', function () {
    $response = $this->getJson('/api/v1/public/pages/privacy', ['Accept-Language' => 'en']);

    $response->assertOk();
    $response->assertJsonPath('data.slug', 'privacy');
    $response->assertJsonPath('data.title', 'Privacy policy');
});

it('returns 404 for a slug outside the fixed set of pages', function () {
    $this->getJson('/api/v1/public/pages/blog')->assertStatus(404);
});

it('lets an administrator update a page in both locales', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->putJson('/api/v1/admin/pages/about', [
        'title' => ['ar' => 'من نحن', 'en' => 'About us'],
        'body' => ['ar' => 'قصة الشركة', 'en' => 'Company story'],
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.body.en', 'Company story');
    $response->assertJsonPath('data.is_draft', false);
});

it('validates page updates', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->putJson('/api/v1/admin/pages/terms', []);

    $response->assertStatus(422);
    expect($response->json('errors.title'))->not->toBeNull();
    expect($response->json('errors.body'))->not->toBeNull();
});

it('does not allow admin access to an unknown page slug', function () {
    $this->actingAs($this->admin, 'sanctum')->getJson('/api/v1/admin/pages/blog')->assertStatus(404);
});

it('lets a content editor edit pages', function () {
    $editor = User::factory()->create();
    $editor->assignRole('Content editor');

    $this->actingAs($editor, 'sanctum')->getJson('/api/v1/admin/pages/about')->assertOk();
});

it('forbids sales from editing pages', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this->actingAs($sales, 'sanctum')->getJson('/api/v1/admin/pages/about')->assertStatus(403);
});
