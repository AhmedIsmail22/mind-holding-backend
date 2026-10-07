<?php

namespace App\Http\Resources\Admin;

use App\Models\Page;
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
            'slug_ar' => $this->slug_ar,
            'title' => $this->getTranslations('title'),
            'body' => $this->getTranslations('body'),
            'is_draft' => $this->is_draft,
        ];
    }
}
