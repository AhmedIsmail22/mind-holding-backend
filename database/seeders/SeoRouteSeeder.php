<?php

namespace Database\Seeders;

use App\Models\SeoMeta;
use Illuminate\Database\Seeder;

class SeoRouteSeeder extends Seeder
{
    /**
     * Draft SEO for the five route-level pages that have no database row to
     * fall back on. Entities (services, solutions, pages) fall back to their
     * own name and description, so they need no seed rows.
     */
    public function run(): void
    {
        $pages = [
            'home' => [
                'title' => ['ar' => 'MIND Holding — برمجيات وتسويق', 'en' => 'MIND Holding — Software & Marketing'],
                'description' => ['ar' => 'مواقع وتطبيقات وأنظمة أعمال وتسويق رقمي للشركات في مصر والخليج.', 'en' => 'Websites, apps, business systems and digital marketing for businesses in Egypt and the Gulf.'],
            ],
            'services' => [
                'title' => ['ar' => 'خدماتنا — MIND Holding', 'en' => 'Our services — MIND Holding'],
                'description' => ['ar' => 'خدمات البرمجيات والتسويق الرقمي من MIND Holding.', 'en' => 'Software and digital marketing services from MIND Holding.'],
            ],
            'solutions' => [
                'title' => ['ar' => 'حلولنا — MIND Holding', 'en' => 'Our solutions — MIND Holding'],
                'description' => ['ar' => 'حلول رقمية جاهزة لقطاعات التجارة والمطاعم والخدمات.', 'en' => 'Ready-made digital solutions for commerce, restaurants and services.'],
            ],
            'work' => [
                'title' => ['ar' => 'أعمالنا — MIND Holding', 'en' => 'Our work — MIND Holding'],
                'description' => ['ar' => 'مشاريع حقيقية نفذها فريقنا.', 'en' => 'Real projects delivered by our team.'],
            ],
            'contact' => [
                'title' => ['ar' => 'تواصل معنا — MIND Holding', 'en' => 'Contact us — MIND Holding'],
                'description' => ['ar' => 'اطلب عرض سعر أو تجربة أو اتصالاً.', 'en' => 'Request a quote, a demo or a callback.'],
            ],
        ];

        foreach ($pages as $key => $content) {
            SeoMeta::firstOrCreate(
                ['route_key' => $key],
                ['title' => $content['title'], 'description' => $content['description'], 'is_draft' => true],
            );
        }
    }
}
