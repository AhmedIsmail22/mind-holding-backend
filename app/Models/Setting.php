<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Setting extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    public array $translatable = [
        'company_name',
        'address',
    ];

    protected $fillable = [
        'company_name',
        'phone_landline',
        'phone_mobile_egypt',
        'whatsapp_egypt',
        'whatsapp_dubai',
        'gulf_countries',
        'email',
        'lead_notification_email',
        'address',
        'social_links',
        'budget_options',
        'start_timing_options',
        'google_analytics_id',
    ];

    protected function casts(): array
    {
        return [
            'gulf_countries' => 'array',
            'social_links' => 'array',
            'budget_options' => 'array',
            'start_timing_options' => 'array',
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
