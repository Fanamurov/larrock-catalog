<?php

namespace Larrock\ComponentCatalog\Middleware;

use Cache;
use Closure;
use LarrockCatalog;

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
        $data = Cache::rememberForever('catalogSearchModule', function () {
            $data = [];
            foreach (LarrockCatalog::getModel()->whereActive(1)->with(['getCategory'])->get() as $item) {
                $data[$item->id]['id'] = $item->id;
                $data[$item->id]['title'] = $item->title;
                $data[$item->id]['category'] = $item->getCategory->first()->title;
            }

            return $data;
        });
        \View::share('catalogSearch', $data);

        return $next($request);
    }
}
