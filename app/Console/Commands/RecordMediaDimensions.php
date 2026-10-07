<?php

namespace App\Console\Commands;

use App\Support\Media\MediaAsset;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RecordMediaDimensions extends Command
{
    protected $signature = 'media:record-dimensions';

    protected $description = 'Record width and height for every stored image so public responses can include them';

    public function handle(): int
    {
        $count = 0;

        Media::query()->chunkById(100, function ($items) use (&$count) {
            foreach ($items as $media) {
                MediaAsset::recordDimensions($media);
                $count++;
            }
        });

        $this->info("Recorded dimensions for {$count} media item(s).");

        return self::SUCCESS;
    }
}
