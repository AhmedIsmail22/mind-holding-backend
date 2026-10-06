<?php

namespace App\Services\HomeContent;

use App\DTOs\HomeContent\UpdateHomeContentData;
use App\Models\HomeContent;

class HomeContentService
{
    public function current(): HomeContent
    {
        return HomeContent::current();
    }

    public function update(UpdateHomeContentData $data): HomeContent
    {
        $content = HomeContent::current();

        $content->setTranslations('hero_headline', $data->heroHeadline);
        $content->setTranslations('hero_subheadline', $data->heroSubheadline);
        $content->stats = $data->stats;
        $content->differentiators = $data->differentiators;
        $content->process_steps = $data->processSteps;
        $content->setTranslations('closing_cta_headline', $data->closingCtaHeadline);

        if ($data->closingCtaSubheadline !== null) {
            $content->setTranslations('closing_cta_subheadline', $data->closingCtaSubheadline);
        }

        $content->is_draft = false;
        $content->save();

        if ($data->heroImage !== null) {
            $content->clearMediaCollection('hero');
            $content->addMedia($data->heroImage)->toMediaCollection('hero');
        }

        return $content->refresh();
    }
}
