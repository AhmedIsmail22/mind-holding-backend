<?php

namespace App\Models;

use App\Models\Concerns\ReleasesSlugOnDelete;
use App\Support\Seo\InvalidatesSitemap;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Solution extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia, InvalidatesSitemap, ReleasesSlugOnDelete, SoftDeletes;

    public array $translatable = ['name', 'audience', 'summary'];

    protected $fillable = [
        'solution_industry_id',
        'name',
        'slug',
        'slug_ar',
        'audience',
        'summary',
        'problem_points',
        'features',
        'deliverables',
        'demo_url',
        'demo_credentials',
        'is_flagship',
        'is_published',
        'is_draft',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'problem_points' => 'array',
            'features' => 'array',
            'deliverables' => 'array',
            'is_flagship' => 'boolean',
            'is_published' => 'boolean',
            'is_draft' => 'boolean',
        ];
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(SolutionIndustry::class, 'solution_industry_id');
    }

    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable');
    }

    public function relatedSolutions(): BelongsToMany
    {
        return $this->belongsToMany(Solution::class, 'related_solutions', 'solution_id', 'related_solution_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_solution');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('thumb')
            ->format('webp')
            ->width(400)
            ->nonQueued();
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }
}
