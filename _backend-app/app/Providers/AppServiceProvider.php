<?php

namespace App\Providers;

use App\Models\Commune;
use App\Models\District;
use App\Models\Ethnicity;
use App\Models\Gender;
use App\Models\Nationality;
use App\Models\Province;
use App\Models\Religion;
use App\Models\Village;
use App\Observers\CommuneObserver;
use App\Observers\DistrictObserver;
use App\Observers\EthnicityObserver;
use App\Observers\GenderObserver;
use App\Observers\NationalityObserver;
use App\Observers\ProvinceObserver;
use App\Observers\ReligionObserver;
use App\Observers\VillageObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Province::observe(ProvinceObserver::class);
        District::observe(DistrictObserver::class);
        Commune::observe(CommuneObserver::class);
        Village::observe(VillageObserver::class);
        Gender::observe(GenderObserver::class);
        Ethnicity::observe(EthnicityObserver::class);
        Nationality::observe(NationalityObserver::class);
        Religion::observe(ReligionObserver::class);
    }
}
