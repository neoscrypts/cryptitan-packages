<?php

namespace NeoScrypts\Installer;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('installer', function ($app) {
            return new Installer($app->make('filesystem'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Kernel $kernel)
    {
        $kernel->pushMiddleware(InstallerMiddleware::class);
    }
}