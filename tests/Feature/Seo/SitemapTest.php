<?php

use App\Models\Project;
use App\Models\Service;
use App\Models\Solution;
use App\Models\SolutionIndustry;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

it('lists published services and solutions with bilingual alternates', function () {
    Service::factory()->create(['slug' => 'web-design', 'is_published' => true]);
    Service::factory()->create(['slug' => 'hidden', 'is_published' => false]);
    Solution::factory()->create([
        'solution_industry_id' => SolutionIndustry::factory()->create()->id,
        'slug' => 'clinic-booking',
        'is_published' => true,
    ]);

    $response = $this->getJson('/api/v1/public/sitemap');

    $response->assertOk();
    $paths = collect($response->json('data'))->pluck('path');

    expect($paths)->toContain('/services/web-design', '/solutions/clinic-booking', '/');
    expect($paths)->not->toContain('/services/hidden');

    $service = collect($response->json('data'))->firstWhere('path', '/services/web-design');
    expect($service['alternates'])->toBe(['ar' => null, 'en' => '/en/services/web-design']);
});

it('omits the work page when no project is published, and includes it when one is', function () {
    $this->getJson('/api/v1/public/sitemap')->assertOk();
    expect(collect($this->getJson('/api/v1/public/sitemap')->json('data'))->pluck('path'))->not->toContain('/work');

    Project::factory()->create(['is_published' => true]);
    Cache::flush();

    $paths = collect($this->getJson('/api/v1/public/sitemap')->json('data'))->pluck('path');
    expect($paths)->toContain('/work');
});
