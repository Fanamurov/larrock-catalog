<?php

namespace Larrock\ComponentCatalog;

use Illuminate\Support\ServiceProvider;
use Larrock\ComponentCatalog\Middleware\CatalogSearch;
use Larrock\ComponentCatalog\Middleware\RandomCatalogItems;

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

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/larrock')
        ]);

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
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
    }
}