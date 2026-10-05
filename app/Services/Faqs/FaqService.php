<?php

namespace App\Services\Faqs;

use App\DTOs\Faqs\CreateFaqData;
use App\DTOs\Faqs\UpdateFaqData;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class FaqService
{
    public function listGeneral(): Collection
    {
        return Faq::whereNull('faqable_type')->orderBy('order')->get();
    }

    public function listPublishedGeneral(): Collection
    {
        return Faq::whereNull('faqable_type')->where('is_published', true)->orderBy('order')->get();
    }

    public function listForOwner(Model $owner): Collection
    {
        return $owner->faqs()->orderBy('order')->get();
    }

    public function createGeneral(CreateFaqData $data): Faq
    {
        return Faq::create([
            'question' => $data->question,
            'answer' => $data->answer,
            'is_published' => $data->isPublished,
            'is_draft' => false,
            'order' => $data->order,
        ]);
    }

    public function createForOwner(Model $owner, CreateFaqData $data): Faq
    {
        return $owner->faqs()->create([
            'question' => $data->question,
            'answer' => $data->answer,
            'is_published' => $data->isPublished,
            'is_draft' => false,
            'order' => $data->order,
        ]);
    }

    public function update(Faq $faq, UpdateFaqData $data): Faq
    {
        $faq->update([
            'question' => $data->question,
            'answer' => $data->answer,
            'is_published' => $data->isPublished,
            'is_draft' => false,
            'order' => $data->order,
        ]);

        return $faq;
    }

    public function delete(Faq $faq): void
    {
        $faq->delete();
    }
}
