<?php

use Larrock\ComponentCatalog\AdminCatalogController;
use Larrock\ComponentCatalog\CatalogController;

Route::group(['middleware' => ['web', 'AddMenuFront', 'GetSeo', 'AddBlocksTemplate']], function(){
    Route::get('/catalog', function()
    {
        return Redirect::to('/');
    });

    Route::get('/catalog/all', [
        'as' => 'catalog.all', 'uses' => CatalogController::class .'@getAllTovars'
    ]);
    Route::get('/catalog/{category}', [
        'as' => 'catalog.category', 'uses' => CatalogController::class .'@getCategoryExpanded'
    ]);
    Route::get('/catalog/{category}/{child}', [
        'as' => 'catalog.category.child', 'uses' => CatalogController::class .'@getCategoryExpanded'
    ]);
    Route::get('/catalog/{category}/{child}/{grandson}', [
        'as' => 'catalog.category.grandson', 'uses' => CatalogController::class .'@getCategoryExpanded'
    ]);
    Route::get('/catalog/{category}/{child}/{grandson}/{item}', [
        'as' => 'catalog.category.grandson.item', 'uses' => CatalogController::class .'@getItem'
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

Route::group(['prefix' => 'admin', 'middleware'=> ['web', 'level:2', 'LarrockAdminMenu']], function(){
    Route::resource('catalog', AdminCatalogController::class, ['names' => [
        'index' => 'admin.catalog.index',
        'show' => 'admin.catalog.show',
        'edit' => 'admin.catalog.edit',
    ]]);
    Route::post('catalog/copy', AdminCatalogController::class .'@copy');

    Route::get('/', [
        'as' => 'admin.home', 'uses' => AdminCatalogController::class .'@index'
    ]); //Роут главной страницы админки

    Route::post('/ajax/getTovar', [
        'as' => 'ajax.admin.getTovar', 'uses' => AdminCatalogController::class .'@getTovar'
    ]);
});