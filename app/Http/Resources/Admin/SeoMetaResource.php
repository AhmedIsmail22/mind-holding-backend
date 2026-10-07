<?php

namespace App\Http\Resources\Admin;

use App\Models\SeoMeta;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SeoMeta
 */
class SeoMetaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'target' => $this->route_key ?? ($this->seoable_type ? class_basename($this->seoable_type).':'.$this->seoable_id : null),
            'title' => $this->title === null ? ['ar' => null, 'en' => null] : $this->getTranslations('title'),
            'description' => $this->description === null ? ['ar' => null, 'en' => null] : $this->getTranslations('description'),
            'share_image_url' => $this->getFirstMediaUrl('share_image', 'og') ?: null,
            'is_draft' => $this->is_draft,
        ];
    }
}
