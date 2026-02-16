<?php

namespace VV\Classify\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Statamic\Statamic;

class TestCase extends OrchestraTestCase
{
    protected $shouldFakeVersion = true;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if ($this->shouldFakeVersion) {
            \Facades\Statamic\Version::shouldReceive('get')->andReturn('4.0.0-testing');
        }
    }

    /**
     * Load package service provider.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Statamic\Providers\StatamicServiceProvider::class,
            \VV\Classify\ServiceProvider::class,
        ];
    }

    /**
     * Load package alias.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Statamic' => Statamic::class,
        ];
    }

    /**
     * Load Environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $manifestClass = class_exists(\Statamic\Addons\Manifest::class)
            ? \Statamic\Addons\Manifest::class
            : \Statamic\Extend\Manifest::class;

        $app->make($manifestClass)->manifest = [
            'visuellverstehen/statamic-classify' => [
                'id'        => 'visuellverstehen/statamic-classify',
                'namespace' => 'VV\\Classify\\',
            ],
        ];
    }

    /**
     * Resolve the Application Configuration and set the Statamic configuration.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $configDirectory = __DIR__.'/../vendor/statamic/cms/config';
        $configPaths = glob($configDirectory.'/*.php') ?: [];

        foreach ($configPaths as $path) {
            $config = basename($path, '.php');
            $app['config']->set("statamic.$config", require $path);
        }

        // Setting the user repository to the default flat file system
        $app['config']->set('statamic.users.repository', 'file');

        // Assume the free edition within tests when available.
        if ($app['config']->has('statamic.editions.pro')) {
            $app['config']->set('statamic.editions.pro', false);
        }
    }
}
