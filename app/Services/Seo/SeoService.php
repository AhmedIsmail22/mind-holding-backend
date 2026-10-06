<?php

namespace App\Services\Seo;

use App\DTOs\Seo\UpdateSeoData;
use App\Models\Page;
use App\Models\SeoMeta;
use App\Models\Service;
use App\Models\Solution;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class SeoService
{
    /**
     * Resolves the SEO row for an admin target. Creates an unsaved one if the
     * target has none yet. Types: service|solution|page (by id, id, slug) or
     * route (by a fixed key from config/seo.php).
     */
    public function targetFor(string $type, string $key): SeoMeta
    {
        return match ($type) {
            'service' => Service::findOrFail($key)->seo()->firstOrNew(),
            'solution' => Solution::findOrFail($key)->seo()->firstOrNew(),
            'page' => Page::where('slug', $key)->firstOrFail()->seo()->firstOrNew(),
            'route' => $this->routeTarget($key),
            default => throw new ModelNotFoundException("Unknown SEO target type [{$type}]."),
        };
    }

    public function findRoute(string $key): SeoMeta
    {
        return SeoMeta::where('route_key', $key)->firstOrFail();
    }

    public function update(SeoMeta $meta, UpdateSeoData $data): SeoMeta
    {
        return DB::transaction(function () use ($meta, $data) {
            $meta->setTranslations('title', $data->title);
            $meta->setTranslations('description', $data->description);
            $meta->is_draft = false;
            $meta->save();

            if ($data->shareImage !== null) {
                $meta->clearMediaCollection('share_image');
                $meta->addMedia($data->shareImage)->toMediaCollection('share_image');
            }

            return $meta->refresh();
        });
    }

    private function routeTarget(string $key): SeoMeta
    {
        if (! in_array($key, config('seo.route_keys'), true)) {
            throw new ModelNotFoundException("Unknown route key [{$key}].");
        }

        return SeoMeta::firstOrNew(['route_key' => $key]);
    }
}
