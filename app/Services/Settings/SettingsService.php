<?php

namespace App\Services\Settings;

use App\DTOs\Settings\UpdateSettingsData;
use App\Models\Setting;
use App\Support\Media\MediaAsset;

class SettingsService
{
    public function current(): Setting
    {
        return Setting::current();
    }

    public function update(UpdateSettingsData $data): Setting
    {
        $setting = Setting::current();

        $setting->setTranslations('company_name', $data->companyName);
        $setting->setTranslations('address', $data->address);
        $setting->phone_landline = $data->phoneLandline;
        $setting->phone_mobile_egypt = $data->phoneMobileEgypt;
        $setting->whatsapp_egypt = $data->whatsappEgypt;
        $setting->whatsapp_dubai = $data->whatsappDubai;
        $setting->gulf_countries = $data->gulfCountries;
        $setting->email = $data->email;
        $setting->lead_notification_email = $data->leadNotificationEmail;
        $setting->social_links = $data->socialLinks;
        $setting->budget_options = $data->budgetOptions;
        $setting->start_timing_options = $data->startTimingOptions;
        $setting->google_analytics_id = $data->googleAnalyticsId;
        $setting->save();

        if ($data->logo !== null) {
            MediaAsset::replace($setting, $data->logo, 'logo', $data->logoAlt);
        } else {
            MediaAsset::setAlt($setting->getFirstMedia('logo'), $data->logoAlt);
        }

        return $setting->refresh();
    }
}
