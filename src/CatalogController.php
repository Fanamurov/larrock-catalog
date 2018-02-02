<?php

namespace Larrock\ComponentCatalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use Larrock\ComponentCatalog\Models\Catalog;
use Larrock\ComponentCategory\Facades\LarrockCategory;
use App\Http\Controllers\Controller;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Larrock\Core\Helpers\Tree;
use Larrock\Core\Models\Link;
use Session;
use Larrock\ComponentCatalog\Facades\LarrockCatalog;

class CatalogController extends Controller
{
    public function __construct()
    {
        LarrockCatalog::shareConfig();
        $this->middleware(LarrockCatalog::combineFrontMiddlewares());
    }

    /**
     * Вывод списка корневых разделов
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCategoryRoot()
    {
        $data = Cache::remember('getTovars_root', 1440, function(){
            $data['data'] = LarrockCategory::getModel()->whereLevel(1)->whereActive(1)->whereComponent('catalog')
                ->orderBy('position', 'DESC')->orderBy('created_at', 'ASC')->get();
            return $data;
        });

        if(count($data['data']) === 0){
            throw new \Exception('Catalog categories not found', 404);
        }

        return view(config('larrock.views.catalog.categories', 'larrock::front.catalog.categories'), $data);
    }


    /**
     * Получение конкретного товара/товаров раздела/товаров разделов/списка разделов
     * @param Request $request
     * @param $category
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCategory(Request $request, $category)
    {
        $select_category = last(\Route::current()->parameters());

        if(LarrockCatalog::getModel()->whereUrl($select_category)->first()){
            //Это товар, а не раздел
            return $this->getItem($select_category);
        }

        $data = Cache::rememberForever('getCategoryCatalog'. $select_category, function() use ($select_category) {
            return LarrockCategory::getModel()->whereComponent('catalog')->whereActive(1)->whereUrl($select_category)
                ->with(['get_childActive.get_childActive'])->firstOrFail();
        });

        foreach ($data->parent_tree as $category){
            if($category->active !== 1){
                throw new \Exception('Товаров в разделе не найдено', 404);
            }
        }

        if(config('larrock.catalog.categoryExpanded', TRUE) === TRUE) {
            $cache_key = sha1('categoryArrayExp'. $select_category);
            $category_array = Cache::remember($cache_key, 1440, function() use ($data){
                $category_array = collect([]);
                foreach($data->get_childActive as $value){
                    if(config('larrock.catalog.categoryExpanded', TRUE) === TRUE) {
                        $category_array->push($value->id);
                        foreach ($value->get_childActive as $child_active) {
                            $category_array->push($child_active->id);
                        }
                    }
                }
                if(count($category_array) < 1){
                    $category_array = [$data->id];
                }
                return $category_array;
            });
        }else{
            $category_array = collect([$data->id]);
        }

        //Получаем товары в выборке разделов
        $data->get_tovarsActive = LarrockCatalog::getModel()::whereActive(1)->whereHas('get_category', function ($q) use ($category_array){
            $q->whereIn('category.id', $category_array);
        });

        $filters = $this->getFilters($request, $data->get_tovarsActive->get());
        \View::share('filters', $filters);
        \View::share('sort', $this->addSort());
        \View::share('module_listCatalog', $this->listCatalog($select_category));

        $sort_cost = $request->cookie('sort_cost');
        if($sort_cost && $sort_cost !== 'none'){
            $data->get_tovarsActive->orderBy('cost', $sort_cost);
        }

        $data->get_tovarsActive = $this->applyFilters($request, $data->get_tovarsActive, $filters);

        $data->get_tovarsActive = $data->get_tovarsActive->select('catalog.*')
            ->paginate($request->cookie('perPage', config('larrock.catalog.DefaultItemsOnPage', 36)));

        if(count($data->get_tovarsActive) === 0){
            if(config('larrock.catalog.categoryExpanded', TRUE) === TRUE) {
                throw new \Exception('Товаров в разделе не найдено', 404);
            }

            if( !$view = config('larrock.views.catalog.categoryUniq.'. $select_category)){
                $view = config('larrock.views.catalog.categories', 'larrock::front.catalog.categories');
            }
            return view($view, ['data' => $data]);
        }

        if(count($category_array) > 0){
            $data->get_tovarsActive->setPath($data->full_url);
        }

        if( !$view = config('larrock.views.catalog.categoryUniq.'. $select_category)){
            if($request->cookie('vid', config('larrock.catalog.categoriesView'), 'blocks') === 'table'){
                $view = config('larrock.views.catalog.categoriesTable', 'larrock::front.catalog.items-table');
            }else{
                $view = config('larrock.views.catalog.categoriesBlocks', 'larrock::front.catalog.items-4-3');
            }
        }

        return view($view, ['data' => $data]);
    }


    /**
     * Создание блока фильтров для товаров каталога
     * @param Request $request
     * @param $data Models\Catalog коллекция товаров
     */
    protected function getFilters(Request $request, $data)
    {
        if($data && count($data) > 0){
            $filters = [];
            //Сначала формируем запрос на получение товаров по всем фильтрам
            foreach(LarrockCatalog::getRows() as $row_key => $row_value){
                if($row_value->filtered && (is_string($data->first()->{$row_key}) || is_integer($data->first()->{$row_key}))){
                    if($request->has($row_key) && is_array($request->get($row_key))){
                        $data = $data->whereIn($row_key, $request->get($row_key));
                    }
                }

                //Параметры через Link
                if($row_value->filtered && $row_value->attached){

                }
            }
            //Получаем доступные фильтры
            foreach(LarrockCatalog::getRows() as $row_key => $row_value){
                if($row_value->filtered && (is_string($data->first()->{$row_key}) || is_integer($data->first()->{$row_key}))){
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
            }

            if(count($filters) > 0){
                return $filters;
            }
        }
        return $data;
    }

    /**
     * Применение значений фильтров товаров для запроса
     * @param Request $request
     * @param Catalog $data
     * @param $filters
     * @return mixed
     */
    protected function applyFilters(Request $request, $data, $filters)
    {
        foreach ($filters as $key => $filter){
            if($request->has($key) && is_array($request->get($key))){
                if(LarrockCatalog::getRows()[$key]->attached){
                    //$data = $data->whereIn($key, $request->get($key));
                    /*$data = $data->whereHas('getLink', function ($q){
                        $q->whereIn('category.id', $category_array);
                    });*/
                    $model_param = LarrockCatalog::getRows()[$key]->modelChild;
                    $model_param = new $model_param;
                    $table = $model_param->getTable();

                    //$data = $data->join($table, 'id', '=', $table.'.title');
                    //dd($data->toSql());
                }else{
                    $data = $data->whereIn($key, $request->get($key));
                }
            }
        }
        return $data;
    }

    /**
     * Добавление сортировок вверх/вниз
     * @return array
     */
    protected function addSort()
    {
        $sort = [];
        foreach(LarrockCatalog::getRows() as $key => $value) {
            if ($value->sorted) {
                $sort[$key]['name'] = trim($value->title);
                $sort[$key]['values'] = ['1<span class="divider">→</span>9', 'Без сортировки', '9<span class="divider">→</span>1'];
                $sort[$key]['data'] = ['asc', 'none', 'desc'];
            }
        }
        return $sort;
    }


    /**
     * Вывод страницы товара
     * @param $item
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getItem($item)
    {
        if(config('larrock.catalog.ShowItemPage', true) !== true){
            throw new \Exception('Страница товара отключена', 404);
        }
        $data = LarrockCatalog::getModel()->whereActive(1)->whereUrl($item)->with(['get_seo', 'get_category', 'getImages', 'getFiles'])->firstOrFail();

        foreach ($data->get_category as $item_category){
            foreach ($item_category->parent_tree as $category){
                if($category->active !== 1){
                    throw new \Exception('Раздел '. $category->title .' не опубликован', 404);
                }
            }
        }

        //Модуль списка разделов справа
        \View::share('module_listCatalog', $this->listCatalog($data->get_category->first()->url));

        return view()->first([config('larrock.views.catalog.itemUniq.'. $item, 'larrock::front.catalog.item.'. $item),
            config('larrock.views.catalog.item', 'larrock::front.catalog.item')], ['data' => $data]);
    }

    /**
     * Отдельная страница вывода результатов нечеткого поиска по каталогу
     * @param Request $request
     * @param string $words
     * @return mixed
     */
    public function searchResult(Request $request, $words = '')
    {
        $words = $request->get('query', $words);
        if( empty($words)){
            Session::push('message.danger', 'Вы не указали искомое слово');
        }
        $paginate = Cookie::get('perPage', 48);

        //Ищем опубликованные разделы и их опубликованных потомков
        $getActiveCategory = LarrockCategory::getModel()->whereActive(1)->whereComponent('catalog')->whereParent(NULL)
            ->with(['get_childActive.get_childActive.get_childActive'])->get();
        $tree = new Tree();
        $activeCategory = $tree->listActiveCategories($getActiveCategory);

        $data['data'] = LarrockCatalog::getModel()->search($words)->whereHas('get_category', function($q) use($activeCategory){
            $q->whereIn(LarrockCategory::getConfig()->table .'.id', $activeCategory);
        })->whereActive(1)->paginate($paginate);
        $data['words'] = $words;

        return view(config('larrock.views.catalog.search', 'larrock::front.catalog.items-search-result'), $data);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editPerPage(Request $request)
    {
        $response = new Response('perPage');
        $response->withCookie(cookie('perPage', $request->get('q'), 45000));
        Session::flash('perPage', $request->get('p'));
        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function sort(Request $request)
    {
        $response = new Response('sort');
        $response->withCookie(cookie('sort_'. $request->get('type'), $request->get('q'), 45000));
        Session::flash('sort_'. $request->get('type'), $request->get('q'));
        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function vid(Request $request)
    {
        $response = new Response('vid');
        $response->withCookie(cookie('vid', $request->get('q', 'blocks'), 45000));
        Session::flash('vid', $request->get('q', 'blocks'));
        return $response;
    }

    /**
     * Ajax
     * @param Request $request
     * @return View|Response
     */
    public function getTovar(Request $request)
    {
        if($get_tovar = LarrockCatalog::getModel()->whereActive(1)->whereId($request->get('id'))->with(['get_category'])->first()){
            foreach ($get_tovar->get_category as $item_category){
                foreach ($item_category->parent_tree as $category){
                    if($category->active !== 1){
                        return response('Товар находится в неопубликованном разделе', 404);
                    }
                }
            }
            if($request->get('in_template', 'true') === 'true'){
                return view(config('larrock.views.catalog.modal', 'larrock::front.modals.addToCart'), ['data' => $get_tovar, 'app' => new CatalogComponent()]);
            }
            return response()->json($get_tovar);
        }
        return response('Товар не найден', 404);
    }

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

    /**
     * Генерация YML-карты каталога
     * @return Response|CatalogController
     */
    public function YML()
    {
        $data = Cache::remember('YMLcatalog', 1440, function() use ($activeCategory){
            $getActiveCategory = LarrockCategory::getModel()->whereActive(1)->whereParent(NULL)
                ->whereComponent('catalog')->with(['get_childActive.get_childActive.get_childActive'])->get();
            $tree = new Tree();
            $activeCategory = $tree->listActiveCategories($getActiveCategory);

            return LarrockCatalog::getModel()->whereActive(1)->whereHas('get_category', function($q) use($activeCategory){
                $q->whereIn(LarrockCategory::getConfig()->table .'.id', $activeCategory);
            })->get();
        });

        $categories = Cache::remember('YMLcategory', 1440, function(){
            return LarrockCategory::getModel()->whereActive(1)->whereComponent('catalog')->get();
        });

        return \Response::view('larrock::front.yml', ['data' => $data, 'categories' => $categories])->header('Content-Type', 'application/xml');
    }
}