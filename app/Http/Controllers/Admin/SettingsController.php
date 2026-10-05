<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Http\Resources\Admin\SettingsResource;
use App\Services\Settings\SettingsService;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingsService $settingsService,
    ) {}

    public function show(): JsonResponse
    {
        return $this->success(new SettingsResource($this->settingsService->current()));
    }

    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $setting = $this->settingsService->update($request->toDto());

        return $this->success(new SettingsResource($setting), 'Settings updated.');
    }
}
