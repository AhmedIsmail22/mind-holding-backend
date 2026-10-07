<?php

namespace App\Services\Pages;

use App\DTOs\Pages\UpdatePageData;
use App\Models\Page;

class PageService
{
    public function findBySlug(string $slug): Page
    {
        return Page::where('slug', $slug)->orWhere('slug_ar', $slug)->firstOrFail();
    }

    public function update(Page $page, UpdatePageData $data): Page
    {
        $page->setTranslations('title', $data->title);
        $page->setTranslations('body', $data->body);
        $page->slug_ar = $data->slugAr;
        $page->is_draft = false;
        $page->save();

        return $page;
    }
}
