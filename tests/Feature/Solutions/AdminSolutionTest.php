<?php

use App\Models\Service;
use App\Models\Solution;
use App\Models\SolutionIndustry;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
    $this->industry = SolutionIndustry::factory()->create();
});

function validSolutionPayload(int $industryId, array $overrides = []): array
{
    return array_merge([
        'solution_industry_id' => $industryId,
        'name' => ['ar' => 'حل', 'en' => 'Solution'],
        'slug' => 'test-solution',
        'target_audience' => ['ar' => 'جمهور', 'en' => 'Audience'],
        'problem_points' => [['ar' => 'مشكلة', 'en' => 'Problem']],
        'features' => [
            'customer' => [['ar' => 'ميزة', 'en' => 'Feature']],
        ],
        'deliverables' => [['ar' => 'مخرج', 'en' => 'Deliverable']],
        'is_flagship' => false,
        'is_published' => true,
        'order' => 0,
    ], $overrides);
}

it('allows a content editor to create a solution', function () {
    $editor = User::factory()->create();
    $editor->assignRole('Content editor');

    $response = $this->actingAs($editor, 'sanctum')
        ->postJson('/api/v1/admin/solutions', validSolutionPayload($this->industry->id));

    $response->assertCreated();
    $response->assertJsonPath('data.name.en', 'Solution');
    $this->assertDatabaseHas('solutions', ['slug' => 'test-solution']);
});

it('validates solution creation input', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/solutions', []);

    $response->assertStatus(422);
    foreach (['solution_industry_id', 'name', 'slug', 'target_audience', 'problem_points', 'deliverables'] as $field) {
        expect($response->json("errors.$field"))->not->toBeNull();
    }
});

it('syncs related solutions and related services on create', function () {
    $otherSolution = Solution::factory()->create(['solution_industry_id' => $this->industry->id]);
    $service = Service::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/solutions', validSolutionPayload($this->industry->id, [
        'related_solution_ids' => [$otherSolution->id],
        'related_service_ids' => [$service->id],
    ]));

    $response->assertCreated();
    $response->assertJsonPath('data.related_solution_ids', [$otherSolution->id]);
    $response->assertJsonPath('data.related_service_ids', [$service->id]);
});

it('uploads mockup images converted to webp', function () {
    Storage::fake('public');

    $response = $this->actingAs($this->admin, 'sanctum')->post('/api/v1/admin/solutions', [
        ...validSolutionPayload($this->industry->id),
        'mockups' => [UploadedFile::fake()->image('mockup1.png', 600, 400)],
    ]);

    $response->assertCreated();
    expect($response->json('data.mockups'))->toHaveCount(1);
    expect($response->json('data.mockups.0'))->toContain('.webp');
});

it('updates a solution via post (file-upload compatible)', function () {
    $solution = Solution::factory()->create(['solution_industry_id' => $this->industry->id]);

    $response = $this->actingAs($this->admin, 'sanctum')->post("/api/v1/admin/solutions/{$solution->id}", validSolutionPayload($this->industry->id, [
        'slug' => $solution->slug,
        'is_flagship' => true,
    ]));

    $response->assertOk();
    $response->assertJsonPath('data.is_flagship', true);
});

it('reorders solutions', function () {
    $a = Solution::factory()->create(['solution_industry_id' => $this->industry->id, 'order' => 0]);
    $b = Solution::factory()->create(['solution_industry_id' => $this->industry->id, 'order' => 1]);

    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/solutions/reorder', [
        'ordered_ids' => [$b->id, $a->id],
    ]);

    $response->assertOk();
    expect($b->refresh()->order)->toBe(0);
    expect($a->refresh()->order)->toBe(1);
});

it('soft deletes a solution', function () {
    $solution = Solution::factory()->create(['solution_industry_id' => $this->industry->id]);

    $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/solutions/{$solution->id}");

    $response->assertOk();
    $this->assertSoftDeleted('solutions', ['id' => $solution->id]);
});

it('forbids sales from managing solutions', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this->actingAs($sales, 'sanctum')
        ->postJson('/api/v1/admin/solutions', validSolutionPayload($this->industry->id))
        ->assertStatus(403);
});
