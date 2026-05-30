<?php

namespace App\Observers;

use App\Models\Nationality;
use Illuminate\Support\Facades\Cache;

class NationalityObserver
{
    /**
     * Handle the Nationality "created" event.
     */
    public function created(Nationality $nationality): void
    {
        Cache::forget('nationalitiesCache');

    }

    /**
     * Handle the Nationality "updated" event.
     */
    public function updated(Nationality $nationality): void
    {
        Cache::forget('nationalitiesCache');

    }

    /**
     * Handle the Nationality "deleted" event.
     */
    public function deleted(Nationality $nationality): void
    {
        Cache::forget('nationalitiesCache');

    }

    /**
     * Handle the Nationality "restored" event.
     */
    public function restored(Nationality $nationality): void
    {
        Cache::forget('nationalitiesCache');

    }

    /**
     * Handle the Nationality "force deleted" event.
     */
    public function forceDeleted(Nationality $nationality): void
    {
        Cache::forget('nationalitiesCache');

    }
}
