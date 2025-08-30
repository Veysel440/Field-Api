<?php

namespace App\Observers;

use App\Models\Asset;
use App\Support\CacheVersion;

class AssetObserver
{
    public function created(Asset $asset): void
    {
        CacheVersion::bump('assets');
    }

    public function updated(Asset $asset): void
    {
        CacheVersion::bump('assets');
    }

    public function deleted(Asset $asset): void
    {
        CacheVersion::bump('assets');
    }

    public function restored(Asset $asset): void
    {
        CacheVersion::bump('assets');
    }

    public function forceDeleted(Asset $asset): void
    {
        CacheVersion::bump('assets');
    }
}
