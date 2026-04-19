<?php

namespace Perturbatio\LivewireMarkdownNavigator;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Perturbatio\LivewireMarkdownNavigator\Commands\CacheDocs;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewireMarkdownNavigatorServiceProvider extends PackageServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        View::addNamespace(
            namespace: 'livewire-markdown-navigator',
            hints: [__DIR__.'/../resources/views'],
        );

        Blade::componentNamespace(
            namespace: 'Perturbatio\\LivewireMarkdownNavigator\\View\\Components',
            prefix: 'livewire-markdown-navigator',
        );

        Livewire::addNamespace(
            namespace: 'perturbatio',
            viewPath: __DIR__.'/../resources/views/livewire',
        );

    }

    public function configurePackage(Package $package): void
    {
        /**
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('livewire-markdown-navigator')
            ->hasConfigFile()
            ->hasViews()
            ->hasAssets()
            ->hasCommand(CacheDocs::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishAssets()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('perturbatio/livewire-markdown-navigator');
            });
    }
}
