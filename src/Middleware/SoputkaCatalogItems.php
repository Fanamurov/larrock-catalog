<?php

namespace Larrock\ComponentCatalog\Middleware;

use Cache;
use Closure;
use LarrockCatalog;

class SoputkaCatalogItems
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $item_url = last(\Route::current()->parameters());
        $cache_key = sha1('SoputkaCatalogItems'. $item_url);
        $tovars = Cache::rememberForever($cache_key, function () use ($item_url) {
            if($item = LarrockCatalog::getModel()->whereUrl($item_url)->first()){
                if($getLink = $item->link(LarrockCatalog::getModelName())){
                    return LarrockCatalog::getModel()->whereIn('id', $getLink->pluck('id_child'))->get();
                }
            }
            return NULL;
        });

        \View::share('SoputkaCatalogItems', $tovars);
        return $next($request);
    }
}