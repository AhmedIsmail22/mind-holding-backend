<?php

namespace Database\Seeders;

use App\Models\HomeContent;
use Illuminate\Database\Seeder;

class HomeContentSeeder extends Seeder
{
    /**
     * Draft copy (is_draft = true) for the Home page. Differentiators are the
     * three examples the SRS §3.1 names. Process steps mirror the SRS §9
     * timeline. Stats stay empty: the SRS allows only real numbers, and none
     * have been supplied.
     */
    public function run(): void
    {
        if (HomeContent::query()->exists()) {
            return;
        }

        HomeContent::create([
            'hero_headline' => [
                'ar' => 'نجمع بين البرمجيات والتسويق لينمو نشاطك',
                'en' => 'We combine software and marketing to grow your business',
            ],
            'hero_subheadline' => [
                'ar' => 'مواقع وتطبيقات وأنظمة أعمال، مع إدارة السوشيال ميديا والإعلانات — فريق واحد وشريك واحد.',
                'en' => 'Websites, apps and business systems, plus social media and paid ads: one team, one partner.',
            ],
            'stats' => [],
            'differentiators' => [
                [
                    'title' => ['ar' => 'البرمجيات والتسويق في مكان واحد', 'en' => 'Software and marketing in one place'],
                    'description' => ['ar' => 'لا حاجة لتنسيق بين عدة مزودين.', 'en' => 'No need to coordinate between several vendors.'],
                ],
                [
                    'title' => ['ar' => 'دعم بعد الإطلاق', 'en' => 'Post-launch support'],
                    'description' => ['ar' => 'نبقى معك بعد تسليم المشروع.', 'en' => 'We stay with you after the project is delivered.'],
                ],
                [
                    'title' => ['ar' => 'نعرف السوقين المصري والخليجي', 'en' => 'We know both Egyptian and Gulf markets'],
                    'description' => ['ar' => 'خبرة في احتياجات الشركات في المنطقتين.', 'en' => 'Experience with business needs in both regions.'],
                ],
            ],
            'process_steps' => [
                [
                    'title' => ['ar' => 'اعتماد المتطلبات', 'en' => 'Requirements approval'],
                    'description' => ['ar' => 'مراجعة واعتماد نطاق المشروع.', 'en' => 'Review and approval of the project scope.'],
                    'duration' => ['ar' => 'الأسبوع 0', 'en' => 'Week 0'],
                ],
                [
                    'title' => ['ar' => 'التصميم', 'en' => 'Design'],
                    'description' => ['ar' => 'تصميم الصفحات الرئيسية ثم باقي الصفحات.', 'en' => 'Design of the main pages, then the rest.'],
                    'duration' => ['ar' => 'الأسبوع 1', 'en' => 'Week 1'],
                ],
                [
                    'title' => ['ar' => 'المحتوى', 'en' => 'Content'],
                    'description' => ['ar' => 'كتابة المحتوى وترجمته.', 'en' => 'Writing and translating the content.'],
                    'duration' => ['ar' => 'الأسبوع 1-2', 'en' => 'Weeks 1-2'],
                ],
                [
                    'title' => ['ar' => 'التطوير', 'en' => 'Development'],
                    'description' => ['ar' => 'بناء الموقع ولوحة التحكم، مع عرض تجريبي أسبوعي.', 'en' => 'Building the website and dashboard, with a weekly demo.'],
                    'duration' => ['ar' => 'الأسبوع 2-4', 'en' => 'Weeks 2-4'],
                ],
                [
                    'title' => ['ar' => 'الاختبار والإطلاق', 'en' => 'Testing and launch'],
                    'description' => ['ar' => 'اختبار داخلي وقبول العميل ثم الإطلاق.', 'en' => 'Internal QA, client acceptance, then go-live.'],
                    'duration' => ['ar' => 'الأسبوع 5', 'en' => 'Week 5'],
                ],
            ],
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
}
