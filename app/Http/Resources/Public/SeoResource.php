<?php

namespace App\Http\Resources\Public;

use App\Models\SeoMeta;
use App\Support\Seo\SeoResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SeoMeta
 */
class SeoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return SeoResolver::forRouteKey($this->resource);
    }
}
