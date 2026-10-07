<?php

namespace App\Services\Seo;

use App\Models\Page;
use App\Models\Project;
use App\Models\Service;
use App\Models\Solution;
use App\Support\Seo\PublicUrl;
use DateTimeInterface;

class SitemapService
{
    private const FIXED_PAGES = [
        ['path' => '/', 'requires' => null],
        ['path' => '/services', 'requires' => null],
        ['path' => '/solutions', 'requires' => null],
        ['path' => '/work', 'requires' => 'projects'],
        ['path' => '/contact', 'requires' => null],
    ];

    /**
     * Absolute URLs on the primary domain for sitemap.xml. `url` is the Arabic
     * version when it exists, otherwise the English one. `alternates` are the
     * hreflang URLs, and a locale is null when it has no URL.
     *
     * @return list<array{url: ?string, lastmod: ?string, alternates: array{ar: ?string, en: ?string}}>
     */
    public function entries(): array
    {
        $entries = [];

        foreach (self::FIXED_PAGES as $page) {
            if ($page['requires'] === 'projects' && ! Project::where('is_published', true)->exists()) {
                continue;
            }

            // Fixed route pages exist in both locales (SRS section 3).
            $entries[] = $this->build(
                PublicUrl::localized('ar', $page['path']),
                PublicUrl::localized('en', $page['path']),
                null,
            );
        }

        foreach (Page::get(['slug', 'slug_ar', 'updated_at']) as $page) {
            $entries[] = $this->fromSlugs('/', $page->slug_ar, $page->slug, $page->updated_at);
        }

        foreach (Service::where('is_published', true)->get(['slug', 'slug_ar', 'updated_at']) as $service) {
            $entries[] = $this->fromSlugs('/services/', $service->slug_ar, $service->slug, $service->updated_at);
        }

        foreach (Solution::where('is_published', true)->get(['slug', 'slug_ar', 'updated_at']) as $solution) {
            $entries[] = $this->fromSlugs('/solutions/', $solution->slug_ar, $solution->slug, $solution->updated_at);
        }

        // Project pages are served by id in both locales.
        foreach (Project::where('is_published', true)->get(['id', 'updated_at']) as $project) {
            $path = '/work/'.$project->id;
            $entries[] = $this->build(PublicUrl::localized('ar', $path), PublicUrl::localized('en', $path), $project->updated_at);
        }

        return $entries;
    }

    private function fromSlugs(string $prefix, ?string $slugAr, ?string $slugEn, ?DateTimeInterface $updatedAt): array
    {
        $ar = filled($slugAr) ? PublicUrl::localized('ar', $prefix.$slugAr) : null;
        $en = filled($slugEn) ? PublicUrl::localized('en', $prefix.$slugEn) : null;

        return $this->build($ar, $en, $updatedAt);
    }

    private function build(?string $ar, ?string $en, ?DateTimeInterface $updatedAt): array
    {
        return [
            'url' => $ar ?? $en,
            'lastmod' => $updatedAt?->format(DateTimeInterface::ATOM),
            'alternates' => ['ar' => $ar, 'en' => $en],
        ];
    }
}
