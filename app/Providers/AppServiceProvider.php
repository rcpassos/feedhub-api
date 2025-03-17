<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        $this->configureApiRateLimiter();
        $this->configureCommands();
        $this->configureDates();
        $this->configureModels();
        $this->configureUrl();
    }

    private function configureApiRateLimiter(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            App::isProduction()
        );
    }

    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureModels(): void
    {
        // throw an exception if access to a property that does not exist
        Model::shouldBeStrict();

        // disable mass assignment protection
        // Model::unguard();
    }

    private function configureUrl(): void
    {
        URL::forceScheme('https');
    }
}
