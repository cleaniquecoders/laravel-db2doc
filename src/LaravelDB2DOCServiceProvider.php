<?php

namespace CleaniqueCoders\LaravelDB2DOC;

use Illuminate\Support\ServiceProvider;

class LaravelDB2DOCServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \CleaniqueCoders\LaravelDB2DOC\Console\Commands\LaravelDb2DocCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
