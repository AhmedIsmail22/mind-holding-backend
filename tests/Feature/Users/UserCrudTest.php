<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
});

it('allows an administrator to list users', function () {
    User::factory()->count(3)->create();

    $response = $this->actingAs($this->admin, 'sanctum')->getJson('/api/v1/admin/users');

    $response->assertOk();
    expect($response->json('meta.total'))->toBe(4); // 3 + the admin itself
});

it('allows an administrator to create a user with a role', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/users', [
        'name' => 'Sales Person',
        'email' => 'sales@mindholding.net',
        'password' => 'password123',
        'role' => 'Sales',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.role', 'Sales');

    $this->assertDatabaseHas('users', ['email' => 'sales@mindholding.net']);
});

it('validates user creation input', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/users', [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
        'role' => 'Not A Role',
    ]);

    $response->assertStatus(422);
    foreach (['name', 'email', 'password', 'role'] as $field) {
        expect($response->json("errors.$field"))->not->toBeNull();
    }
});

it('allows an administrator to update a user and change their role', function () {
    $editor = User::factory()->create();
    $editor->assignRole('Content editor');

    $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/v1/admin/users/{$editor->id}", [
        'name' => $editor->name,
        'email' => $editor->email,
        'role' => 'Sales',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.role', 'Sales');
});

it('allows an administrator to soft delete a user', function () {
    $editor = User::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/users/{$editor->id}");

    $response->assertOk();
    $this->assertSoftDeleted('users', ['id' => $editor->id]);
});

it('prevents an administrator from deleting their own account', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/users/{$this->admin->id}");

    $response->assertStatus(422);
    $this->assertDatabaseHas('users', ['id' => $this->admin->id, 'deleted_at' => null]);
});

it('forbids a content editor from managing users', function () {
    $editor = User::factory()->create();
    $editor->assignRole('Content editor');

    $response = $this->actingAs($editor, 'sanctum')->getJson('/api/v1/admin/users');

    $response->assertStatus(403);
});

it('forbids sales from managing users', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $response = $this->actingAs($sales, 'sanctum')->postJson('/api/v1/admin/users', [
        'name' => 'X',
        'email' => 'x@mindholding.net',
        'password' => 'password123',
        'role' => 'Sales',
    ]);

    $response->assertStatus(403);
});
