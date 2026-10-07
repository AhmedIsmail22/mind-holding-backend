<?php

use App\Models\Service;
use Database\Seeders\SeoRouteSeeder;

it('returns a canonical URL on the primary domain in the requested locale', function () {
    $this->seed(SeoRouteSeeder::class);
    Service::factory()->create(['slug' => 'web-design', 'slug_ar' => 'تصميم-المواقع', 'is_published' => true]);

    $en = $this->getJson('/api/v1/public/services/web-design', ['Accept-Language' => 'en']);
    expect($en->json('data.seo.canonical_url'))->toBe('https://bitcodak.com/en/services/web-design');

    $ar = $this->getJson('/api/v1/public/services/web-design', ['Accept-Language' => 'ar']);
    expect($ar->json('data.seo.canonical_url'))->toBe('https://bitcodak.com/ar/services/'.rawurlencode('تصميم-المواقع'));
});

it('returns canonical URLs for route pages and never the legacy domain', function () {
    $this->seed(SeoRouteSeeder::class);

    $response = $this->getJson('/api/v1/public/seo/services', ['Accept-Language' => 'en']);

    expect($response->json('data.canonical_url'))->toBe('https://bitcodak.com/en/services');
    expect($response->getContent())->not->toContain('mindholding.net');
});
