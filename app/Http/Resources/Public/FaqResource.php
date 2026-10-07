<?php

namespace App\Http\Resources\Public;

use App\Models\Faq;
use App\Support\Translations\OptionalTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Faq
 */
class FaqResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question' => OptionalTranslation::text($this->resource, 'question'),
            'answer' => OptionalTranslation::text($this->resource, 'answer'),
        ];
    }
}
