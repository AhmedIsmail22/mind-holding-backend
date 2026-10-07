<?php

namespace App\Http\Resources\Public;

use App\Models\Page;
use App\Support\Seo\SeoResolver;
use App\Support\Translations\OptionalTranslation;
use App\Support\Translations\SlugAlternates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Page
 */
class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'alternates' => SlugAlternates::for($this->resource),
            'title' => OptionalTranslation::text($this->resource, 'title'),
            'body' => OptionalTranslation::text($this->resource, 'body'),
            'seo' => SeoResolver::for($this->resource),
        ];
    }
}
