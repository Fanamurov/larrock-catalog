# Laravel Larrock CMS :: Catalog Component

---

#### Depends
- fanamurov/larrock-core
- fanamurov/larrock-category

## INSTALL

1. Install larrock-core, larrock-category
2. Install larrock-catalog
  ```sh
  composer require fanamurov/larrock-catalog
  ```

4. Add the ServiceProvider to the providers array in app/config/app.php
  ```
  //LARROCK COMPONENT CATALOG DEPENDS
  \Larrock\ComponentCatalog\LarrockComponentCatalogServiceProvider::class
  ```

5. Publish views, migrations etc.
  ```sh
  $ php artisan vendor:publish
  ```
  Or
  ```sh
  $ php artisan vendor:publish --provider="Larrock\ComponentCatalog\LarrockComponentCatalogServiceProvider"
  ```
       
6. Run artisan command:
  ```sh
  $ php artisan larrock:check
  ```
  And follow the tips for setting third-party dependencies
  
  
7. Run migrations
  ```sh
  $ php artisan migrate
  ```

##START
http://yousite/admin/catalog