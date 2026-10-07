<?php

use App\Models\Project;
use App\Models\Service;
use App\Models\Solution;
use App\Models\SolutionIndustry;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

it('lists published services and solutions with absolute bilingual alternates on the primary domain', function () {
    Service::factory()->create(['slug' => 'web-design', 'is_published' => true]);
    Service::factory()->create(['slug' => 'hidden', 'is_published' => false]);
    Solution::factory()->create([
        'solution_industry_id' => SolutionIndustry::factory()->create()->id,
        'slug' => 'clinic-booking',
        'is_published' => true,
    ]);

    $response = $this->getJson('/api/v1/public/sitemap');

    $response->assertOk();
    $urls = collect($response->json('data'))->pluck('url');

    expect($urls)->toContain('https://bitcodak.com/en/services/web-design', 'https://bitcodak.com/en/solutions/clinic-booking', 'https://bitcodak.com/ar');
    expect($urls)->not->toContain('https://bitcodak.com/en/services/hidden');

    $service = collect($response->json('data'))->firstWhere('url', 'https://bitcodak.com/en/services/web-design');
    expect($service['alternates'])->toBe(['ar' => null, 'en' => 'https://bitcodak.com/en/services/web-design']);
});

it('never emits the legacy mindholding.net domain', function () {
    Service::factory()->create(['slug' => 'web-design', 'is_published' => true]);

    expect($this->getJson('/api/v1/public/sitemap')->getContent())->not->toContain('mindholding.net');
});

it('omits the work page when no project is published, and includes it when one is', function () {
    $this->getJson('/api/v1/public/sitemap')->assertOk();
    expect(collect($this->getJson('/api/v1/public/sitemap')->json('data'))->pluck('url'))->not->toContain('https://bitcodak.com/ar/work');

    Project::factory()->create(['is_published' => true]);
    Cache::flush();

    $urls = collect($this->getJson('/api/v1/public/sitemap')->json('data'))->pluck('url');
    expect($urls)->toContain('https://bitcodak.com/ar/work');
});
