<?php

namespace Larrock\ComponentCatalog\Helpers;

use Cache;
use LarrockCategory;
use LarrockCatalog;

class GetCategoriesArray{
    /**
     * @param string $select_item url текущего раздела каталога
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|null
     * @throws \Exception
     */
    public static function getCategoriesArray($select_item)
    {
        //Проверка разделов из url на опубликованность
        foreach (\Route::current()->parameters() as $param) {
            if (! $category = LarrockCategory::getModel()->whereUrl($param)->first()) {
                //Может это товар?
                if (LarrockCatalog::getModel()->whereUrl($select_item)->first()) {
                    return null;
                }
                throw new \Exception('Раздел '.$param.' не существует', 404);
            }
            if ($category->active !== 1) {
                throw new \Exception('Раздел '.$category.' не опубликован', 404);
            }
        }

        $data = Cache::rememberForever('getCategoryCatalog'.$select_item, function () use ($select_item) {
            return LarrockCategory::getModel()->whereComponent('catalog')->whereActive(1)->whereUrl($select_item)
                ->with(['getChildActive.getChildActive'])->first();
        });
        if (! $data) {
            throw new \Exception('Раздел с url:'.$select_item.' не найден', 404);
        }

        foreach ($data->parent_tree as $category) {
            if ($category->active !== 1) {
                throw new \Exception('Товаров в разделе не найдено', 404);
            }
        }

        if (config('larrock.catalog.categoryExpanded', true) === true) {
            $cache_key = sha1('categoryArrayExp'.$select_item);
            $category_array = Cache::rememberForever($cache_key, function () use ($data) {
                $category_array = collect([]);
                foreach ($data->getChildActive as $value) {
                    if (config('larrock.catalog.categoryExpanded', true) === true) {
                        $category_array->push($value->id);
                        foreach ($value->getChildActive as $child_active) {
                            $category_array->push($child_active->id);
                        }
                    }
                }
                if (\count($category_array) < 1) {
                    $category_array = [$data->id];
                }

                return $category_array;
            });
        }

        return collect([$data->id]);
    }
}