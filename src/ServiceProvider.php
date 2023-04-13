<?php

namespace VV\Classify;

use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    protected $modifiers = [
        \VV\Classify\Modifiers\Classify::class,
    ];
    
    protected $tags = [
        \VV\Classify\Tags\Classify::class,
    ];

    public function boot()
    {
        parent::boot();

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'classify');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('classify.php'),
            ], 'classify');
        }

        $this->app->singleton(ClassifyParser::class, HtmlParser::class);

        Statamic::afterInstalled(function ($command) {
            $command->call('vendor:publish', ['--tag' => 'classify']);
        });
    }
}
