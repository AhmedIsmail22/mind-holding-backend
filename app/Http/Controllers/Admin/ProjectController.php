<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\CreateProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\Admin\ProjectResource;
use App\Models\Project;
use App\Services\Projects\ProjectService;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(ProjectResource::collection($this->projectService->list()));
    }

    public function store(CreateProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->create($request->toDto(), $request->file('images', []), $request->validated('images_alt'));

        return $this->success(new ProjectResource($project), 'Project created.', 201);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load(['media', 'services']);

        return $this->success(new ProjectResource($project));
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project = $this->projectService->update($project, $request->toDto(), $request->file('images', []), $request->validated('images_alt'));

        return $this->success(new ProjectResource($project), 'Project updated.');
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->projectService->delete($project);

        return $this->success(null, 'Project deleted.');
    }
}
