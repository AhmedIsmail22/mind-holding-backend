<?php

namespace App\Services\Seo;

use App\Models\Page;
use App\Models\Project;
use App\Models\Service;
use App\Models\Solution;
use DateTimeInterface;

class SitemapService
{
    private const FIXED_PAGES = [
        ['path' => '/', 'key' => 'home'],
        ['path' => '/services', 'key' => 'services'],
        ['path' => '/solutions', 'key' => 'solutions'],
        ['path' => '/work', 'key' => 'work', 'requires' => 'projects'],
        ['path' => '/contact', 'key' => 'contact'],
    ];

    /**
     * URL data for the frontend to build sitemap.xml. Alternates are the
     * localized paths for hreflang. A locale is null when that locale has no
     * slug. Fixed route pages always exist in both locales.
     *
     * @return list<array{path: string, lastmod: ?string, alternates: array{ar: ?string, en: ?string}}>
     */
    public function entries(): array
    {
        $entries = [];

        foreach (self::FIXED_PAGES as $page) {
            if (($page['requires'] ?? null) === 'projects' && ! Project::where('is_published', true)->exists()) {
                continue;
            }

            $entries[] = [
                'path' => $page['path'],
                'lastmod' => null,
                'alternates' => [
                    'ar' => '/ar'.($page['path'] === '/' ? '' : $page['path']),
                    'en' => '/en'.($page['path'] === '/' ? '' : $page['path']),
                ],
            ];
        }

        foreach (Page::get(['slug', 'slug_ar', 'updated_at']) as $page) {
            $entries[] = $this->entry('/'.$page->slug, $page->slug_ar, $page->slug, '', $page->updated_at);
        }

        foreach (Service::where('is_published', true)->get(['slug', 'slug_ar', 'updated_at']) as $service) {
            $entries[] = $this->entry('/services/'.$service->slug, $service->slug_ar, $service->slug, 'services/', $service->updated_at);
        }

        foreach (Solution::where('is_published', true)->get(['slug', 'slug_ar', 'updated_at']) as $solution) {
            $entries[] = $this->entry('/solutions/'.$solution->slug, $solution->slug_ar, $solution->slug, 'solutions/', $solution->updated_at);
        }

        foreach (Project::where('is_published', true)->get(['id', 'slug', 'slug_ar', 'updated_at']) as $project) {
            $entries[] = $this->entry('/work/'.$project->id, $project->slug_ar, $project->slug, 'work/', $project->updated_at);
        }

        return $entries;
    }

    /** @return array{path: string, lastmod: ?string, alternates: array{ar: ?string, en: ?string}} */
    private function entry(string $path, ?string $slugAr, ?string $slugEn, string $segment, ?DateTimeInterface $updatedAt): array
    {
        return [
            'path' => $path,
            'lastmod' => $updatedAt?->format(DateTimeInterface::ATOM),
            'alternates' => [
                'ar' => $this->localized('/ar/', $segment, $slugAr),
                'en' => $this->localized('/en/', $segment, $slugEn),
            ],
        ];
    }

    private function localized(string $prefix, string $segment, ?string $slug): ?string
    {
        return $slug === null || $slug === '' ? null : $prefix.$segment.$slug;
    }
}
