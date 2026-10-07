<?php

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

function servicePayload(array $overrides = []): array
{
    return array_merge([
        'group' => 'software',
        'name' => ['ar' => 'تصميم المواقع', 'en' => 'Website design'],
        'slug' => 'website-design',
        'summary' => ['ar' => 'ملخص', 'en' => 'Short summary'],
        'description' => ['ar' => 'وصف', 'en' => 'Description'],
        'deliverables' => [['ar' => 'عنصر', 'en' => 'Item']],
        'delivery_steps' => [['ar' => 'خطوة', 'en' => 'Step']],
        'is_published' => true,
        'order' => 1,
    ], $overrides);
}

it('stores a service summary and returns it in the requested locale', function () {
    $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/services', servicePayload())->assertCreated();

    $response = $this->getJson('/api/v1/public/services/website-design', ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data.summary'))->toBe('Short summary');
});

it('returns the admin service summary in both locales', function () {
    $service = Service::factory()->create(['summary' => ['ar' => 'ملخص', 'en' => 'Summary']]);

    $response = $this->actingAs($this->admin, 'sanctum')->getJson("/api/v1/admin/services/{$service->id}");

    expect($response->json('data.summary'))->toBe(['ar' => 'ملخص', 'en' => 'Summary']);
});

it('returns a null service summary when none was set', function () {
    Service::factory()->create(['slug' => 'no-summary', 'is_published' => true, 'summary' => null]);

    $response = $this->getJson('/api/v1/public/services/no-summary');

    expect($response->json('data.summary'))->toBeNull();
});

it('rejects a service summary over 500 characters', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/services', servicePayload([
        'summary' => ['ar' => 'ملخص', 'en' => str_repeat('x', 501)],
    ]));

    $response->assertStatus(422);
    expect($response->json('errors'))->toHaveKey('summary.en');
});

function solutionPayload(int $industryId, array $overrides = []): array
{
    return array_merge([
        'solution_industry_id' => $industryId,
        'name' => ['ar' => 'حل', 'en' => 'Solution'],
        'slug' => 'restaurant-app',
        'audience' => ['ar' => 'للمطاعم', 'en' => 'For restaurants and cafés'],
        'summary' => ['ar' => 'ملخص الحل', 'en' => 'Order-ahead and delivery for restaurants.'],
        'problem_points' => [['ar' => 'مشكلة', 'en' => 'Problem']],
        'features' => ['customer' => [['ar' => 'ميزة', 'en' => 'Feature']]],
        'deliverables' => [['ar' => 'مخرج', 'en' => 'Deliverable']],
        'is_published' => true,
    ], $overrides);
}

it('returns the solution audience and summary publicly in the requested locale', function () {
    $industry = SolutionIndustry::factory()->create();
    $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/solutions', solutionPayload($industry->id))->assertCreated();

    $response = $this->getJson('/api/v1/public/solutions/restaurant-app', ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data.audience'))->toBe('For restaurants and cafés');
    expect($response->json('data.summary'))->toBe('Order-ahead and delivery for restaurants.');
});

it('returns the solution audience and summary in both locales for admins', function () {
    $solution = Solution::factory()->create(['slug' => 'admin-view']);

    $response = $this->actingAs($this->admin, 'sanctum')->getJson("/api/v1/admin/solutions/{$solution->id}");

    $response->assertOk();
    expect($response->json('data.audience'))->toBe(['ar' => 'الجمهور المستهدف', 'en' => 'Target audience']);
    expect($response->json('data.summary'))->toBeNull();
});

it('requires a solution audience', function () {
    $industry = SolutionIndustry::factory()->create();
    $payload = solutionPayload($industry->id);
    unset($payload['audience']);

    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/solutions', $payload);

    $response->assertStatus(422);
    expect($response->json('errors'))->toHaveKey('audience');
});
