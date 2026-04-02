<?php

namespace Perturbatio\LivewireMarkdownNavigator\Tests;

use Livewire\Livewire;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Perturbatio\LivewireMarkdownNavigator\LivewireMarkdownNavigatorServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->registerLivewireComponents();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            LivewireMarkdownNavigatorServiceProvider::class,
        ];
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('markdown-navigator');
    }

    protected function defineEnvironment($app)
    {
        tap($app['config'], function ($config) {
            $config->set('cache.default', 'array');
        });
    }

    public function getEnvironmentSetUp($app)
    {
        //        config()->set('database.default', 'testing');

        /*
         foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__ . '/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
         }
         */
    }
}
