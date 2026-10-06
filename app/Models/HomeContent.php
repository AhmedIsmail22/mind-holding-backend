<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class HomeContent extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $table = 'home_content';

    public array $translatable = ['hero_headline', 'hero_subheadline', 'closing_cta_headline', 'closing_cta_subheadline'];

    protected $fillable = [
        'hero_headline',
        'hero_subheadline',
        'stats',
        'differentiators',
        'process_steps',
        'closing_cta_headline',
        'closing_cta_subheadline',
        'is_draft',
    ];

    protected function casts(): array
    {
        return [
            'stats' => 'array',
            'differentiators' => 'array',
            'process_steps' => 'array',
            'is_draft' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return self::query()->firstOrFail();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->nonQueued();
    }
}
