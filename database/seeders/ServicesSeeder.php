<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * The 8 services from SRS §4 with draft summaries (CR-01 copy). Rows
     * whose content an admin has already published are left alone.
     */
    public function run(): void
    {
        $genericDeliverables = [
            ['ar' => 'استشارة أولية وتحديد الاحتياجات', 'en' => 'Initial consultation and requirements scoping'],
            ['ar' => 'تصميم وتنفيذ متوافق مع هوية العلامة', 'en' => 'Design and delivery matching your brand identity'],
            ['ar' => 'دعم ومتابعة بعد التسليم', 'en' => 'Support and follow-up after delivery'],
        ];

        $genericSteps = [
            ['ar' => 'مكالمة اكتشاف لفهم المتطلبات', 'en' => 'Discovery call to understand requirements'],
            ['ar' => 'عرض سعر ومخطط زمني', 'en' => 'Quote and timeline proposal'],
            ['ar' => 'التنفيذ والمراجعات', 'en' => 'Execution and review rounds'],
            ['ar' => 'التسليم والدعم', 'en' => 'Delivery and support'],
        ];

        $services = [
            [
                'group' => 'software', 'slug' => 'website-design-development', 'order' => 0,
                'ar' => 'تصميم وتطوير المواقع', 'en' => 'Website design & development',
                'summary_ar' => 'مواقع سريعة باللغتين العربية والإنجليزية، مصممة لتحويل الزائر إلى عميل.',
                'summary_en' => 'Fast, bilingual websites built to turn visitors into inquiries.',
            ],
            [
                'group' => 'software', 'slug' => 'mobile-apps', 'order' => 1,
                'ar' => 'تطبيقات الموبايل (أندرويد و iOS)', 'en' => 'Mobile apps (Android and iOS)',
                'summary_ar' => 'تطبيقات أندرويد وiOS، من الفكرة حتى النشر على المتاجر.',
                'summary_en' => 'Android and iOS apps, from first idea to store launch.',
            ],
            [
                'group' => 'software', 'slug' => 'custom-business-systems', 'order' => 2,
                'ar' => 'أنظمة أعمال مخصصة', 'en' => 'Custom business systems',
                'summary_ar' => 'أنظمة تُبنى حول طريقة عملك الفعلية.',
                'summary_en' => 'Systems built around how your business actually works.',
            ],
            [
                'group' => 'software', 'slug' => 'ecommerce-stores', 'order' => 3,
                'ar' => 'متاجر التجارة الإلكترونية', 'en' => 'E-commerce stores',
                'summary_ar' => 'متاجر إلكترونية متكاملة بالدفع والشحن ولوحة تحكم سهلة.',
                'summary_en' => 'Online stores with payments, shipping, and an easy dashboard.',
            ],
            [
                'group' => 'marketing', 'slug' => 'social-media-management', 'order' => 4,
                'ar' => 'إدارة السوشيال ميديا', 'en' => 'Social media management',
                'summary_ar' => 'محتوى مخطط ونشر منتظم على جميع منصاتك.',
                'summary_en' => 'Planned content and consistent posting across your channels.',
            ],
            [
                'group' => 'marketing', 'slug' => 'paid-ads', 'order' => 5,
                'ar' => 'الإعلانات الممولة (ميتا، جوجل، تيك توك، سناب شات)', 'en' => 'Paid ads (Meta, Google, TikTok, Snapchat)',
                'summary_ar' => 'حملات على ميتا وجوجل وتيك توك وسناب شات، نقيس نتائجها بالعملاء الفعليين.',
                'summary_en' => 'Campaigns on Meta, Google, TikTok and Snapchat, tracked to real leads.',
            ],
            [
                'group' => 'marketing', 'slug' => 'brand-identity-design', 'order' => 6,
                'ar' => 'الهوية البصرية والتصميم', 'en' => 'Brand identity & design',
                'summary_ar' => 'شعار وهوية بصرية وتصميمات تجعل علامتك التجارية مميزة.',
                'summary_en' => 'Logos, visual identity, and design that make your brand recognizable.',
            ],
            [
                'group' => 'marketing', 'slug' => 'photography-motion-graphics', 'order' => 7,
                'ar' => 'التصوير والموشن جرافيك', 'en' => 'Photography & motion graphics',
                'summary_ar' => 'تصوير منتجات وفيديوهات موشن لمحتواك وإعلاناتك.',
                'summary_en' => 'Product photography and motion videos for your content and ads.',
            ],
        ];

        foreach ($services as $service) {
            $existing = Service::where('slug', $service['slug'])->first();

            if ($existing !== null && ! $existing->is_draft) {
                continue;
            }

            Service::updateOrCreate(
                ['slug' => $service['slug']],
                [
                    'group' => $service['group'],
                    'name' => ['ar' => $service['ar'], 'en' => $service['en']],
                    'summary' => ['ar' => $service['summary_ar'], 'en' => $service['summary_en']],
                    'description' => [
                        'ar' => "خدمة {$service['ar']} من بيتكودك — محتوى مبدئي سيتم استبداله بالنسخة النهائية من العميل.",
                        'en' => "{$service['en']} from Bitcodak — placeholder copy, to be replaced with the client's final content.",
                    ],
                    'deliverables' => $genericDeliverables,
                    'delivery_steps' => $genericSteps,
                    'is_published' => true,
                    'is_draft' => true,
                    'order' => $service['order'],
                ],
            );
        }
    }
}
