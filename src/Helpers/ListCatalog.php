<?php

namespace Larrock\ComponentCatalog\Helpers;

use Cache;
use LarrockCategory;

class ListCatalog
{
    /**
     * Данные для модуля выбора разделов каталога.
     * @param $category_url
     * @return mixed
     */
    public function listCatalog($category_url)
    {
        $data = Cache::rememberForever('list_catalog'.$category_url, function () use ($category_url) {
            if ($data['current'] = LarrockCategory::getModel()->whereUrl($category_url)->whereComponent('catalog')->whereActive(1)->first()) {
                $data['parent'] = LarrockCategory::getModel()->whereId($data['current']->parent)->whereComponent('catalog')->whereActive(1)->first();
                $data['current_level'] = LarrockCategory::getModel()->whereParent($data['current']->parent)->whereComponent('catalog')->whereActive(1)->get();
                $data['next_level'] = LarrockCategory::getModel()->whereParent($data['current']->id)->whereComponent('catalog')->whereActive(1)->get();

                $data['parent_level'] = [];
                if ($getCategory = LarrockCategory::getModel()->whereId($data['current']->parent)->whereComponent('catalog')->whereActive(1)->first()) {
                    $data['parent_level'] = LarrockCategory::getModel()->whereParent($getCategory->parent)->whereComponent('catalog')->whereActive(1)->get();
                }
            }

            return $data;
        });

        return $data;
    }
}
