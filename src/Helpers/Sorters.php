<?php

namespace Larrock\ComponentCatalog\Helpers;

use Larrock\ComponentCatalog\Facades\LarrockCatalog;
use Illuminate\Http\Request;

class Sorters
{
    /**
     * Добавление сортировок вверх/вниз
     * @return array
     */
    public function getSorts()
    {
        $sort = [];
        foreach(LarrockCatalog::getRows() as $key => $value) {
            if ($value->sorted) {
                $sort[$key]['name'] = trim($value->title);
                $sort[$key]['values'] = ['1<span class="divider">→</span>9', 'Без сортировки', '9<span class="divider">→</span>1'];
                $sort[$key]['data'] = ['asc', 'none', 'desc'];
            }
        }
        if(count($sort) > 0){
            return $sort;
        }
    }

    public function applySorts($model, Request $request)
    {
        $sort_cost = $request->cookie('sort_cost');
        if($sort_cost && $sort_cost !== 'none'){
            $model->orderBy('cost', $sort_cost);
        }
        return $model;
    }
}