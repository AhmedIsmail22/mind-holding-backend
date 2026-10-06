<?php

namespace Database\Seeders;

use App\Models\HomeSection;
use Illuminate\Database\Seeder;

class HomeSectionsSeeder extends Seeder
{
    /**
     * The 10 Home sections in SRS §3.1 order. All enabled by default.
     */
    public function run(): void
    {
        $sections = [
            'hero',
            'quick_stats',
            'our_services',
            'solutions_by_industry',
            'why_us',
            'how_we_work',
            'selected_work',
            'technologies',
            'faq',
            'closing_cta',
        ];

        foreach ($sections as $order => $key) {
            HomeSection::firstOrCreate(
                ['key' => $key],
                ['is_enabled' => true, 'order' => $order],
            );
        }
    }
}
