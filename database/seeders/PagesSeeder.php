<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    /**
     * The three fixed pages from SRS §6. Body text is a draft placeholder
     * until the client supplies final copy.
     */
    public function run(): void
    {
        $pages = [
            'about' => [
                'title' => ['ar' => 'من نحن', 'en' => 'About us'],
                'body' => ['ar' => 'محتوى مؤقت — سيتم استبداله بقصة الشركة النهائية.', 'en' => 'Placeholder content. Will be replaced with the final company story.'],
            ],
            'privacy' => [
                'title' => ['ar' => 'سياسة الخصوصية', 'en' => 'Privacy policy'],
                'body' => ['ar' => 'محتوى مؤقت — سيتم استبداله بسياسة الخصوصية النهائية.', 'en' => 'Placeholder content. Will be replaced with the final privacy policy.'],
            ],
            'terms' => [
                'title' => ['ar' => 'الشروط والأحكام', 'en' => 'Terms and conditions'],
                'body' => ['ar' => 'محتوى مؤقت — سيتم استبداله بالشروط والأحكام النهائية.', 'en' => 'Placeholder content. Will be replaced with the final terms and conditions.'],
            ],
        ];

        foreach ($pages as $slug => $page) {
            Page::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $page['title'],
                    'body' => $page['body'],
                    'is_draft' => true,
                ],
            );
        }
    }
}
