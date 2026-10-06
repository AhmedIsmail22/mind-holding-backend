<?php

namespace App\Services\HomeContent;

use App\Models\HomeSection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class HomeSectionService
{
    public function listOrdered(): Collection
    {
        return HomeSection::orderBy('order')->get();
    }

    public function listEnabledOrdered(): Collection
    {
        return HomeSection::where('is_enabled', true)->orderBy('order')->get();
    }

    /**
     * @param  array<int, array{key: string, is_enabled: bool, order: int}>  $sections
     */
    public function bulkUpdate(array $sections): void
    {
        DB::transaction(function () use ($sections) {
            foreach ($sections as $section) {
                HomeSection::where('key', $section['key'])->update([
                    'is_enabled' => $section['is_enabled'],
                    'order' => $section['order'],
                ]);
            }
        });
    }
}
