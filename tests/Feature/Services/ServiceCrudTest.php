<?php

use App\Models\Project;
use App\Models\Service;
use App\Models\Solution;
use App\Models\SolutionIndustry;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
});

function validServicePayload(array $overrides = []): array
{
    return array_merge([
        'group' => 'software',
        'name' => ['ar' => 'تصميم المواقع', 'en' => 'Website design'],
        'slug' => 'website-design',
        'description' => ['ar' => 'وصف', 'en' => 'Description'],
        'deliverables' => [['ar' => 'عنصر', 'en' => 'Item']],
        'delivery_steps' => [['ar' => 'خطوة', 'en' => 'Step']],
        'is_published' => true,
        'order' => 1,
    ], $overrides);
}

it('lists published services grouped by software/marketing publicly', function () {
    Service::factory()->create(['group' => 'software', 'is_published' => true]);
    Service::factory()->create(['group' => 'marketing', 'is_published' => true]);
    Service::factory()->create(['group' => 'software', 'is_published' => false]);

    $response = $this->getJson('/api/v1/public/services');

    $response->assertOk();
    expect($response->json('data.software'))->toHaveCount(1);
    expect($response->json('data.marketing'))->toHaveCount(1);
});

it('shows a published service by slug with resolved locale content', function () {
    Service::factory()->create([
        'slug' => 'website-design',
        'is_published' => true,
        'deliverables' => [['ar' => 'عنصر', 'en' => 'Item one']],
        'delivery_steps' => [['ar' => 'خطوة', 'en' => 'Step one']],
    ]);

    $response = $this->getJson('/api/v1/public/services/website-design', ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data.deliverables.0'))->toBe('Item one');
    expect($response->json('data.delivery_steps.0'))->toBe('Step one');
});

it('returns 404 for an unpublished service on the public endpoint', function () {
    Service::factory()->create(['slug' => 'draft-service', 'is_published' => false]);

    $response = $this->getJson('/api/v1/public/services/draft-service');

    $response->assertStatus(404);
});

it('allows a content editor to create a service', function () {
    $editor = User::factory()->create();
    $editor->assignRole('Content editor');

    $response = $this->actingAs($editor, 'sanctum')->postJson('/api/v1/admin/services', validServicePayload());

    $response->assertCreated();
    $response->assertJsonPath('data.name.en', 'Website design');
    $this->assertDatabaseHas('services', ['slug' => 'website-design']);
});

it('validates service creation input', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/services', []);

    $response->assertStatus(422);
    foreach (['group', 'name', 'slug', 'description', 'deliverables', 'delivery_steps'] as $field) {
        expect($response->json("errors.$field"))->not->toBeNull();
    }
});

it('allows updating and unpublishing a service', function () {
    $service = Service::factory()->create(['is_published' => true]);

    $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/v1/admin/services/{$service->id}", validServicePayload([
        'slug' => $service->slug,
        'is_published' => false,
    ]));

    $response->assertOk();
    $response->assertJsonPath('data.is_published', false);
});

it('soft deletes a service', function () {
    $service = Service::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/services/{$service->id}");

    $response->assertOk();
    $this->assertSoftDeleted('services', ['id' => $service->id]);
});

it('forbids sales from managing services', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this->actingAs($sales, 'sanctum')->postJson('/api/v1/admin/services', validServicePayload())->assertStatus(403);
});

it('shows related published solutions and projects on the public service detail', function () {
    $service = Service::factory()->create(['slug' => 'website-design', 'is_published' => true]);
    $industry = SolutionIndustry::factory()->create();
    $solution = Solution::factory()->create(['solution_industry_id' => $industry->id, 'is_published' => true]);
    $project = Project::factory()->create(['is_published' => true]);

    $service->solutions()->sync([$solution->id]);
    $service->projects()->sync([$project->id]);

    $response = $this->getJson('/api/v1/public/services/website-design');

    $response->assertOk();
    expect($response->json('data.related_solutions'))->toHaveCount(1);
    expect($response->json('data.related_projects'))->toHaveCount(1);
});
