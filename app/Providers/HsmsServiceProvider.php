<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\HSms;

class HSmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('HSmsService', function()
        {
            return new HSms();
        });
    }
}
