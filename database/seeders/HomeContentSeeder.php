<?php

namespace Database\Seeders;

use App\Models\HomeContent;
use Illuminate\Database\Seeder;

class HomeContentSeeder extends Seeder
{
    /**
     * Draft copy for the Home page (CR-01). Differentiators and process steps
     * are replaced on every run, but only while the row is still a draft, so
     * copy an admin has published is never overwritten. Stats are never seeded:
     * the SRS allows real numbers only.
     */
    public function run(): void
    {
        $content = HomeContent::query()->first();

        if ($content === null) {
            $content = HomeContent::create([
                'hero_headline' => [
                    'ar' => 'نجمع بين البرمجيات والتسويق لينمو نشاطك',
                    'en' => 'We combine software and marketing to grow your business',
                ],
                'hero_subheadline' => [
                    'ar' => 'مواقع وتطبيقات وأنظمة أعمال، مع إدارة السوشيال ميديا والإعلانات — فريق واحد وشريك واحد.',
                    'en' => 'Websites, apps and business systems, plus social media and paid ads: one team, one partner.',
                ],
                'stats' => [],
                'differentiators' => [],
                'process_steps' => [],
                'closing_cta_headline' => [
                    'ar' => 'جاهز لبدء مشروعك؟',
                    'en' => 'Ready to start your project?',
                ],
                'closing_cta_subheadline' => [
                    'ar' => 'أخبرنا عن مشروعك وسنتواصل معك قريبًا.',
                    'en' => 'Tell us about your project and we will get back to you soon.',
                ],
                'is_draft' => true,
            ]);
        }

        if (! $content->is_draft) {
            return;
        }

        $content->update([
            'differentiators' => [
                [
                    'title' => ['ar' => 'برمجة وتسويق في فريق واحد', 'en' => 'Software and marketing, one team'],
                    'description' => ['ar' => 'فريق واحد يبني منتجك ويجلب لك العملاء، دون التنقل بين أكثر من شركة.', 'en' => 'One team builds your product and brings you customers, so nothing gets lost between agencies.'],
                ],
                [
                    'title' => ['ar' => 'نفهم السوقين', 'en' => 'We know both markets'],
                    'description' => ['ar' => 'حلول مصممة لمصر والخليج: العربية أولًا، وتصميم صحيح من اليمين لليسار، ووسائل دفع وتوصيل محلية.', 'en' => 'Built for Egypt and the Gulf: Arabic first, right-to-left done properly, local payment and delivery options.'],
                ],
                [
                    'title' => ['ar' => 'كل شيء واضح قبل البدء', 'en' => 'Clear scope before we start'],
                    'description' => ['ar' => 'نطاق العمل والمدة والسعر مكتوبة قبل أن نبدأ.', 'en' => 'A written scope, timeline and price before any work begins.'],
                ],
                [
                    'title' => ['ar' => 'تتابع العمل كل أسبوع', 'en' => 'You see progress every week'],
                    'description' => ['ar' => 'عرض عملي للتقدم في نهاية كل أسبوع، بلا مفاجآت في النهاية.', 'en' => 'A working demo at the end of every week, not a surprise at the end.'],
                ],
                [
                    'title' => ['ar' => 'كل شيء ملكك', 'en' => 'You own everything'],
                    'description' => ['ar' => 'الكود والحسابات والنطاق تُسلَّم باسمك.', 'en' => 'Code, accounts and domain are delivered in your name.'],
                ],
                [
                    'title' => ['ar' => 'معك بعد الإطلاق', 'en' => 'We stay after launch'],
                    'description' => ['ar' => 'تدريب لفريقك ودعم بعد التشغيل.', 'en' => 'Training for your team and support after go-live.'],
                ],
            ],
            'process_steps' => [
                [
                    'title' => ['ar' => 'مكالمة تعارف', 'en' => 'Discovery call'],
                    'duration' => ['ar' => 'يوم إلى يومين', 'en' => '1–2 days'],
                    'description' => ['ar' => 'نتعرف على نشاطك وأهدافك واحتياجاتك بدقة.', 'en' => 'We learn your business, goals and what you need.'],
                ],
                [
                    'title' => ['ar' => 'النطاق والعرض', 'en' => 'Scope & proposal'],
                    'duration' => ['ar' => '2–3 أيام', 'en' => '2–3 days'],
                    'description' => ['ar' => 'نرسل لك نطاق العمل والمدة والسعر مكتوبة.', 'en' => 'You get a written scope, timeline and price.'],
                ],
                [
                    'title' => ['ar' => 'التصميم', 'en' => 'Design'],
                    'duration' => ['ar' => 'أسبوع إلى أسبوعين', 'en' => '1–2 weeks'],
                    'description' => ['ar' => 'تعتمد التصميم قبل أن نبدأ أي برمجة.', 'en' => 'You approve the design before any development starts.'],
                ],
                [
                    'title' => ['ar' => 'التطوير', 'en' => 'Development'],
                    'duration' => ['ar' => 'حسب حجم المشروع', 'en' => 'Depends on scope'],
                    'description' => ['ar' => 'نبني على مراحل، وتتابع العمل كل أسبوع.', 'en' => 'We build in stages and show you a working demo every week.'],
                ],
                [
                    'title' => ['ar' => 'الإطلاق والدعم', 'en' => 'Launch & support'],
                    'duration' => ['ar' => 'مستمر', 'en' => 'Ongoing'],
                    'description' => ['ar' => 'نطلق المشروع وندرّب فريقك ونبقى معك بعد التشغيل.', 'en' => 'We launch, train your team, and stay with you after go-live.'],
                ],
            ],
            'is_draft' => true,
        ]);
    }
}
