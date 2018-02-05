<?php

namespace Larrock\ComponentCatalog\Helpers;

use Cache;
use Larrock\ComponentCategory\Facades\LarrockCategory;

class ListCatalog
{
    /**
     * Данные для модуля выбора разделов каталога
     * @param $category_url
     * @return mixed
     */
    public function listCatalog($category_url)
    {
        $data = Cache::remember('list_catalog'. $category_url, 1440, function() use ($category_url) {
            if($data['current'] = LarrockCategory::getModel()->whereUrl($category_url)->whereComponent('catalog')->whereActive(1)->first()){
                $data['parent'] = LarrockCategory::getModel()->whereId($data['current']->parent)->whereComponent('catalog')->whereActive(1)->first();
                $data['current_level'] = LarrockCategory::getModel()->whereParent($data['current']->parent)->whereComponent('catalog')->whereActive(1)->get();
                $data['next_level'] = LarrockCategory::getModel()->whereParent($data['current']->id)->whereComponent('catalog')->whereActive(1)->get();

                $data['parent_level'] = [];
                if($get_category = LarrockCategory::getModel()->whereId($data['current']->parent)->whereComponent('catalog')->whereActive(1)->first()){
                    $data['parent_level'] = LarrockCategory::getModel()->whereParent($get_category->parent)->whereComponent('catalog')->whereActive(1)->get();
                }
            }
            return $data;
        });
        return $data;
    }
}