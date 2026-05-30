<?php

namespace App\Observers;

use App\Models\Gender;
use Illuminate\Support\Facades\Cache;

class GenderObserver
{
    /**
     * Handle the Gender "created" event.
     */
    public function created(Gender $gender): void
    {
        Cache::forget('gendersCache');

    }

    /**
     * Handle the Gender "updated" event.
     */
    public function updated(Gender $gender): void
    {
        Cache::forget('gendersCache');

    }

    /**
     * Handle the Gender "deleted" event.
     */
    public function deleted(Gender $gender): void
    {
        Cache::forget('gendersCache');

    }

    /**
     * Handle the Gender "restored" event.
     */
    public function restored(Gender $gender): void
    {
        Cache::forget('gendersCache');

    }

    /**
     * Handle the Gender "force deleted" event.
     */
    public function forceDeleted(Gender $gender): void
    {
        Cache::forget('gendersCache');

    }
}
