<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seeds the single settings row with the contact details explicitly
     * approved in the SRS (§10 "Approved contact details" /  "Proposals
     * approved by the client") — these are real, not placeholders. Social
     * links, the GA ID, and the logo are left empty/null because no real
     * values were supplied yet. Budget/start-timing options are generic
     * starter dropdown choices (not business facts, so the "never invent
     * content" rule doesn't apply) meant to be customized by the admin.
     */
    public function run(): void
    {
        if (Setting::query()->exists()) {
            return;
        }

        Setting::create([
            'company_name' => ['ar' => 'MIND Holding', 'en' => 'MIND Holding'],
            'phone_landline' => '+202 22746241',
            'phone_mobile_egypt' => '+20 111 564 6730',
            'whatsapp_egypt' => '+20 111 564 6730',
            'whatsapp_dubai' => '+971 50 336 5403',
            'gulf_countries' => ['SA', 'AE', 'KW', 'QA', 'BH', 'OM'],
            'email' => 'info@mindholding.net',
            'lead_notification_email' => 'info@mindholding.net',
            'address' => [
                'ar' => '8 محمد توفيق دياب، مدينة نصر، القاهرة، مصر',
                'en' => '8 Mohammed Tawfik Diab, Nasr City, Cairo, Egypt',
            ],
            'social_links' => [],
            'budget_options' => [
                ['ar' => 'أقل من 1,000 دولار', 'en' => 'Less than $1,000'],
                ['ar' => '1,000 - 5,000 دولار', 'en' => '$1,000 - $5,000'],
                ['ar' => '5,000 - 15,000 دولار', 'en' => '$5,000 - $15,000'],
                ['ar' => 'أكثر من 15,000 دولار', 'en' => 'More than $15,000'],
            ],
            'start_timing_options' => [
                ['ar' => 'في أقرب وقت', 'en' => 'As soon as possible'],
                ['ar' => 'خلال شهر', 'en' => 'Within a month'],
                ['ar' => '1-3 أشهر', 'en' => '1-3 months'],
                ['ar' => 'لم يتم تحديد الوقت بعد', 'en' => 'Not decided yet'],
            ],
            'google_analytics_id' => null,
        ]);
    }
}
