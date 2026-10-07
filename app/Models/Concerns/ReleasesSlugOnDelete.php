<?php

namespace App\Models\Concerns;

/**
 * Soft-deleted rows still hold their unique slug in the database, so a new
 * record could never reuse it. Deleting appends the id to the slug, which frees
 * the original for reuse. Records are not restored through the API.
 */
trait ReleasesSlugOnDelete
{
    public function releaseSlugAndDelete(): void
    {
        $suffix = '-deleted-'.$this->getKey();

        $this->forceFill([
            'slug' => $this->slug.$suffix,
            'slug_ar' => filled($this->slug_ar) ? $this->slug_ar.$suffix : null,
        ])->save();

        $this->delete();
    }
}
