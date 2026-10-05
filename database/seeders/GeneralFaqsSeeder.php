<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class GeneralFaqsSeeder extends Seeder
{
    /**
     * 6 general FAQs on price, timeline, support, and payment, for the Home
     * FAQ section per SRS §3.1 #9. Draft copy pending final client review.
     */
    public function run(): void
    {
        if (Faq::query()->whereNull('faqable_type')->exists()) {
            return;
        }

        $faqs = [
            ['ar' => 'كم تكلفة المشروع؟', 'ar_a' => 'تختلف التكلفة حسب نطاق المشروع ومتطلباته. تواصل معنا لعرض سعر مخصص.', 'en' => 'How much does a project cost?', 'en_a' => 'Cost depends on the scope and requirements of your project. Contact us for a custom quote.'],
            ['ar' => 'كم تستغرق مدة تنفيذ المشروع؟', 'ar_a' => 'تختلف المدة حسب حجم المشروع، وعادة ما نشارك جدولًا زمنيًا تقديريًا بعد مكالمة الاكتشاف.', 'en' => 'How long does a project take?', 'en_a' => 'Timelines vary by project size; we share an estimated schedule after the discovery call.'],
            ['ar' => 'هل تقدمون دعمًا بعد الإطلاق؟', 'ar_a' => 'نعم، نقدم دعمًا ومتابعة بعد التسليم لكل المشاريع.', 'en' => 'Do you provide support after launch?', 'en_a' => 'Yes, we provide post-launch support and follow-up for every project.'],
            ['ar' => 'ما هي طرق وخطط الدفع المتاحة؟', 'ar_a' => 'نناقش خطط الدفع المناسبة لكل مشروع بشكل مباشر خلال عرض السعر.', 'en' => 'What payment methods/plans do you offer?', 'en_a' => 'We discuss a payment plan suited to each project directly in the quote.'],
            ['ar' => 'هل تتعاملون مع عملاء خارج مصر؟', 'ar_a' => 'نعم، نعمل مع عملاء في مصر ودول الخليج.', 'en' => 'Do you work with clients outside Egypt?', 'en_a' => 'Yes, we work with clients in Egypt and across the Gulf.'],
            ['ar' => 'هل يمكنني طلب تجربة قبل اتخاذ القرار؟', 'ar_a' => 'بعض الحلول الرئيسية لدينا تجربة تفاعلية متاحة من صفحة الحل.', 'en' => 'Can I request a demo before deciding?', 'en_a' => 'Several of our flagship solutions have an interactive demo available from the solution page.'],
        ];

        foreach ($faqs as $order => $faq) {
            Faq::create([
                'faqable_type' => null,
                'faqable_id' => null,
                'question' => ['ar' => $faq['ar'], 'en' => $faq['en']],
                'answer' => ['ar' => $faq['ar_a'], 'en' => $faq['en_a']],
                'is_published' => true,
                'is_draft' => true,
                'order' => $order,
            ]);
        }
    }
}
