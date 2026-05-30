<?php

namespace App\Observers;

use App\Models\Ethnicity;
use Illuminate\Support\Facades\Cache;

class EthnicityObserver
{
    /**
     * Handle the Ethnicity "created" event.
     */
    public function created(Ethnicity $ethnicity): void
    {
        Cache::forget('ethnicitiesCache');

    }

    /**
     * Handle the Ethnicity "updated" event.
     */
    public function updated(Ethnicity $ethnicity): void
    {
        Cache::forget('ethnicitiesCache');

    }

    /**
     * Handle the Ethnicity "deleted" event.
     */
    public function deleted(Ethnicity $ethnicity): void
    {
        Cache::forget('ethnicitiesCache');

    }

    /**
     * Handle the Ethnicity "restored" event.
     */
    public function restored(Ethnicity $ethnicity): void
    {
        Cache::forget('ethnicitiesCache');

    }

    /**
     * Handle the Ethnicity "force deleted" event.
     */
    public function forceDeleted(Ethnicity $ethnicity): void
    {
        Cache::forget('ethnicitiesCache');

    }
}
