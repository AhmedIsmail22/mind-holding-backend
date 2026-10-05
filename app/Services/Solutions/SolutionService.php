<?php

namespace App\Services\Solutions;

use App\DTOs\Solutions\CreateSolutionData;
use App\DTOs\Solutions\UpdateSolutionData;
use App\Models\Solution;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class SolutionService
{
    public function list(): Collection
    {
        return Solution::with(['industry', 'media', 'relatedSolutions', 'services'])->orderBy('order')->get();
    }

    public function listPublished(?string $industrySlug = null): Collection
    {
        $query = Solution::where('is_published', true)
            ->with(['industry', 'media'])
            ->orderByDesc('is_flagship')
            ->orderBy('order');

        if ($industrySlug !== null) {
            $query->whereHas('industry', fn ($q) => $q->where('slug', $industrySlug));
        }

        return $query->get();
    }

    public function findPublishedBySlug(string $slug): Solution
    {
        return Solution::where('slug', $slug)->where('is_published', true)->firstOrFail();
    }

    /**
     * @param  UploadedFile[]  $mockups
     */
    public function create(CreateSolutionData $data, array $mockups = []): Solution
    {
        $solution = Solution::create([
            'solution_industry_id' => $data->solutionIndustryId,
            'name' => $data->name,
            'slug' => $data->slug,
            'target_audience' => $data->targetAudience,
            'problem_points' => $data->problemPoints,
            'features' => $data->features,
            'deliverables' => $data->deliverables,
            'demo_url' => $data->demoUrl,
            'demo_credentials' => $data->demoCredentials,
            'is_flagship' => $data->isFlagship,
            'is_published' => $data->isPublished,
            'is_draft' => false,
            'order' => $data->order,
        ]);

        $solution->relatedSolutions()->sync($data->relatedSolutionIds);
        $solution->services()->sync($data->relatedServiceIds);

        foreach ($mockups as $mockup) {
            $solution->addMedia($mockup)->toMediaCollection('mockups');
        }

        return $solution;
    }

    /**
     * @param  UploadedFile[]  $mockups
     */
    public function update(Solution $solution, UpdateSolutionData $data, array $mockups = []): Solution
    {
        $solution->update([
            'solution_industry_id' => $data->solutionIndustryId,
            'name' => $data->name,
            'slug' => $data->slug,
            'target_audience' => $data->targetAudience,
            'problem_points' => $data->problemPoints,
            'features' => $data->features,
            'deliverables' => $data->deliverables,
            'demo_url' => $data->demoUrl,
            'demo_credentials' => $data->demoCredentials,
            'is_flagship' => $data->isFlagship,
            'is_published' => $data->isPublished,
            'is_draft' => false,
            'order' => $data->order,
        ]);

        $solution->relatedSolutions()->sync($data->relatedSolutionIds);
        $solution->services()->sync($data->relatedServiceIds);

        foreach ($mockups as $mockup) {
            $solution->addMedia($mockup)->toMediaCollection('mockups');
        }

        return $solution;
    }

    public function delete(Solution $solution): void
    {
        $solution->delete();
    }

    /**
     * @param  int[]  $orderedIds
     */
    public function reorder(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                Solution::whereKey($id)->update(['order' => $index]);
            }
        });
    }
}
