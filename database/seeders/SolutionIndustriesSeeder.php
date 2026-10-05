<?php

namespace Database\Seeders;

use App\Models\SolutionIndustry;
use Illuminate\Database\Seeder;

class SolutionIndustriesSeeder extends Seeder
{
    /**
     * The 7 industries used to filter the solutions catalog, per SRS §3.3.
     */
    public function run(): void
    {
        if (SolutionIndustry::query()->exists()) {
            return;
        }

        $industries = [
            ['slug' => 'commerce', 'ar' => 'التجارة', 'en' => 'Commerce'],
            ['slug' => 'business-systems', 'ar' => 'أنظمة الأعمال', 'en' => 'Business systems'],
            ['slug' => 'logistics', 'ar' => 'الخدمات اللوجستية', 'en' => 'Logistics'],
            ['slug' => 'restaurants', 'ar' => 'المطاعم', 'en' => 'Restaurants'],
            ['slug' => 'bookings', 'ar' => 'الحجوزات والخدمات', 'en' => 'Bookings & services'],
            ['slug' => 'education', 'ar' => 'التعليم', 'en' => 'Education'],
            ['slug' => 'real-estate', 'ar' => 'العقارات والمقاولات', 'en' => 'Real estate & contracting'],
        ];

        foreach ($industries as $order => $industry) {
            SolutionIndustry::create([
                'name' => ['ar' => $industry['ar'], 'en' => $industry['en']],
                'slug' => $industry['slug'],
                'order' => $order,
            ]);
        }
    }
}
