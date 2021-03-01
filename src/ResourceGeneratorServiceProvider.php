<?php

namespace Pantherify\YALNT;

use Illuminate\Support\ServiceProvider;
use Pantherify\YALNT\Console\Commands\ResourceGeneratorCommand;

class ResourceGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Publishes configuration file.
     *
     * @return  void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => $this->app->make('path.config') . DIRECTORY_SEPARATOR . 'yalnt.php'
        ], 'config');

        $this->loadViewsFrom(__DIR__ . '/Views/', 'yalnt');
    }

    /**
     * Make config publishment optional by merging the config from the package.
     *
     * @return  void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'yalnt');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ResourceGeneratorCommand::class,
            ]);
        }
    }
}
