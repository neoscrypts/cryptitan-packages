<?php

namespace NeoScrypts\Multipay;

use Illuminate\Support\ServiceProvider;

class MultipayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMultipay();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishResources();
    }

    /**
     * Register multipay class
     *
     * @return void
     */
    protected function registerMultipay()
    {
        $this->app->singleton('multipay', function ($app) {
            return new Multipay($app->config->get('multipay', []));
        });
    }

    /**
     * Publish package resources
     *
     * @return void
     */
    protected function publishResources()
    {
        $this->publishes([
            __DIR__ . '/./config/multipay.php' => config_path('multipay.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/./config/multipay.php', 'multipay'
        );
    }
}
