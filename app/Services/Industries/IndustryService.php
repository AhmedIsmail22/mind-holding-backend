<?php

namespace App\Services\Industries;

use App\DTOs\Industries\CreateIndustryData;
use App\DTOs\Industries\UpdateIndustryData;
use App\Models\SolutionIndustry;
use Illuminate\Database\Eloquent\Collection;

class IndustryService
{
    public function list(): Collection
    {
        return SolutionIndustry::orderBy('order')->get();
    }

    public function create(CreateIndustryData $data): SolutionIndustry
    {
        return SolutionIndustry::create([
            'name' => $data->name,
            'slug' => $data->slug,
            'slug_ar' => $data->slugAr,
            'order' => $data->order,
        ]);
    }

    public function update(SolutionIndustry $industry, UpdateIndustryData $data): SolutionIndustry
    {
        $industry->update([
            'name' => $data->name,
            'slug' => $data->slug,
            'slug_ar' => $data->slugAr,
            'order' => $data->order,
        ]);

        return $industry;
    }

    public function delete(SolutionIndustry $industry): void
    {
        $industry->releaseSlugAndDelete();
    }
}
