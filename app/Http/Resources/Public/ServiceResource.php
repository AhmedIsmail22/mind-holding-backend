<?php

namespace App\Http\Resources\Public;

use App\Support\Seo\SeoResolver;
use App\Support\Translations\OptionalTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'group' => $this->group,
            'name' => $this->name,
            'summary' => OptionalTranslation::current($this->resource, 'summary'),
            'slug' => $this->slug,
            'description' => $this->description,
            'deliverables' => collect($this->deliverables)->map(fn ($item) => $item[$locale] ?? $item['en'])->values(),
            'delivery_steps' => collect($this->delivery_steps)->map(fn ($item) => $item[$locale] ?? $item['en'])->values(),
            'faqs' => FaqResource::collection($this->whenLoaded('faqs')),
            'related_solutions' => SolutionListResource::collection($this->whenLoaded('solutions')),
            'related_projects' => ProjectListResource::collection($this->whenLoaded('projects')),
            'seo' => SeoResolver::for($this->resource),
        ];
    }
}
