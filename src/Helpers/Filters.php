<?php

namespace Larrock\ComponentCatalog\Helpers;

use Illuminate\Http\Request;
use Larrock\ComponentCatalog\Facades\LarrockCatalog;
use Larrock\Core\Models\Link;
use Cache;

class Filters
{
    /**
     * Формирование запроса на получение товаров каталога с примененными фильтрами
     * @param Request $request
     * @param null|array    $category_array
     * @return mixed
     */
    public function getTovarsByFilters(Request $request, $category_array = NULL)
    {
        $data_query = LarrockCatalog::getModel()::whereActive(1);
        if($category_array){
            $data_query->whereHas('get_category', function ($q) use ($category_array){
                $q->whereIn('category.id', $category_array);
            });
        }

        if(count($request->all()) !== 0){
            $data_query = $data_query->whereHas('getAllLinks', function ($q) use ($request){
                foreach(LarrockCatalog::getRows() as $row_key => $row_value){
                    if($row_value->filtered && $request->has($row_key) && is_array($request->get($row_key))){
                        if($row_value->attached){
                            $model_param = new $row_value->modelChild;
                            $params = $model_param->whereIn('title', $request->get($row_key))->get();
                            $params_array = [];
                            foreach ($params as $param){
                                $params_array[] = $param['id'];
                            }
                            $q->whereIn('link.id_child', $params_array)->whereModelChild($row_value->modelChild);
                        }else{
                            $q->whereIn($row_key, $request->get($row_key));
                        }
                    }
                }
            });
        }

        return $data_query;
    }

    /**
     * Получение доступных/выбранных фильтров для товаров
     * @param $data
     * @return array
     */
    public function getFilters($data)
    {
        $select_category = last(\Route::current()->parameters());
        $cache_key = sha1('filtersCategory'. $select_category);
        if($cached = Cache::get($cache_key)){
            return $cached;
        }

        $filters = [];
        //Получаем доступные фильтры
        foreach(LarrockCatalog::getRows() as $row_key => $row_value){
            if($row_value->filtered && $row_key !== 'category' && (is_string($data->first()->{$row_key}) || is_integer($data->first()->{$row_key}))){
                $filters[$row_key] = $data->groupBy($row_key)->keys();
            }

            if($row_value->filtered && $row_value->attached){
                $links = collect();
                foreach ($data as $item){
                    $links->push(Link::whereIdParent($item->id)->whereModelParent(LarrockCatalog::getModelName())->whereModelChild($row_value->modelChild)->get());
                }
                $filters[$row_key] = [];
                $links = $links->collapse()->groupBy('id_parent');
                foreach ($links as $link){
                    foreach ($link as $link_item){
                        $filters[$row_key][] = $link_item->getFullDataChild()->title;
                    }
                }
            }
        }

        foreach ($filters as $key => $filter){
            $filters[$key] = collect($filter)->unique();
            foreach ($filters[$key] as $item_key => $filter_item){
                if(empty($filter_item)){
                    unset($filters[$key][$item_key]);
                }
            }
        }

        if(count($filters) > 0){
            if(count(\Request::all()) === 0){
                Cache::forever($cache_key, $filters);
            }
            return $filters;
        }
    }
}