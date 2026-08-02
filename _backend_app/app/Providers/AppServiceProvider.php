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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('requested_at', function () {
            return now();
        });
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

        // Allow public access to Scramble-generated API docs in all environments.
        // By default Scramble's RestrictedDocsAccess middleware only permits local viewing.
        Gate::define('viewApiDocs', fn ($user = null) => true);
    }
}
