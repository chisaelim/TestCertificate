<?php

namespace App\Observers;

use App\Models\Commune;
use Illuminate\Support\Facades\Cache;

class CommuneObserver
{
    /**
     * Handle the Commune "created" event.
     */
    public function created(Commune $commune): void
    {
        Cache::forget('communesCache'.$commune->id_parent);
    }

    /**
     * Handle the Commune "updated" event.
     */
    public function updated(Commune $commune): void
    {
        Cache::forget('communesCache'.$commune->id_parent);
    }

    /**
     * Handle the Commune "deleted" event.
     */
    public function deleted(Commune $commune): void
    {
        Cache::forget('communesCache'.$commune->id_parent);
    }

    /**
     * Handle the Commune "restored" event.
     */
    public function restored(Commune $commune): void
    {
        Cache::forget('communesCache'.$commune->id_parent);
    }

    /**
     * Handle the Commune "force deleted" event.
     */
    public function forceDeleted(Commune $commune): void
    {
        Cache::forget('communesCache'.$commune->id_parent);
    }
}
