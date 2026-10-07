<?php

use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

it('lets a deleted project slug be reused by a new project', function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('Administrator');

    $payload = [
        'slug' => 'reused-slug',
        'client_name' => ['ar' => 'عميل', 'en' => 'Client'],
        'overview' => ['ar' => 'م', 'en' => 'O'],
        'challenge' => ['ar' => 'م', 'en' => 'C'],
        'solution' => ['ar' => 'م', 'en' => 'S'],
        'technologies' => ['Laravel'],
    ];

    $first = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/projects', $payload);
    $first->assertCreated();

    $this->actingAs($admin, 'sanctum')->deleteJson('/api/v1/admin/projects/'.$first->json('data.id'))->assertOk();

    $second = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/projects', $payload);
    $second->assertCreated();

    expect(Project::where('slug', 'reused-slug')->count())->toBe(1);
});
