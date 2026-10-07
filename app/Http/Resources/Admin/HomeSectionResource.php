<?php

namespace App\Http\Resources\Admin;

use App\Models\HomeSection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin HomeSection
 */
class HomeSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->key,
            'is_enabled' => $this->is_enabled,
            'order' => $this->order,
        ];
    }
}
