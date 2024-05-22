<?php

namespace VV\Classify\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Statamic\Extend\Manifest;
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

        $app->make(Manifest::class)->manifest = [
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

        $configs = [
            'assets', 'cp', 'forms', 'routes', 'static_caching',
            'sites', 'stache', 'system', 'users',
        ];

        foreach ($configs as $config) {
            $path = __DIR__."/../vendor/statamic/cms/config/{$config}.php";
            
            // Statamic 5 does not have a `sites` config anymore, so we better check first
            if (file_exists($path)) {
                $app['config']->set("statamic.$config", require($path));
            }
        }

        // Setting the user repository to the default flat file system
        $app['config']->set('statamic.users.repository', 'file');

        // Assume the pro edition within tests
        $app['config']->set('statamic.editions.pro', false);
    }
}
