<?php

namespace Larrock\ComponentCatalog\Middleware;

use Cache;
use Closure;
use LarrockCategory;

class RandomCatalogItems
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $show_items = [];
        $get_categories = Cache::rememberForever('get_categoriesRandomCatalogItems', function(){
            if(config('larrock.catalog.RandomCatalogItems.categories')){
                return LarrockCategory::getModel()->whereActive(1)
                    ->whereIn(LarrockCategory::getTable(). '.id', config('larrock.catalog.RandomCatalogItems.categories'))
                    ->whereComponent('catalog')->get(['id']);
            }
            return LarrockCategory::getModel()->whereActive(1)
                ->whereLevel(config('larrock.catalog.RandomCatalogItems.level', 3))
                ->whereComponent('catalog')->get(['id']);
        });
        $select_categories = $get_categories->random(config('larrock.catalog.RandomCatalogItems.items', 3));
        foreach ($select_categories as $category){
            if($category->get_tovarsActive()->count() > 0){
                $show_items[] = $category->get_tovarsActive()->get()->random(1)->first();
            }
        }
        \View::share('RandomCatalogItems', $show_items);
        return $next($request);
    }
}