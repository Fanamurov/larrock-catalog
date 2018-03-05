<?php

namespace Larrock\ComponentCatalog;

use Illuminate\Support\ServiceProvider;
use Larrock\ComponentCatalog\Middleware\CatalogSearch;
use Larrock\ComponentCatalog\Middleware\RandomCatalogItems;
use Larrock\ComponentCatalog\Middleware\SoputkaCatalogItems;

class LarrockComponentCatalogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/views', 'larrock');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/larrock')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('larrockcatalog', function() {
            $class = config('larrock.components.catalog', CatalogComponent::class);
            return new $class;
        });

        $this->app['router']->aliasMiddleware('CatalogSearch', CatalogSearch::class);
        $this->app['router']->aliasMiddleware('RandomCatalogItems', RandomCatalogItems::class);
        $this->app['router']->aliasMiddleware('SoputkaCatalogItems', SoputkaCatalogItems::class);
    }
}