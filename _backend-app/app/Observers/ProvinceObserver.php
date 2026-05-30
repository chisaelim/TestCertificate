<?php

namespace App\Observers;

use App\Models\Province;
use Illuminate\Support\Facades\Cache;

class ProvinceObserver
{
    /**
     * Handle the Province "created" event.
     */
    public function created(Province $province): void
    {
        Cache::forget('provincesCache');

    }

    /**
     * Handle the Province "updated" event.
     */
    public function updated(Province $province): void
    {
        Cache::forget('provincesCache');

    }

    /**
     * Handle the Province "deleted" event.
     */
    public function deleted(Province $province): void
    {
        Cache::forget('provincesCache');

    }

    /**
     * Handle the Province "restored" event.
     */
    public function restored(Province $province): void
    {
        Cache::forget('provincesCache');

    }

    /**
     * Handle the Province "force deleted" event.
     */
    public function forceDeleted(Province $province): void
    {
        Cache::forget('provincesCache');

    }
}
