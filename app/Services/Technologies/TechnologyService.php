<?php

namespace App\Services\Technologies;

use App\DTOs\Technologies\CreateTechnologyData;
use App\DTOs\Technologies\UpdateTechnologyData;
use App\Models\Technology;
use App\Support\Media\MediaAsset;
use Illuminate\Database\Eloquent\Collection;

class TechnologyService
{
    public function list(): Collection
    {
        return Technology::with('media')->orderBy('order')->get();
    }

    public function create(CreateTechnologyData $data): Technology
    {
        $technology = Technology::create([
            'name' => $data->name,
            'category' => $data->category,
            'order' => $data->order,
        ]);

        if ($data->logo !== null) {
            MediaAsset::attach($technology, $data->logo, 'logo', $data->logoAlt);
        }

        return $technology;
    }

    public function update(Technology $technology, UpdateTechnologyData $data): Technology
    {
        $technology->update([
            'name' => $data->name,
            'category' => $data->category,
            'order' => $data->order,
        ]);

        if ($data->logo !== null) {
            MediaAsset::replace($technology, $data->logo, 'logo', $data->logoAlt);
        } else {
            MediaAsset::setAlt($technology->getFirstMedia('logo'), $data->logoAlt);
        }

        return $technology;
    }

    public function delete(Technology $technology): void
    {
        $technology->delete();
    }
}
