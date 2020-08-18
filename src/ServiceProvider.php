<?php

namespace VV\Classify;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $modifiers = [
        \VV\Classify\Modifiers\Classify::class,
    ];

    public function boot()
    {
        parent::boot();

        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'classify');

        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('classify.php'),
            ], 'Classify config');
        }
    }
}
