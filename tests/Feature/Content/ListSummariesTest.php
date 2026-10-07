<?php

use App\Models\Service;
use App\Models\Solution;
use App\Models\SolutionIndustry;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

it('includes the summary in the public service list', function () {
    Service::factory()->create([
        'slug' => 'mobile-apps',
        'group' => 'software',
        'is_published' => true,
        'summary' => ['ar' => 'تطبيقات', 'en' => 'Apps from idea to store.'],
    ]);

    $response = $this->getJson('/api/v1/public/services', ['Accept-Language' => 'en']);

    expect($response->json('data.software.0.summary'))->toBe('Apps from idea to store.');
});

it('includes the audience and summary in the public solution list', function () {
    Solution::factory()->create([
        'solution_industry_id' => SolutionIndustry::factory()->create()->id,
        'is_published' => true,
        'audience' => ['ar' => 'للمطاعم', 'en' => 'Restaurants'],
        'summary' => ['ar' => 'ملخص', 'en' => 'Order ahead.'],
    ]);

    $response = $this->getJson('/api/v1/public/solutions', ['Accept-Language' => 'en']);

    expect($response->json('data.0.audience'))->toBe('Restaurants');
    expect($response->json('data.0.summary'))->toBe('Order ahead.');
});

it('returns a null summary in the list when none is set', function () {
    Service::factory()->create(['slug' => 'no-summary', 'group' => 'software', 'is_published' => true, 'summary' => null]);

    $response = $this->getJson('/api/v1/public/services');

    expect($response->json('data.software.0.summary'))->toBeNull();
});

it('includes the summary in the public project list and admin editing', function () {
    $admin = User::factory()->create();
    $this->seed(RolesAndPermissionsSeeder::class);
    $admin->assignRole('Administrator');

    $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/projects', [
        'slug' => 'summary-project',
        'client_name' => ['ar' => 'عميل', 'en' => 'Client'],
        'summary' => ['ar' => 'ملخص', 'en' => 'Project summary'],
        'overview' => ['ar' => 'م', 'en' => 'O'],
        'challenge' => ['ar' => 'م', 'en' => 'C'],
        'solution' => ['ar' => 'م', 'en' => 'S'],
        'technologies' => ['Laravel'],
        'is_published' => true,
    ])->assertCreated();

    $list = $this->getJson('/api/v1/public/projects', ['Accept-Language' => 'en']);
    expect($list->json('data.0.summary'))->toBe('Project summary');

    $admin = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/admin/projects');
    expect($admin->json('data.0.summary'))->toBe(['ar' => 'ملخص', 'en' => 'Project summary']);
});
