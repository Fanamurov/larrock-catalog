<?php

use Larrock\ComponentCatalog\AdminCatalogController;
use Larrock\ComponentCatalog\CatalogController;

$middlewares = ['web', 'GetSeo'];
if(file_exists(base_path(). '/vendor/fanamurov/larrock-menu')){
    $middlewares[] = 'AddMenuFront';
}
if(file_exists(base_path(). '/vendor/fanamurov/larrock-blocks')){
    $middlewares[] = 'AddBlocksTemplate';
}
if(file_exists(base_path(). '/vendor/fanamurov/larrock-discount')){
    $middlewares[] = 'DiscountsShare';
}

Route::group(['middleware' => $middlewares], function(){
    Route::get('/catalog', function()
    {
        return Redirect::to('/');
    });

    Route::get('/root', [
        'as' => 'catalog.root', 'uses' => CatalogController::class .'@getCategoryRoot'
    ]);
    Route::get('/yml.xml', [
        'as' => 'catalog.yml', 'uses' => CatalogController::class .'@YML'
    ]);

    Route::get('/catalog/all', [
        'as' => 'catalog.all', 'uses' => CatalogController::class .'@getAllTovars'
    ]);

    Route::get('/catalog/{category}/{category2?}/{category3?}/{category4?}', [
        'as' => 'catalog.category', 'uses' => CatalogController::class .'@getCategoryExpanded'
    ]);

    Route::any('/search/catalog/serp/{words?}', [
        'as' => 'search.catalog', 'uses' => CatalogController::class .'@searchResult'
    ]);
    Route::get('/search/catalog', [
        'as' => 'search.catalog', 'uses' => CatalogController::class .'@searchItem'
    ]);

    Route::post('/ajax/editPerPage', [
        'as' => 'ajax.editPerPage', 'uses' => CatalogController::class .'@editPerPage'
    ]);
    Route::post('/ajax/sort', [
        'as' => 'ajax.sort', 'uses' => CatalogController::class .'@sort'
    ]);
    Route::post('/ajax/vid', [
        'as' => 'ajax.vid', 'uses' => CatalogController::class .'@vid'
    ]);
});

Route::group(['prefix' => 'admin', 'middleware'=> ['web', 'level:2', 'LarrockAdminMenu', 'SaveAdminPluginsData', 'SiteSearchAdmin']], function(){
    Route::resource('catalog', AdminCatalogController::class, ['names' => [
        'index' => 'admin.catalog.index',
        'show' => 'admin.catalog.show',
        'edit' => 'admin.catalog.edit',
    ]]);
    Route::post('catalog/copy', AdminCatalogController::class .'@copy');

    Route::post('/ajax/getTovar', [
        'as' => 'ajax.admin.getTovar', 'uses' => AdminCatalogController::class .'@getTovar'
    ]);
});