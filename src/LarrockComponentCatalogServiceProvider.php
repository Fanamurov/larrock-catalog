<?php

namespace Larrock\ComponentCatalog;

use Illuminate\Support\ServiceProvider;

class LarrockComponentCatalogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'larrock');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/larrock')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make(CatalogComponent::class);

        $timestamp = date('Y_m_d_His', time());
        $migrations = [];
        if ( !class_exists('CreateLarrockCatalogTable')){
            $migrations = [__DIR__.'/../database/migrations/0000_00_00_000000_create_catalog_table.php' => database_path('migrations/'.$timestamp.'_create_catalog_table.php')];
        }
        if ( !class_exists('CreateLarrockCategoryCatalogTable')){
            $migrations = [__DIR__.'/../database/migrations/0000_00_00_000000_create_category_catalog_table.php' => database_path('migrations/'.$timestamp.'_create_category_catalog_table.php')];
        }
        if ( !class_exists('CreateLarrockOptionParamTable')){
            $migrations = [__DIR__.'/../database/migrations/0000_00_00_000000_create_option_param_table.php' => database_path('migrations/'.$timestamp.'_create_option_param_table.php')];
        }
        if ( !class_exists('CreateLarrockOptionParamLinkTable')){
            $migrations = [__DIR__.'/../database/migrations/0000_00_00_000000_create_option_param_link_table.php' => database_path('migrations/'.$timestamp.'_create_option_param_link_table.php')];
        }
        if ( !class_exists('AddForeignKeysToLarrockCatalogTable')){
            $migrations = [__DIR__.'/../database/migrations/0000_00_00_000000_add_foreign_keys_to_catalog_table.php' => database_path('migrations/'.$timestamp.'_add_foreign_keys_to_catalog_table.php')];
        }
        if ( !class_exists('AddForeignKeysToLarrockCategoryCatalogTable')){
            $migrations = [__DIR__.'/../database/migrations/0000_00_00_000000_add_foreign_keys_to_category_catalog_table.php' => database_path('migrations/'.$timestamp.'_add_foreign_keys_to_category_catalog_table.php')];
        }
        if ( !class_exists('AddForeignKeysToLarrockOptionParamLinkTable')){
            $migrations = [__DIR__.'/../database/migrations/0000_00_00_000000_add_foreign_keys_to_option_param_link_table.php' => database_path('migrations/'.$timestamp.'_add_foreign_keys_to_option_param_link_table')];
        }

        $this->publishes([
            $migrations
        ], 'migrations');
    }
}
