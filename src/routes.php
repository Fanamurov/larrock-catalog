<?php

Route::get('/catalog', function(){ return Redirect::to('/'); });
Route::get('/root', 'Larrock\ComponentCatalog\CatalogController@getCategoryRoot')->name('catalog.root');
Route::get('/yml.xml', 'Larrock\ComponentCatalog\CatalogController@YML')->name('catalog.yml');
Route::get('/catalog/all', 'Larrock\ComponentCatalog\CatalogController@getAllTovars')->name('catalog.all');
Route::get('/catalog/{category}/{category2?}/{category3?}/{category4?}', 'Larrock\ComponentCatalog\CatalogController@getCategoryExpanded')->name('catalog.category');
Route::any('/search/catalog/serp/{words?}', 'Larrock\ComponentCatalog\CatalogController@searchResult')->name('catalog.search.words');
Route::get('/search/catalog', 'Larrock\ComponentCatalog\CatalogController@searchItem')->name('catalog.search');
Route::post('/ajax/editPerPage', 'Larrock\ComponentCatalog\CatalogController@editPerPage')->name('catalog.editPerPage');
Route::post('/ajax/sort', 'Larrock\ComponentCatalog\CatalogController@sort')->name('catalog.sort');
Route::post('/ajax/vid', 'Larrock\ComponentCatalog\CatalogController@vid')->name('catalog.vid');

Route::group(['prefix' => 'admin', 'middleware'=> ['web', 'level:2', 'LarrockAdminMenu', 'SaveAdminPluginsData', 'SiteSearchAdmin']], function(){
    Route::resource('catalog', 'Larrock\ComponentCatalog\AdminCatalogController', ['names' => [
        'index' => 'admin.catalog.index',
        'show' => 'admin.catalog.show',
        'edit' => 'admin.catalog.edit',
    ]]);
    Route::post('catalog/copy', 'Larrock\ComponentCatalog\AdminCatalogController@copy')->name('catalog.admin.copy');
    Route::post('/ajax/getTovar', 'Larrock\ComponentCatalog\AdminCatalogController@getTovar')->name('catalog.admin.getTovar');
});