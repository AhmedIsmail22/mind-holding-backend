<?php

use App\Models\Redirect;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
    $this->editor = User::factory()->create();
    $this->editor->assignRole('Content editor');
});

it('lets an administrator create a redirect that resolves with a 301', function () {
    $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => '/old-services/web-design/',
        'new_path' => '/services/website-design-development',
    ])->assertCreated();

    $response = $this->getJson('/api/v1/public/redirects/resolve?path=/old-services/web-design');

    $response->assertOk();
    expect($response->json('data'))->toBe([
        'from' => '/old-services/web-design',
        'to' => '/services/website-design-development',
        'status' => 301,
    ]);
});

it('returns 404 when no redirect matches the path', function () {
    $this->getJson('/api/v1/public/redirects/resolve?path=/nothing-here')->assertStatus(404);
});

it('requires a path to resolve', function () {
    $this->getJson('/api/v1/public/redirects/resolve')->assertStatus(422);
});

it('rejects a duplicate active old path', function () {
    Redirect::create(['old_path' => '/a', 'new_path' => '/b']);

    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => '/a',
        'new_path' => '/c',
    ]);

    $response->assertStatus(422);
    expect($response->json('errors.old_path'))->not->toBeNull();
});

it('rejects a redirect that points to itself', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => '/same',
        'new_path' => '/same',
    ]);

    $response->assertStatus(422);
    expect($response->json('errors.new_path'))->not->toBeNull();
});

it('rejects a full URL instead of a path', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => 'https://mindholding.net/old',
        'new_path' => '/new',
    ]);

    $response->assertStatus(422);
});

it('stops resolving a soft-deleted redirect and allows its path to be reused', function () {
    $redirect = Redirect::create(['old_path' => '/gone', 'new_path' => '/old-target']);

    $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/redirects/{$redirect->id}")->assertOk();
    $this->getJson('/api/v1/public/redirects/resolve?path=/gone')->assertStatus(404);

    $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => '/gone',
        'new_path' => '/new-target',
    ])->assertCreated();
});

it('lists active redirects publicly', function () {
    Redirect::create(['old_path' => '/one', 'new_path' => '/uno']);
    Redirect::create(['old_path' => '/two', 'new_path' => '/dos']);

    $response = $this->getJson('/api/v1/public/redirects');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('data.0.status'))->toBe(301);
});

it('forbids a content editor from managing redirects', function () {
    $this->actingAs($this->editor, 'sanctum')->getJson('/api/v1/admin/redirects')->assertStatus(403);
});
