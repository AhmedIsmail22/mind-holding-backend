<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class GeneralFaqsSeeder extends Seeder
{
    /**
     * General FAQs for the Home page (draft copy, simplified MSA). Drafts are
     * matched by English question and updated, new ones are created, and draft
     * FAQs no longer in this list are removed. Published FAQs are never touched.
     */
    public function run(): void
    {
        $faqs = [
            [
                'en' => 'How much does a project cost?', 'ar' => 'كم تكلفة المشروع؟',
                'en_a' => 'It depends on the scope. After the discovery call, you get a written price before any work starts.',
                'ar_a' => 'تعتمد التكلفة على حجم المشروع ومتطلباته، ونرسل لك سعرًا مكتوبًا بعد مكالمة التعارف وقبل بدء أي عمل.',
            ],
            [
                'en' => 'How long does a project take?', 'ar' => 'كم يستغرق تنفيذ المشروع؟',
                'en_a' => 'It depends on the scope. We set the timeline in the written proposal, and you see progress every week.',
                'ar_a' => 'تختلف المدة حسب حجم المشروع، ونحددها في العرض المكتوب، وتتابع التقدم كل أسبوع.',
            ],
            [
                'en' => 'Do you work with clients outside Egypt?', 'ar' => 'هل تعملون مع عملاء خارج مصر؟',
                'en_a' => 'Yes. We work with clients in Egypt and the Gulf, and meetings are held online.',
                'ar_a' => 'نعم، نعمل مع عملاء في مصر ودول الخليج، وتتم الاجتماعات عبر الإنترنت.',
            ],
            [
                'en' => 'Who owns the code?', 'ar' => 'لمن تكون ملكية الكود؟',
                'en_a' => 'You do. Code, accounts and domain are delivered in your name.',
                'ar_a' => 'الملكية لك بالكامل، ويُسلَّم الكود والحسابات والنطاق باسمك.',
            ],
            [
                'en' => 'What happens after launch?', 'ar' => 'ماذا يحدث بعد الإطلاق؟',
                'en_a' => 'We train your team and support you after go-live. Ongoing maintenance is available on request.',
                'ar_a' => 'ندرّب فريقك وندعمك بعد التشغيل، ويتوفر عقد صيانة مستمر عند الطلب.',
            ],
            [
                'en' => 'How does payment work?', 'ar' => 'كيف تتم عملية الدفع؟',
                'en_a' => 'In installments tied to project milestones, agreed in the proposal.',
                'ar_a' => 'على دفعات مرتبطة بمراحل المشروع، ويُتفق عليها في العرض.',
            ],
        ];

        $keptQuestions = [];

        foreach ($faqs as $order => $faq) {
            $keptQuestions[] = $faq['en'];

            $draft = Faq::query()
                ->whereNull('faqable_type')
                ->where('is_draft', true)
                ->get()
                ->first(fn (Faq $existing) => $existing->getTranslation('question', 'en') === $faq['en']);

            $attributes = [
                'question' => ['ar' => $faq['ar'], 'en' => $faq['en']],
                'answer' => ['ar' => $faq['ar_a'], 'en' => $faq['en_a']],
                'is_published' => true,
                'is_draft' => true,
                'order' => $order,
            ];

            if ($draft !== null) {
                $draft->update($attributes);
            } else {
                Faq::create(['faqable_type' => null, 'faqable_id' => null] + $attributes);
            }
        }

        Faq::query()
            ->whereNull('faqable_type')
            ->where('is_draft', true)
            ->get()
            ->filter(fn (Faq $existing) => ! in_array($existing->getTranslation('question', 'en'), $keptQuestions, true))
            ->each(fn (Faq $existing) => $existing->delete());
    }
}
