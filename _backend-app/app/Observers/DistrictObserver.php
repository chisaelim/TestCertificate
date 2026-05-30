<?php

namespace App\Observers;

use App\Models\District;
use Illuminate\Support\Facades\Cache;

class DistrictObserver
{
    /**
     * Handle the District "created" event.
     */
    public function created(District $district): void
    {
        Cache::forget('districtsCache' . $district->parent_id);
    }

    /**
     * Handle the District "updated" event.
     */
    public function updated(District $district): void
    {
        Cache::forget('districtsCache' . $district->parent_id);
    }

    /**
     * Handle the District "deleted" event.
     */
    public function deleted(District $district): void
    {
        Cache::forget('districtsCache' . $district->parent_id);
    }

    /**
     * Handle the District "restored" event.
     */
    public function restored(District $district): void
    {
        Cache::forget('districtsCache' . $district->parent_id);
    }

    /**
     * Handle the District "force deleted" event.
     */
    public function forceDeleted(District $district): void
    {
        Cache::forget('districtsCache' . $district->parent_id);
    }
}
