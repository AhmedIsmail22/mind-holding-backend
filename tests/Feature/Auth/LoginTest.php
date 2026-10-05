<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('logs in with valid credentials and returns a token', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);
    $user->assignRole('Administrator');

    $response = $this->postJson('/api/v1/admin/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertOk();
    $response->assertJsonPath('success', true);
    expect($response->json('data.token'))->not->toBeEmpty();
    expect($response->json('data.user.role'))->toBe('Administrator');
});

it('rejects an unknown email', function () {
    $response = $this->postJson('/api/v1/admin/auth/login', [
        'email' => 'nobody@mindholding.net',
        'password' => 'whatever123',
    ]);

    $response->assertStatus(422);
    expect($response->json('success'))->toBeFalse();
});

it('rejects a wrong password', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $response = $this->postJson('/api/v1/admin/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422);
});

it('validates required fields', function () {
    $response = $this->postJson('/api/v1/admin/auth/login', []);

    $response->assertStatus(422);
    expect($response->json('errors.email'))->not->toBeNull();
    expect($response->json('errors.password'))->not->toBeNull();
});

it('locks out after too many failed attempts from the same email and ip', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/v1/admin/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->postJson('/api/v1/admin/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(422);
    expect($response->json('errors.email.0'))->toContain('Too many login attempts');
});
