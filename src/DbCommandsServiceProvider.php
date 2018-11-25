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
            __DIR__.'/env/mysql/' => base_path()
        ], 'envmysql');

        $this->publishes([
            __DIR__.'/env/sqlite/' => base_path()
        ], 'envsqlite');
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
