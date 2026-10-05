<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'company_name' => $this->company_name,
            'logo_url' => $this->getFirstMediaUrl('logo', 'webp') ?: null,
            'phone_landline' => $this->phone_landline,
            'phone_mobile_egypt' => $this->phone_mobile_egypt,
            'whatsapp_egypt' => $this->whatsapp_egypt,
            'whatsapp_dubai' => $this->whatsapp_dubai,
            'gulf_countries' => $this->gulf_countries,
            'email' => $this->email,
            'address' => $this->address,
            'social_links' => $this->social_links,
            'budget_options' => collect($this->budget_options)->map(fn ($option) => $option[$locale] ?? $option['en'])->values(),
            'start_timing_options' => collect($this->start_timing_options)->map(fn ($option) => $option[$locale] ?? $option['en'])->values(),
            'google_analytics_id' => $this->google_analytics_id,
        ];
    }
}
