#### Depends
- fanamurov/larrock-core
- fanamurov/larrock-category

## INSTALL
1. Install larrock-catalog
  ```sh
  composer require fanamurov/larrock-catalog
  ```

2. Publish views, migrations etc.
  ```sh
  $ php artisan vendor:publish
  ```
  Or
  ```sh
  $ php artisan vendor:publish --provider="Larrock\ComponentCatalog\LarrockComponentCatalogServiceProvider"
  $ php artisan vendor:publish --provider="Larrock\ComponentCategory\LarrockComponentCategoryServiceProvider::class" //IF NEED
  ```
  
3. Run migrations
  ```sh
  $ php artisan migrate
  ```

## START
http://yousite/admin/catalog

## CONFIG
Create or change /config/**larrock.php**
```php
<?php
return [
  'catalog' => [
      'templates' => [
          'categoriesTable' => 'larrock::front.catalog.items-table',
          'categoriesBlocks' => 'larrock::front.catalog.items-4-3',
      ],
  
      'categoriesView' => 'blocks' //Вид каталога по-умолчанию (blocks или table) 
    'DefaultItemsOnPage' => 36, //Кол-во товаров на странице раздела по-умолчанию
  
    'ShowItemPage' => true, //Если true - показывать ссылки на страницу товара
  
      'modules' => [
          'sortCost' => TRUE, //Показывать модуль сортировки
          'lilu' => TRUE, //Показывать модуль фильтров товаров
          'vid' => TRUE, //Показывать модуль выбора шаблона
          'itemsOnPage' => TRUE, //Показывать модуль кол-ва товаров на страницу
      ]
  ]
];
```