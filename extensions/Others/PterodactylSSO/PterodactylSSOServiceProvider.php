<?php

namespace Paymenter\Extensions\Others\PterodactylSSO;

use Illuminate\Support\ServiceProvider;

class PterodactylSSOServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register the main extension class
        $this->app->singleton('extensions.others.pterodactyl-sso', function ($app) {
            return new \Paymenter\Extensions\Others\PterodactylSSO\PterodactylSSO($app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'pterodactyl-sso');
        
        // Register routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }
}
