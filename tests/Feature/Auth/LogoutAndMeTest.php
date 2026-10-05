<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('returns the authenticated admin profile with role', function () {
    $user = User::factory()->create();
    $user->assignRole('Sales');

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/admin/auth/me');

    $response->assertOk();
    $response->assertJsonPath('data.email', $user->email);
    $response->assertJsonPath('data.role', 'Sales');
});

it('rejects me without authentication', function () {
    $response = $this->getJson('/api/v1/admin/auth/me');

    $response->assertStatus(401);
});

it('revokes the current token on logout', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);
    $user->assignRole('Administrator');

    $login = $this->postJson('/api/v1/admin/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $token = $login->json('data.token');

    expect($user->tokens()->count())->toBe(1);

    $logout = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/v1/admin/auth/logout');

    $logout->assertOk();

    // The token row is gone from the DB (this is what actually matters —
    // a follow-up simulated request re-using Sanctum's cached guard
    // instance within the same test process would still "pass" even for a
    // genuinely revoked token, which is a Laravel test-harness artifact,
    // not something that happens across real separate requests).
    expect($user->tokens()->count())->toBe(0);
});
