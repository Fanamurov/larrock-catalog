# Laravel Larrock CMS :: Catalog Component

---

#### Depends
- fanamurov/larrock-core
- fanamurov/larrock-category

## INSTALL
1. Install larrock-catalog
  ```sh
  composer require fanamurov/larrock-catalog
  ```

2. Add the ServiceProvider to the providers array in app/config/app.php
  ```
  //LARROCK COMPONENT CATALOG DEPENDS
  \Larrock\ComponentCatalog\LarrockComponentCatalogServiceProvider::class
  \Larrock\ComponentCategory\LarrockComponentCategoryServiceProvider::class //IF NEED
  ```

3. Publish views, migrations etc.
  ```sh
  $ php artisan vendor:publish
  ```
  Or
  ```sh
  $ php artisan vendor:publish --provider="Larrock\ComponentCatalog\LarrockComponentCatalogServiceProvider"
  $ php artisan vendor:publish --provider="Larrock\ComponentCategory\LarrockComponentCategoryServiceProvider::class" //IF NEED
  ```
       
4. Run artisan command:
  ```sh
  $ php artisan larrock:check
  ```
  And follow the tips for setting third-party dependencies
  
  
5. Run migrations
  ```sh
  $ php artisan migrate
  ```

##START
http://yousite/admin/catalog

##CONFIG
Create /config/larrock.php
Change Model or ComponentConfig:
```php
return [
    'components' => [
        'catalog' => App\Components\CatalogComponent::class
    ],

    'models' => [
        'catalog' => \App\Models\Catalog::class
    ]
];
```

Create App\Components\CatalogComponent and extends \Larrock\ComponentCatalog\CatalogComponent

Create App\Models\Catalog and extends ComponentCatalog\Models\Catalog