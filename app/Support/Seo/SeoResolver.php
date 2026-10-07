<?php

namespace App\Support\Seo;

use App\Models\Page;
use App\Models\Project;
use App\Models\SeoMeta;
use App\Models\Service;
use App\Models\Solution;
use App\Support\Media\MediaAsset;
use Illuminate\Database\Eloquent\Model;

final class SeoResolver
{
    private const ROUTE_PATHS = [
        'home' => '/',
        'services' => '/services',
        'solutions' => '/solutions',
        'work' => '/work',
        'contact' => '/contact',
    ];

    /**
     * Resolved SEO for the current locale. Falls back to the entity's own
     * name and description when no SEO row exists, so every page has a title.
     *
     * @return array{title: ?string, description: ?string, share_image_url: ?string, canonical_url: ?string}
     */
    public static function for(Model $entity): array
    {
        $canonical = self::canonicalUrl($entity);
        $meta = $entity->seo;

        if ($meta !== null) {
            return self::fromMeta($meta, $canonical);
        }

        return match (true) {
            $entity instanceof Service => ['title' => $entity->name, 'description' => $entity->description, 'share_image_url' => null, 'share_image' => null, 'canonical_url' => $canonical],
            $entity instanceof Solution => ['title' => $entity->name, 'description' => $entity->audience, 'share_image_url' => null, 'share_image' => null, 'canonical_url' => $canonical],
            $entity instanceof Page => ['title' => $entity->title, 'description' => null, 'share_image_url' => null, 'share_image' => null, 'canonical_url' => $canonical],
            default => ['title' => null, 'description' => null, 'share_image_url' => null, 'share_image' => null, 'canonical_url' => $canonical],
        };
    }

    public static function forRouteKey(SeoMeta $meta): array
    {
        $path = self::ROUTE_PATHS[$meta->route_key] ?? null;

        return self::fromMeta($meta, $path === null ? null : PublicUrl::localized(app()->getLocale(), $path));
    }

    /** Canonical absolute URL for the current locale, using that locale's slug. */
    public static function canonicalUrl(Model $entity): ?string
    {
        $locale = app()->getLocale();
        $localizedSlug = fn (?string $ar, ?string $en): ?string => $locale === 'ar' && filled($ar) ? $ar : (filled($en) ? $en : null);

        $path = match (true) {
            $entity instanceof Service => ($slug = $localizedSlug($entity->slug_ar, $entity->slug)) ? '/services/'.$slug : null,
            $entity instanceof Solution => ($slug = $localizedSlug($entity->slug_ar, $entity->slug)) ? '/solutions/'.$slug : null,
            $entity instanceof Page => ($slug = $localizedSlug($entity->slug_ar, $entity->slug)) ? '/'.$slug : null,
            $entity instanceof Project => ($slug = $localizedSlug($entity->slug_ar, $entity->slug)) ? '/work/'.$slug : null,
            default => null,
        };

        return $path === null ? null : PublicUrl::localized($locale, $path);
    }

    private static function fromMeta(SeoMeta $meta, ?string $canonical): array
    {
        $image = $meta->getFirstMediaUrl('share_image', 'og');

        return [
            'title' => $meta->title,
            'description' => $meta->description,
            'share_image_url' => $image === '' ? null : self::absolute($image),
            'share_image' => MediaAsset::present($meta->getFirstMedia('share_image'), 'og'),
            'canonical_url' => $canonical,
        ];
    }

    private static function absolute(string $url): string
    {
        return str_starts_with($url, 'http') ? $url : PublicUrl::base().'/'.ltrim($url, '/');
    }
}
