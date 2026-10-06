<?php

namespace App\Http\Resources\Public;

use App\Support\Seo\SeoResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolutionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $resolveList = fn ($items) => collect($items)->map(fn ($item) => $item[$locale] ?? $item['en'])->values();

        $hasDemo = ! empty($this->demo_url);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'industry' => [
                'slug' => $this->industry->slug,
                'name' => $this->industry->name,
            ],
            'target_audience' => $this->target_audience,
            'is_flagship' => $this->is_flagship,
            'problem_points' => $resolveList($this->problem_points),
            'features' => [
                'customer' => $resolveList($this->features['customer'] ?? []),
                'business_owner' => $resolveList($this->features['business_owner'] ?? []),
                'staff' => $resolveList($this->features['staff'] ?? []),
            ],
            'deliverables' => $resolveList($this->deliverables),
            'mockups' => $this->getMedia('mockups')->map(fn ($media) => $media->getUrl('webp'))->values(),
            'has_demo' => $hasDemo,
            'demo_url' => $hasDemo ? $this->demo_url : null,
            'demo_credentials' => $hasDemo ? $this->demo_credentials : null,
            'faqs' => FaqResource::collection($this->whenLoaded('faqs')),
            'related_solutions' => SolutionListResource::collection($this->whenLoaded('relatedSolutions')),
            'seo' => SeoResolver::for($this->resource),
        ];
    }
}
