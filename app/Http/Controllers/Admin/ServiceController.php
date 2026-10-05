<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\CreateServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
use App\Http\Resources\Admin\ServiceResource;
use App\Models\Service;
use App\Services\Services\ServiceService;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(ServiceResource::collection($this->serviceService->list()));
    }

    public function store(CreateServiceRequest $request): JsonResponse
    {
        $service = $this->serviceService->create($request->toDto());

        return $this->success(new ServiceResource($service), 'Service created.', 201);
    }

    public function show(Service $service): JsonResponse
    {
        return $this->success(new ServiceResource($service));
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service = $this->serviceService->update($service, $request->toDto());

        return $this->success(new ServiceResource($service), 'Service updated.');
    }

    public function destroy(Service $service): JsonResponse
    {
        $this->serviceService->delete($service);

        return $this->success(null, 'Service deleted.');
    }
}
