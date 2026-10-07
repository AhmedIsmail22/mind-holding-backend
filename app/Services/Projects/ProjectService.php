<?php

namespace App\Services\Projects;

use App\DTOs\Projects\CreateProjectData;
use App\DTOs\Projects\UpdateProjectData;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

class ProjectService
{
    public function list(): Collection
    {
        return Project::with(['media', 'services'])->orderBy('order')->get();
    }

    public function listPublished(): Collection
    {
        return Project::where('is_published', true)->with('media')->orderBy('order')->get();
    }

    public function findPublishedBySlug(string $slug): Project
    {
        return Project::where('is_published', true)
            ->where(fn ($query) => $query->where('slug', $slug)->orWhere('slug_ar', $slug))
            ->firstOrFail();
    }

    /**
     * @param  UploadedFile[]  $images
     */
    public function create(CreateProjectData $data, array $images = []): Project
    {
        $project = Project::create([
            'slug' => $data->slug,
            'slug_ar' => $data->slugAr,
            'client_name' => $data->clientName,
            'hide_client_name' => $data->hideClientName,
            'generic_description' => $data->genericDescription,
            'overview' => $data->overview,
            'challenge' => $data->challenge,
            'solution' => $data->solution,
            'technologies' => $data->technologies,
            'live_url' => $data->liveUrl,
            'is_published' => $data->isPublished,
            'order' => $data->order,
        ]);

        $project->services()->sync($data->relatedServiceIds);

        foreach ($images as $image) {
            $project->addMedia($image)->toMediaCollection('images');
        }

        return $project;
    }

    /**
     * @param  UploadedFile[]  $images
     */
    public function update(Project $project, UpdateProjectData $data, array $images = []): Project
    {
        $project->update([
            'slug' => $data->slug,
            'slug_ar' => $data->slugAr,
            'client_name' => $data->clientName,
            'hide_client_name' => $data->hideClientName,
            'generic_description' => $data->genericDescription,
            'overview' => $data->overview,
            'challenge' => $data->challenge,
            'solution' => $data->solution,
            'technologies' => $data->technologies,
            'live_url' => $data->liveUrl,
            'is_published' => $data->isPublished,
            'order' => $data->order,
        ]);

        $project->services()->sync($data->relatedServiceIds);

        foreach ($images as $image) {
            $project->addMedia($image)->toMediaCollection('images');
        }

        return $project;
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }
}
