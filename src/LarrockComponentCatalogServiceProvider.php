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
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(__DIR__.'/views', 'larrock');

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

        $timestamp = date('Y_m_d_His', time());
        $timestamp_after = date('Y_m_d_His', time()+10);

        $migrations = [];
        if ( !class_exists('CreateCatalogTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_catalog_table.php'] = database_path('migrations/'.$timestamp.'_create_catalog_table.php');
        }
        if ( !class_exists('CreateCategoryCatalogTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_category_catalog_table.php'] = database_path('migrations/'.$timestamp.'_create_category_catalog_table.php');
        }
        if ( !class_exists('CreateOptionParamTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_option_param_table.php'] = database_path('migrations/'.$timestamp.'_create_option_param_table.php');
        }
        if ( !class_exists('CreateOptionParamLinkTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_option_param_link_table.php'] = database_path('migrations/'.$timestamp.'_create_option_param_link_table.php');
        }
        if ( !class_exists('AddForeignKeysToCatalogTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_add_foreign_keys_to_catalog_table.php'] = database_path('migrations/'.$timestamp_after.'_add_foreign_keys_to_catalog_table.php');
        }
        if ( !class_exists('AddForeignKeysToCategoryCatalogTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_add_foreign_keys_to_category_catalog_table.php'] = database_path('migrations/'.$timestamp_after.'_add_foreign_keys_to_category_catalog_table.php');
        }
        if ( !class_exists('AddForeignKeysToOptionParamLinkTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_add_foreign_keys_to_option_param_link_table.php'] = database_path('migrations/'.$timestamp_after.'_add_foreign_keys_to_option_param_link_table.php');
        }

        $this->publishes($migrations, 'migrations');
    }
}
