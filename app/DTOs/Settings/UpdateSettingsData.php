<?php

namespace App\DTOs\Settings;

use Illuminate\Http\UploadedFile;

final readonly class UpdateSettingsData
{
    public function __construct(
        public array $companyName,
        public ?string $phoneLandline,
        public ?string $phoneMobileEgypt,
        public string $whatsappEgypt,
        public string $whatsappDubai,
        public array $gulfCountries,
        public string $email,
        public string $leadNotificationEmail,
        public array $address,
        public array $socialLinks,
        public array $budgetOptions,
        public array $startTimingOptions,
        public ?string $googleAnalyticsId,
        public ?UploadedFile $logo,
        public ?array $logoAlt,
    ) {}

    public static function fromArray(array $data, ?UploadedFile $logo): self
    {
        return new self(
            companyName: $data['company_name'],
            phoneLandline: $data['phone_landline'] ?? null,
            phoneMobileEgypt: $data['phone_mobile_egypt'] ?? null,
            whatsappEgypt: $data['whatsapp_egypt'],
            whatsappDubai: $data['whatsapp_dubai'],
            gulfCountries: $data['gulf_countries'],
            email: $data['email'],
            leadNotificationEmail: $data['lead_notification_email'],
            address: $data['address'],
            socialLinks: $data['social_links'] ?? [],
            budgetOptions: $data['budget_options'],
            startTimingOptions: $data['start_timing_options'],
            googleAnalyticsId: $data['google_analytics_id'] ?? null,
            logo: $logo,
            logoAlt: $data['logo_alt'] ?? null,
        );
    }
}
