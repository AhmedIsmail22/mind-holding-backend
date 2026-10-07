<?php

namespace App\Http\Resources\Admin;

use App\Models\Setting;
use App\Support\Media\MediaAsset;
use App\Support\Settings\SettingsArrays;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Setting
 */
class SettingsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'company_name' => $this->getTranslations('company_name'),
            'logo_url' => $this->getFirstMediaUrl('logo', 'webp') ?: null,
            'logo_alt' => MediaAsset::altBothLocales($this->getFirstMedia('logo')),
            'phone_landline' => $this->phone_landline,
            'phone_mobile_egypt' => $this->phone_mobile_egypt,
            'whatsapp_egypt' => $this->whatsapp_egypt,
            'whatsapp_dubai' => $this->whatsapp_dubai,
            'gulf_countries' => SettingsArrays::gulfCountries($this->resource),
            'email' => $this->email,
            'lead_notification_email' => $this->lead_notification_email,
            'address' => $this->getTranslations('address'),
            'social_links' => SettingsArrays::socialLinks($this->resource),
            'budget_options' => $this->budget_options,
            'start_timing_options' => $this->start_timing_options,
            'google_analytics_id' => $this->google_analytics_id,
        ];
    }
}
