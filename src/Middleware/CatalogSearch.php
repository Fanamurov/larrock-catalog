<?php

namespace Larrock\ComponentCatalog\Middleware;

use Cache;
use Closure;
use Larrock\ComponentCatalog\Facades\LarrockCatalog;

class CatalogSearch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data = Cache::remember('catalogSearch', 1440, function(){
            $data = [];
            foreach (LarrockCatalog::getModel()->whereActive(1)->with(['get_category'])->get(['id', 'title']) as $item){
                $data[$item->id]['id'] = $item->id;
                $data[$item->id]['title'] = $item->title;
                $data[$item->id]['category'] = $item->get_category->first()->title;
            }
            return $data;
        });
        \View::share('catalogSearch', $data);
        return $next($request);
    }
}
