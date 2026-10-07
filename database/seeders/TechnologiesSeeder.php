<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;

class TechnologiesSeeder extends Seeder
{
    /**
     * The stack used on client projects, as draft entries with no logos yet.
     * Entries an admin has already published are not touched.
     */
    public function run(): void
    {
        $categories = [
            'backend' => ['ar' => 'الخلفية', 'en' => 'Backend'],
            'web' => ['ar' => 'الويب', 'en' => 'Web'],
            'cloud' => ['ar' => 'السحابة والتشغيل', 'en' => 'Cloud & DevOps'],
            'marketing' => ['ar' => 'التسويق والتحليلات', 'en' => 'Marketing & Analytics'],
        ];

        $technologies = [
            ['name' => 'Laravel', 'category' => 'backend'],
            ['name' => 'PHP', 'category' => 'backend'],
            ['name' => 'MySQL', 'category' => 'backend'],
            ['name' => 'Redis', 'category' => 'backend'],
            ['name' => 'Next.js', 'category' => 'web'],
            ['name' => 'React', 'category' => 'web'],
            ['name' => 'TypeScript', 'category' => 'web'],
            ['name' => 'Tailwind CSS', 'category' => 'web'],
            ['name' => 'Linux VPS', 'category' => 'cloud'],
            ['name' => 'Nginx', 'category' => 'cloud'],
            ['name' => 'Git', 'category' => 'cloud'],
            ['name' => 'Google Analytics', 'category' => 'marketing'],
            ['name' => 'Meta Ads', 'category' => 'marketing'],
            ['name' => 'Google Ads', 'category' => 'marketing'],
            ['name' => 'TikTok Ads', 'category' => 'marketing'],
        ];

        foreach ($technologies as $order => $technology) {
            $existing = Technology::where('name', $technology['name'])->first();

            if ($existing !== null && ! $existing->is_draft) {
                continue;
            }

            Technology::updateOrCreate(
                ['name' => $technology['name']],
                [
                    'category' => $categories[$technology['category']],
                    'order' => $order,
                    'is_draft' => true,
                ],
            );
        }
    }
}
