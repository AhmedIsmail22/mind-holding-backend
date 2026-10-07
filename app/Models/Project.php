<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Project extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia, SoftDeletes;

    public array $translatable = ['client_name', 'generic_description', 'overview', 'challenge', 'solution'];

    protected $fillable = [
        'slug',
        'slug_ar',
        'client_name',
        'hide_client_name',
        'generic_description',
        'overview',
        'challenge',
        'solution',
        'technologies',
        'live_url',
        'is_published',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'hide_client_name' => 'boolean',
            'technologies' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_project');
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
}
