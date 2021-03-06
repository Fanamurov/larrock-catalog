<?php

namespace Larrock\ComponentCatalog\Helpers;

use Cache;
use Larrock\Core\Helpers\FormBuilder\FormSelect;
use Larrock\Core\Helpers\FormBuilder\FormSelectKey;
use Larrock\Core\Helpers\FormBuilder\FormTags;
use LarrockCatalog;
use Illuminate\Http\Request;
use Larrock\Core\Models\Link;
use Illuminate\Support\Collection;

class Filters
{
    /**
     * Формирование запроса на получение товаров каталога с примененными фильтрами.
     * @param Request $request
     * @param null|array    $category_array
     * @return mixed
     */
    public function getTovarsByFilters(Request $request, $category_array = null)
    {
        $data_query = LarrockCatalog::getModel()::whereActive(1);
        if ($category_array) {
            $data_query->whereHas('getCategory', function ($q) use ($category_array) {
                $q->whereIn('category.id', $category_array);
            });
        }

        if (\count($request->all()) !== 0) {
            $data_query = $data_query->whereHas('getAllLinks', function ($q) use ($request) {
                foreach (LarrockCatalog::getRows() as $row_key => $row_value) {
                    if ($row_value->filtered && $request->has($row_key) && \is_array($request->get($row_key))) {
                        if ($row_value instanceof FormTags) {
                            $model_param = new $row_value->modelChild;
                            $params = $model_param->whereIn('title', $request->get($row_key))->get();
                            $params_array = [];
                            foreach ($params as $param) {
                                $params_array[] = $param['id'];
                            }
                            $q->whereIn('link.id_child', $params_array)->whereModelChild($row_value->modelChild);
                        } else {
                            $q->whereIn($row_key, $request->get($row_key));
                        }
                    }
                }
            });
        }

        return $data_query;
    }

    /**
     * Получение доступных/выбранных фильтров для товаров.
     * @param Collection $data
     * @return array
     */
    public function getFilters($data)
    {
        $select_category = last(\Route::current()->parameters());
        $cache_key = sha1('filtersCategory'.$select_category);
        if ($cached = Cache::get($cache_key)) {
            //return $cached;
        }

        $filters = [];
        //Получаем доступные фильтры
        foreach (LarrockCatalog::getRows() as $row_key => $row_value) {
            if ($row_value->filtered && $row_key !== 'category' && (\is_string($data->first()->{$row_key}) || \is_int($data->first()->{$row_key}))) {
                $filters[$row_key] = $data->groupBy($row_key)->keys();
            }

            if ($row_value->filtered) {
                if ($row_value instanceof FormTags) {
                    $links = collect();
                    foreach ($data as $item) {
                        $links->push(Link::whereIdParent($item->id)->whereModelParent(LarrockCatalog::getModelName())->whereModelChild($row_value->modelChild)->get());
                    }
                    $filters[$row_key] = [];
                    /** @var Collection $links */
                    $links = $links->collapse()->groupBy('id_parent');
                    foreach ($links as $link) {
                        foreach ($link as $link_item) {
                            if ($link_item->getFullDataChild()) {
                                $filters[$row_key][] = $link_item->getFullDataChild()->title;
                            }
                        }
                    }
                }

                if ($row_value instanceof FormSelect || $row_value instanceof FormSelectKey) {
                    foreach ($data as $item) {
                        $filters[$row_key][] = $item->{$row_key};
                    }
                }
            }
        }

        foreach ($filters as $key => $filter) {
            $filters[$key] = collect($filter)->unique();
            foreach ($filters[$key] as $item_key => $filter_item) {
                if (empty($filter_item)) {
                    unset($filters[$key][$item_key]);
                }
            }
            $filters[$key] = $filters[$key]->sort();
        }

        if (\count($filters) > 0) {
            if (\count(\Request::all()) === 0) {
                //Cache::forever($cache_key, $filters);
            }

            return $filters;
        }

        return null;
    }

    /**
     * Получение полного списка всех опций фильтров без отсечений
     * @param string $select_category Текущий url раздела каталога
     * @return array
     * @throws \Exception
     */
    public function getAllFilters($select_category)
    {
        //Получаем неактивные фильтры
        $category_array = GetCategoriesArray::getCategoriesArray($select_category);
        $data = Cache::rememberForever('getCategoryCatalog'.$select_category, function () use ($select_category) {
            return LarrockCategory::getModel()->whereComponent('catalog')->whereActive(1)->whereUrl($select_category)
                ->with(['getChildActive.getChildActive'])->first();
        });

        $data = $this->getTovarsByFilters(new Request(), $category_array)->get();

        $filters = [];
        //Получаем доступные фильтры
        foreach (LarrockCatalog::getRows() as $row_key => $row_value) {
            if ($row_value->filtered && $row_key !== 'category' && (\is_string($data->first()->{$row_key}) || \is_int($data->first()->{$row_key}))) {
                $filters[$row_key] = $data->groupBy($row_key)->keys();
            }

            if ($row_value->filtered) {
                if ($row_value instanceof FormTags) {
                    $links = collect();
                    foreach ($data as $item) {
                        $links->push(Link::whereIdParent($item->id)->whereModelParent(LarrockCatalog::getModelName())->whereModelChild($row_value->modelChild)->get());
                    }
                    $filters[$row_key] = [];
                    /** @var Collection $links */
                    $links = $links->collapse()->groupBy('id_parent');
                    foreach ($links as $link) {
                        foreach ($link as $link_item) {
                            if ($link_item->getFullDataChild()) {
                                $filters[$row_key][] = $link_item->getFullDataChild()->title;
                            }
                        }
                    }
                }

                if ($row_value instanceof FormSelect || $row_value instanceof FormSelectKey) {
                    foreach ($data as $item) {
                        $filters[$row_key][] = $item->{$row_key};
                    }
                }
            }
        }

        foreach ($filters as $key => $filter) {
            $filters[$key] = collect($filter)->unique();
            foreach ($filters[$key] as $item_key => $filter_item) {
                if (empty($filter_item)) {
                    unset($filters[$key][$item_key]);
                }
            }
            $filters[$key] = $filters[$key]->sort();
        }

        return $filters;
    }
}
