<?php

Route::get('/catalog', function(){ return Redirect::to('/'); });
Route::get('/root', 'Larrock\ComponentCatalog\CatalogController@getCategoryRoot')->name('catalog.root');
Route::get('/yml.xml', 'Larrock\ComponentCatalog\CatalogController@YML')->name('catalog.yml');
Route::get('/catalog/all', 'Larrock\ComponentCatalog\CatalogController@getAllTovars')->name('catalog.all');

Route::get('/catalog/{category}/{category2?}/{category3?}/{category4?}', 'Larrock\ComponentCatalog\CatalogController@getCategory')
    ->name('catalog.category');

Route::any('/search/catalog/serp/{words?}', 'Larrock\ComponentCatalog\CatalogController@searchResult')->name('catalog.search.words');
Route::get('/search/catalog', 'Larrock\ComponentCatalog\CatalogController@searchItem')->name('catalog.search');
Route::post('/ajax/editPerPage', 'Larrock\ComponentCatalog\CatalogController@editPerPage')->name('catalog.editPerPage');
Route::post('/ajax/sort', 'Larrock\ComponentCatalog\CatalogController@sort')->name('catalog.sort');
Route::post('/ajax/vid', 'Larrock\ComponentCatalog\CatalogController@vid')->name('catalog.vid');

Route::group(['prefix' => 'admin'], function(){
    Route::resource('catalog', 'Larrock\ComponentCatalog\AdminCatalogController', ['names' => [
        'index' => 'admin.catalog.index',
        'show' => 'admin.catalog.show',
        'edit' => 'admin.catalog.edit',
    ]]);
    Route::post('catalog/copy', 'Larrock\ComponentCatalog\AdminCatalogController@copy')->name('catalog.admin.copy');
    Route::post('/ajax/getTovar', 'Larrock\ComponentCatalog\AdminCatalogController@getTovar')->name('catalog.admin.getTovar');
});

Breadcrumbs::register('admin.'. LarrockCatalog::getName() .'.index', function($breadcrumbs){
    $breadcrumbs->push(LarrockCatalog::getTitle(), route('admin.catalog.index'));
});

Breadcrumbs::register('admin.catalog.category', function($breadcrumbs, $data){
    $breadcrumbs->parent('admin.catalog.index');
    foreach($data->parent_tree as $item){
        $active = ' [Не опубликован!]';
        if($item->active === 1){
            $active = '';
        }
        $breadcrumbs->push($item->title . $active, '/admin/'. LarrockCatalog::getName() .'/'. $item->id);
    }
});

Breadcrumbs::register('catalog.index', function($breadcrumbs){
    $breadcrumbs->push('Каталог', '/catalog');
});

Breadcrumbs::register('catalog.item', function($breadcrumbs, $data){
    foreach ($data->get_category as $category){
        foreach ($category->parent_tree as $key => $item){
            if(in_array($item->url, \Route::current()->parameters())){
                $breadcrumbs->push($item->title, $item->full_url);
            }
        }
    }
    $breadcrumbs->push($data->title);
});

Breadcrumbs::register('catalog.search', function($breadcrumbs, $words){
    $breadcrumbs->push('Поиск "'. $words .'"');
});

Breadcrumbs::register('catalog.category', function($breadcrumbs, $data){
    foreach ($data->parent_tree as $key => $item){
        $breadcrumbs->push($item->title, $item->full_url);
    }
});