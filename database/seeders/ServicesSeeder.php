<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * The 8 services from the SRS §4 catalog (4 Software + 4 Marketing).
     * Descriptions/deliverables/steps are draft copy (is_draft = true) —
     * the client provides final copywriting later per SRS §1/§10.
     */
    public function run(): void
    {
        if (Service::query()->exists()) {
            return;
        }

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
            ['group' => 'software', 'slug' => 'website-design-development', 'ar' => 'تصميم وتطوير المواقع', 'en' => 'Website design & development'],
            ['group' => 'software', 'slug' => 'mobile-apps', 'ar' => 'تطبيقات الموبايل (أندرويد و iOS)', 'en' => 'Mobile apps (Android and iOS)'],
            ['group' => 'software', 'slug' => 'custom-business-systems', 'ar' => 'أنظمة أعمال مخصصة', 'en' => 'Custom business systems'],
            ['group' => 'software', 'slug' => 'ecommerce-stores', 'ar' => 'متاجر التجارة الإلكترونية', 'en' => 'E-commerce stores'],
            ['group' => 'marketing', 'slug' => 'social-media-management', 'ar' => 'إدارة السوشيال ميديا', 'en' => 'Social media management'],
            ['group' => 'marketing', 'slug' => 'paid-ads', 'ar' => 'الإعلانات الممولة (ميتا، جوجل، تيك توك، سناب شات)', 'en' => 'Paid ads (Meta, Google, TikTok, Snapchat)'],
            ['group' => 'marketing', 'slug' => 'brand-identity-design', 'ar' => 'الهوية البصرية والتصميم', 'en' => 'Brand identity & design'],
            ['group' => 'marketing', 'slug' => 'photography-motion-graphics', 'ar' => 'التصوير والموشن جرافيك', 'en' => 'Photography & motion graphics'],
        ];

        foreach ($services as $order => $service) {
            Service::create([
                'group' => $service['group'],
                'name' => ['ar' => $service['ar'], 'en' => $service['en']],
                'slug' => $service['slug'],
                'description' => [
                    'ar' => "خدمة {$service['ar']} من بيتكودك — محتوى مبدئي سيتم استبداله بالنسخة النهائية من العميل.",
                    'en' => "{$service['en']} from Bitcodak — placeholder copy, to be replaced with the client's final content.",
                ],
                'deliverables' => $genericDeliverables,
                'delivery_steps' => $genericSteps,
                'is_published' => true,
                'is_draft' => true,
                'order' => $order,
            ]);
        }
    }
}
