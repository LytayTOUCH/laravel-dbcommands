<?php

namespace AseanCode\DbCommands;

use Illuminate\Support\ServiceProvider;

class DbCommandsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\CreateDatabase::class,
                Commands\DropDatabase::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/env/' => base_path()
        ], 'envfiles');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
