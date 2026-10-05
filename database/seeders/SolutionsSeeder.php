<?php

namespace Database\Seeders;

use App\Models\Solution;
use App\Models\SolutionIndustry;
use Illuminate\Database\Seeder;

class SolutionsSeeder extends Seeder
{
    /**
     * The 12 solutions from SRS §4 (4 flagship + 8 additional). Draft copy
     * (is_draft = true) pending the client's final content per SRS §1/§10.
     * No demo_url yet — flagship demos are built post-launch per SRS §9.
     */
    public function run(): void
    {
        if (Solution::query()->exists()) {
            return;
        }

        $deliverables = [
            ['ar' => 'موقع الويب', 'en' => 'Website'],
            ['ar' => 'تطبيق أندرويد', 'en' => 'Android app'],
            ['ar' => 'تطبيق iOS', 'en' => 'iOS app'],
            ['ar' => 'لوحة تحكم', 'en' => 'Dashboard'],
            ['ar' => 'تدريب', 'en' => 'Training'],
            ['ar' => 'دعم', 'en' => 'Support'],
        ];

        $genericProblems = [
            ['ar' => 'عمليات يدوية تستغرق وقتًا طويلاً', 'en' => 'Manual processes that take too long'],
            ['ar' => 'صعوبة متابعة العملاء والطلبات', 'en' => 'Difficulty tracking customers and orders'],
            ['ar' => 'غياب رؤية واضحة على الأداء', 'en' => 'No clear visibility into performance'],
        ];

        $genericFeatures = [
            'customer' => [['ar' => 'تجربة استخدام سهلة', 'en' => 'Easy-to-use experience']],
            'business_owner' => [['ar' => 'تقارير ولوحة متابعة', 'en' => 'Reports and a tracking dashboard']],
            'staff' => [['ar' => 'أدوات تسهّل العمل اليومي', 'en' => 'Tools that simplify daily work']],
        ];

        $solutions = [
            ['slug' => 'ecommerce-store-mobile-app', 'industry' => 'commerce', 'flagship' => true, 'ar' => 'متجر إلكتروني مع تطبيق موبايل', 'en' => 'E-commerce store with mobile app', 'audience_ar' => 'للمتاجر والعلامات التجارية', 'audience_en' => 'Shops and brands'],
            ['slug' => 'restaurant-cafe-app', 'industry' => 'restaurants', 'flagship' => true, 'ar' => 'تطبيق مطاعم وكافيهات (طلب مسبق وتوصيل)', 'en' => 'Restaurant & café app (order-ahead and delivery)', 'audience_ar' => 'للمطاعم والكافيهات والسلاسل', 'audience_en' => 'Restaurants, cafés, chains'],
            ['slug' => 'clinic-appointment-booking', 'industry' => 'bookings', 'flagship' => true, 'ar' => 'نظام حجز مواعيد العيادات', 'en' => 'Clinic appointment booking', 'audience_ar' => 'للعيادات والمراكز الطبية', 'audience_en' => 'Clinics and medical centers'],
            ['slug' => 'integrated-erp-system', 'industry' => 'business-systems', 'flagship' => true, 'ar' => 'نظام ERP متكامل', 'en' => 'Integrated ERP system', 'audience_ar' => 'للشركات متوسطة الحجم والتجارية', 'audience_en' => 'Mid-size and trading companies'],
            ['slug' => 'point-of-sale-pos', 'industry' => 'commerce', 'flagship' => false, 'ar' => 'نظام نقاط البيع (POS)', 'en' => 'Point of sale (POS)', 'audience_ar' => 'للمتاجر والشركات التجارية', 'audience_en' => 'Shops and commerce businesses'],
            ['slug' => 'customer-sales-management-crm', 'industry' => 'business-systems', 'flagship' => false, 'ar' => 'نظام إدارة العملاء والمبيعات (CRM)', 'en' => 'Customer & sales management (CRM)', 'audience_ar' => 'لفرق المبيعات والشركات', 'audience_en' => 'Sales teams and companies'],
            ['slug' => 'shipping-tracking-system', 'industry' => 'logistics', 'flagship' => false, 'ar' => 'نظام شحن وتتبع', 'en' => 'Shipping & tracking system', 'audience_ar' => 'لشركات الشحن واللوجستيات', 'audience_en' => 'Shipping and logistics companies'],
            ['slug' => 'delivery-app', 'industry' => 'logistics', 'flagship' => false, 'ar' => 'تطبيق توصيل (عميل، سائق، لوحة تحكم)', 'en' => 'Delivery app (customer, driver, dashboard)', 'audience_ar' => 'لشركات التوصيل', 'audience_en' => 'Delivery companies'],
            ['slug' => 'gym-fitness-management', 'industry' => 'bookings', 'flagship' => false, 'ar' => 'نظام إدارة الصالات الرياضية', 'en' => 'Gym & fitness management', 'audience_ar' => 'للصالات الرياضية ومراكز اللياقة', 'audience_en' => 'Gyms and fitness centers'],
            ['slug' => 'online-learning-platform', 'industry' => 'education', 'flagship' => false, 'ar' => 'منصة تعليم عن بعد', 'en' => 'Online learning platform', 'audience_ar' => 'للمؤسسات التعليمية والمدربين', 'audience_en' => 'Educational institutions and trainers'],
            ['slug' => 'real-estate-portal', 'industry' => 'real-estate', 'flagship' => false, 'ar' => 'بوابة عقارية', 'en' => 'Real estate portal', 'audience_ar' => 'لشركات التسويق العقاري', 'audience_en' => 'Real estate marketing companies'],
            ['slug' => 'contracting-finishing-system', 'industry' => 'real-estate', 'flagship' => false, 'ar' => 'نظام شركات المقاولات والتشطيبات', 'en' => 'Contracting & finishing company system', 'audience_ar' => 'لشركات المقاولات والتشطيبات', 'audience_en' => 'Contracting and finishing companies'],
        ];

        foreach ($solutions as $order => $solution) {
            $industry = SolutionIndustry::where('slug', $solution['industry'])->first();

            Solution::create([
                'solution_industry_id' => $industry->id,
                'name' => ['ar' => $solution['ar'], 'en' => $solution['en']],
                'slug' => $solution['slug'],
                'target_audience' => ['ar' => $solution['audience_ar'], 'en' => $solution['audience_en']],
                'problem_points' => $genericProblems,
                'features' => $genericFeatures,
                'deliverables' => $deliverables,
                'demo_url' => null,
                'demo_credentials' => null,
                'is_flagship' => $solution['flagship'],
                'is_published' => true,
                'is_draft' => true,
                'order' => $order,
            ]);
        }
    }
}
