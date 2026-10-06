<?php

namespace App\Services\Redirects;

use App\DTOs\Redirects\RedirectData;
use App\Models\Redirect;
use Illuminate\Database\Eloquent\Collection;

class RedirectService
{
    public function list(): Collection
    {
        return Redirect::orderBy('old_path')->get();
    }

    public function create(RedirectData $data): Redirect
    {
        return Redirect::create(['old_path' => $data->oldPath, 'new_path' => $data->newPath]);
    }

    public function update(Redirect $redirect, RedirectData $data): Redirect
    {
        $redirect->update(['old_path' => $data->oldPath, 'new_path' => $data->newPath]);

        return $redirect;
    }

    public function delete(Redirect $redirect): void
    {
        $redirect->delete();
    }

    /**
     * The 301 target for a path, or null if none. Trailing slashes are ignored.
     */
    public function resolve(string $path): ?Redirect
    {
        return Redirect::where('old_path', RedirectData::normalize($path))->first();
    }
}
