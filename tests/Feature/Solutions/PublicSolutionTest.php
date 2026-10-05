<?php

use App\Models\Solution;
use App\Models\SolutionIndustry;

it('lists published solutions with flagship first', function () {
    $industry = SolutionIndustry::factory()->create(['slug' => 'commerce']);

    Solution::factory()->create(['solution_industry_id' => $industry->id, 'is_flagship' => false, 'order' => 0]);
    Solution::factory()->create(['solution_industry_id' => $industry->id, 'is_flagship' => true, 'order' => 1]);

    $response = $this->getJson('/api/v1/public/solutions');

    $response->assertOk();
    expect($response->json('data.0.is_flagship'))->toBeTrue();
});

it('filters solutions by industry slug', function () {
    $commerce = SolutionIndustry::factory()->create(['slug' => 'commerce']);
    $logistics = SolutionIndustry::factory()->create(['slug' => 'logistics']);

    Solution::factory()->create(['solution_industry_id' => $commerce->id]);
    Solution::factory()->create(['solution_industry_id' => $logistics->id]);

    $response = $this->getJson('/api/v1/public/solutions?industry=logistics');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.industry.slug'))->toBe('logistics');
});

it('hides unpublished solutions from the public list', function () {
    Solution::factory()->create(['is_published' => false]);

    $response = $this->getJson('/api/v1/public/solutions');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(0);
});

it('shows a published solution detail with resolved locale content', function () {
    $solution = Solution::factory()->create([
        'slug' => 'ecommerce-store',
        'is_published' => true,
        'demo_url' => null,
        'problem_points' => [['ar' => 'م', 'en' => 'Problem one']],
        'features' => ['customer' => [['ar' => 'م', 'en' => 'Feature one']], 'business_owner' => [], 'staff' => []],
        'deliverables' => [['ar' => 'م', 'en' => 'Deliverable one']],
    ]);

    $response = $this->getJson('/api/v1/public/solutions/ecommerce-store', ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data.problem_points.0'))->toBe('Problem one');
    expect($response->json('data.features.customer.0'))->toBe('Feature one');
    expect($response->json('data.deliverables.0'))->toBe('Deliverable one');
    expect($response->json('data.has_demo'))->toBeFalse();
    expect($response->json('data.demo_url'))->toBeNull();
});

it('exposes the demo url and credentials only when a demo exists', function () {
    $solution = Solution::factory()->create([
        'slug' => 'with-demo',
        'is_published' => true,
        'demo_url' => 'https://demo.mindholding.net/ecommerce',
        'demo_credentials' => 'user: demo / pass: demo123',
    ]);

    $response = $this->getJson('/api/v1/public/solutions/with-demo');

    $response->assertOk();
    expect($response->json('data.has_demo'))->toBeTrue();
    expect($response->json('data.demo_url'))->toBe('https://demo.mindholding.net/ecommerce');
    expect($response->json('data.demo_credentials'))->toBe('user: demo / pass: demo123');
});

it('returns 404 for an unpublished solution on the public endpoint', function () {
    Solution::factory()->create(['slug' => 'draft-solution', 'is_published' => false]);

    $response = $this->getJson('/api/v1/public/solutions/draft-solution');

    $response->assertStatus(404);
});
