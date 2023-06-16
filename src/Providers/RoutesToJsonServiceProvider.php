<?php

namespace Innoboxrr\RoutesToJson\Providers;

use Illuminate\Support\ServiceProvider;
use Innoboxrr\RoutesToJson\Console\Commands\RouteToJsonCommand;

class RoutesToJsonServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->mergeConfigFrom(__DIR__.'/../../config/routes-to-json.php', 'routes-to-json');

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
        $this->commands([
            RouteToJsonCommand::class,
        ]);

        if ($this->app->runningInConsole()) {

            $this->publishes([__DIR__.'/../../config/routes-to-json.php' => config_path('routes-to-json.php')], 'config');

        }

    }
}
