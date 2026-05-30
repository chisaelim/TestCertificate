<?php

namespace App\Observers;

use App\Models\Religion;
use Illuminate\Support\Facades\Cache;

class ReligionObserver
{
    /**
     * Handle the Religion "created" event.
     */
    public function created(Religion $religion): void
    {
        Cache::forget('religionsCache');

    }

    /**
     * Handle the Religion "updated" event.
     */
    public function updated(Religion $religion): void
    {
        Cache::forget('religionsCache');

    }

    /**
     * Handle the Religion "deleted" event.
     */
    public function deleted(Religion $religion): void
    {
        Cache::forget('religionsCache');

    }

    /**
     * Handle the Religion "restored" event.
     */
    public function restored(Religion $religion): void
    {
        Cache::forget('religionsCache');

    }

    /**
     * Handle the Religion "force deleted" event.
     */
    public function forceDeleted(Religion $religion): void
    {
        Cache::forget('religionsCache');

    }
}
