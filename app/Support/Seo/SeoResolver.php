<?php

namespace App\Support\Seo;

use App\Models\Page;
use App\Models\SeoMeta;
use App\Models\Service;
use App\Models\Solution;
use Illuminate\Database\Eloquent\Model;

final class SeoResolver
{
    /**
     * Resolved SEO for the current locale. Falls back to the entity's own
     * name and description when no SEO row exists, so every page has a title.
     *
     * @return array{title: ?string, description: ?string, share_image_url: ?string}
     */
    public static function for(Model $entity): array
    {
        $meta = $entity->seo;

        if ($meta !== null) {
            return self::fromMeta($meta);
        }

        return match (true) {
            $entity instanceof Service => ['title' => $entity->name, 'description' => $entity->description, 'share_image_url' => null],
            $entity instanceof Solution => ['title' => $entity->name, 'description' => $entity->audience, 'share_image_url' => null],
            $entity instanceof Page => ['title' => $entity->title, 'description' => null, 'share_image_url' => null],
            default => ['title' => null, 'description' => null, 'share_image_url' => null],
        };
    }

    public static function forRouteKey(SeoMeta $meta): array
    {
        return self::fromMeta($meta);
    }

    private static function fromMeta(SeoMeta $meta): array
    {
        $image = $meta->getFirstMediaUrl('share_image', 'og');

        return [
            'title' => $meta->title,
            'description' => $meta->description,
            'share_image_url' => $image === '' ? null : self::absolute($image),
        ];
    }

    private static function absolute(string $url): string
    {
        return str_starts_with($url, 'http') ? $url : rtrim(config('seo.site_url'), '/').'/'.ltrim($url, '/');
    }
}
