<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\ProjectListResource;
use App\Http\Resources\Public\ProjectResource;
use App\Services\Projects\ProjectService;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService,
    ) {}

    public function index(): JsonResponse
    {
        // Returns an empty array when nothing is published, which is how
        // the frontend knows to hide the Work nav link (SRS §3.4/§6).
        return $this->success(ProjectListResource::collection($this->projectService->listPublished()));
    }

    public function show(string $slug): JsonResponse
    {
        return $this->success(new ProjectResource($this->projectService->findPublishedBySlug($slug)));
    }
}
