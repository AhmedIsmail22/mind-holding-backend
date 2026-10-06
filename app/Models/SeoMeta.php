<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class SeoMeta extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $table = 'seo_meta';

    public array $translatable = ['title', 'description'];

    protected $fillable = ['seoable_type', 'seoable_id', 'route_key', 'title', 'description', 'is_draft'];

    protected function casts(): array
    {
        return [
            'is_draft' => 'boolean',
        ];
    }

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // 1200x630 is the usual Open Graph size for link previews on WhatsApp and LinkedIn.
        $this->addMediaConversion('og')
            ->format('webp')
            ->width(1200)
            ->height(630)
            ->nonQueued();
    }
}
