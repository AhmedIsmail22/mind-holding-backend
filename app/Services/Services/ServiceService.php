<?php

namespace App\Services\Services;

use App\DTOs\Services\CreateServiceData;
use App\DTOs\Services\UpdateServiceData;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ServiceService
{
    public function list(): Collection
    {
        return Service::orderBy('order')->get();
    }

    /**
     * @return array{software: Collection<int, Service>, marketing: Collection<int, Service>}
     */
    public function listPublishedGrouped(): array
    {
        $services = Service::where('is_published', true)->orderBy('order')->get();

        return [
            'software' => $services->where('group', 'software')->values(),
            'marketing' => $services->where('group', 'marketing')->values(),
        ];
    }

    public function findPublishedBySlug(string $slug): Service
    {
        return Service::where('slug', $slug)->where('is_published', true)->firstOrFail();
    }

    public function create(CreateServiceData $data): Service
    {
        return Service::create([
            'group' => $data->group,
            'name' => $data->name,
            'slug' => $data->slug,
            'description' => $data->description,
            'deliverables' => $data->deliverables,
            'delivery_steps' => $data->deliverySteps,
            'is_published' => $data->isPublished,
            'is_draft' => false,
            'order' => $data->order,
        ]);
    }

    public function update(Service $service, UpdateServiceData $data): Service
    {
        $service->update([
            'group' => $data->group,
            'name' => $data->name,
            'slug' => $data->slug,
            'description' => $data->description,
            'deliverables' => $data->deliverables,
            'delivery_steps' => $data->deliverySteps,
            'is_published' => $data->isPublished,
            'is_draft' => false,
            'order' => $data->order,
        ]);

        return $service;
    }

    public function delete(Service $service): void
    {
        $service->delete();
    }
}
