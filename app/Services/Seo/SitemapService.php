<?php

namespace App\Services\Seo;

use App\Models\Project;
use App\Models\Service;
use App\Models\Solution;

class SitemapService
{
    private const STATIC_PAGES = [
        ['path' => '/', 'key' => 'home'],
        ['path' => '/services', 'key' => 'services'],
        ['path' => '/solutions', 'key' => 'solutions'],
        ['path' => '/work', 'key' => 'work', 'requires' => 'projects'],
        ['path' => '/about', 'key' => 'about'],
        ['path' => '/contact', 'key' => 'contact'],
        ['path' => '/privacy', 'key' => 'privacy'],
        ['path' => '/terms', 'key' => 'terms'],
    ];

    /**
     * URL data for the frontend to build sitemap.xml. Each entry carries the
     * locale-prefixed alternates so hreflang links can be emitted for both
     * Arabic and English.
     *
     * @return list<array{path: string, lastmod: ?string, alternates: array{ar: string, en: string}}>
     */
    public function entries(): array
    {
        $entries = [];

        foreach (self::STATIC_PAGES as $page) {
            if (($page['requires'] ?? null) === 'projects' && ! Project::where('is_published', true)->exists()) {
                continue;
            }

            $entries[] = $this->entry($page['path'], null);
        }

        foreach (Service::where('is_published', true)->get(['slug', 'updated_at']) as $service) {
            $entries[] = $this->entry('/services/'.$service->slug, $service->updated_at);
        }

        foreach (Solution::where('is_published', true)->get(['slug', 'updated_at']) as $solution) {
            $entries[] = $this->entry('/solutions/'.$solution->slug, $solution->updated_at);
        }

        foreach (Project::where('is_published', true)->get(['id', 'updated_at']) as $project) {
            $entries[] = $this->entry('/work/'.$project->id, $project->updated_at);
        }

        return $entries;
    }

    private function entry(string $path, ?\DateTimeInterface $updatedAt): array
    {
        return [
            'path' => $path,
            'lastmod' => $updatedAt?->format(\DateTimeInterface::ATOM),
            'alternates' => [
                'ar' => '/ar'.($path === '/' ? '' : $path),
                'en' => '/en'.($path === '/' ? '' : $path),
            ],
        ];
    }
}
