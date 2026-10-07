<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    public array $translatable = ['name', 'summary', 'description'];

    protected $fillable = [
        'group',
        'name',
        'summary',
        'slug',
        'slug_ar',
        'description',
        'deliverables',
        'delivery_steps',
        'is_published',
        'is_draft',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'deliverables' => 'array',
            'delivery_steps' => 'array',
            'is_published' => 'boolean',
            'is_draft' => 'boolean',
        ];
    }

    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable');
    }

    public function solutions(): BelongsToMany
    {
        return $this->belongsToMany(Solution::class, 'service_solution');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'service_project');
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }
}
