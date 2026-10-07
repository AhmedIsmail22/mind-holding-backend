<?php

namespace App\Http\Resources\Admin;

use App\Models\Setting;
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
            'phone_landline' => $this->phone_landline,
            'phone_mobile_egypt' => $this->phone_mobile_egypt,
            'whatsapp_egypt' => $this->whatsapp_egypt,
            'whatsapp_dubai' => $this->whatsapp_dubai,
            'gulf_countries' => $this->gulf_countries,
            'email' => $this->email,
            'lead_notification_email' => $this->lead_notification_email,
            'address' => $this->getTranslations('address'),
            'social_links' => $this->social_links,
            'budget_options' => $this->budget_options,
            'start_timing_options' => $this->start_timing_options,
            'google_analytics_id' => $this->google_analytics_id,
        ];
    }
}
