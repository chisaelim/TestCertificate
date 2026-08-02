<?php

namespace App\Observers;

use App\Models\Village;
use Illuminate\Support\Facades\Cache;

class VillageObserver
{
    /**
     * Handle the Village "created" event.
     */
    public function created(Village $village): void
    {
        Cache::forget('villagesCache' . $village->parent_id);

    }

    /**
     * Handle the Village "updated" event.
     */
    public function updated(Village $village): void
    {
        Cache::forget('villagesCache' . $village->parent_id);

    }

    /**
     * Handle the Village "deleted" event.
     */
    public function deleted(Village $village): void
    {
        Cache::forget('villagesCache' . $village->parent_id);

    }

    /**
     * Handle the Village "restored" event.
     */
    public function restored(Village $village): void
    {
        Cache::forget('villagesCache' . $village->parent_id);

    }

    /**
     * Handle the Village "force deleted" event.
     */
    public function forceDeleted(Village $village): void
    {
        Cache::forget('villagesCache' . $village->parent_id);

    }
}
