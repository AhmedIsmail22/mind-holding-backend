<?php

namespace App\Services\Redirects;

use App\DTOs\Redirects\RedirectData;
use App\Models\Redirect;
use Illuminate\Database\Eloquent\Collection;

class RedirectService
{
    public function __construct(
        private readonly FrontendRevalidationNotifier $revalidation,
    ) {}

    public function list(): Collection
    {
        return Redirect::orderBy('old_path')->get();
    }

    public function create(RedirectData $data): Redirect
    {
        $redirect = Redirect::create(['old_path' => $data->oldPath, 'new_path' => $data->newPath]);

        $this->revalidation->created($redirect);

        return $redirect;
    }

    public function update(Redirect $redirect, RedirectData $data): Redirect
    {
        $previousOldPath = $redirect->old_path;
        $previousNewPath = $redirect->new_path;

        $redirect->update(['old_path' => $data->oldPath, 'new_path' => $data->newPath]);

        $this->revalidation->updated($redirect, $previousOldPath, $previousNewPath);

        return $redirect;
    }

    public function delete(Redirect $redirect): void
    {
        $oldPath = $redirect->old_path;
        $newPath = $redirect->new_path;

        $redirect->delete();

        $this->revalidation->deleted($oldPath, $newPath);
    }

    /**
     * The 301 target for a path, or null if none. Trailing slashes are ignored.
     */
    public function resolve(string $path): ?Redirect
    {
        return Redirect::where('old_path', RedirectData::normalize($path))->first();
    }
}
