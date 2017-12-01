<?php

namespace Larrock\ComponentCatalog;

use Illuminate\Support\Facades\Cookie;
use Larrock\ComponentCategory\Facades\LarrockCategory;
use Larrock\ComponentDiscount\Helpers\DiscountHelper;
use App\Http\Controllers\Controller;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Larrock\Core\Helpers\Tree;
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
            $data['data'] = LarrockCategory::getModel()->whereLevel(1)->whereActive(1)->whereComponent('catalog')->orderBy('position', 'DESC')->orderBy('created_at', 'ASC')->get();
            return $data;
        });

        if(count($data['data']) === 0){
            return abort(404, 'Catalog categories not found');
        }

        return view('larrock::front.catalog.categories', $data);
    }


    /**
     * Вывод списка дочерних разделов
     *
     * @param Request $request
     * @param $category
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCategory(Request $request, $category)
    {
        $select_category = last(\Route::current()->parameters());

        if(LarrockCatalog::getModel()->whereUrl($select_category)->first()){
            //Это товар, а не раздел
            return $this->getItem($request, $select_category);
        }

        $data['data'] = Cache::remember('getCategoryOnce'. $select_category.'_'. $request->cookie('perPage', 48), 1440,
            function() use ($select_category, $request){
            $data['data'] = LarrockCategory::getModel()->whereActive(1)->whereUrl($select_category)->with(['get_child'])->firstOrFail();
            $data['data']->get_tovarsActive = $data['data']->get_tovarsActive()
                ->paginate($request->cookie('perPage', config('larrock.catalog.DefaultItemsOnPage', 36)));
            return $data;
        });

        foreach ($data['data']->parent_tree as $category){
            if($category->active !== 1){
                return abort('404', 'Раздел не опубликован');
            }
        }

        if(count($data['data']->get_child)> 0){
            return view()->first(['larrock::front.catalog.categories.'. $category, 'larrock::front.catalog.categories'], ['data' => $get_category->get_child]);
        }

        if(count($data['data']->get_tovarsActive) > 0){
            if($request->cookie('vid', config('larrock.catalog.categoriesView'), 'blocks') === 'table'){
                if(view()->exists('larrock.catalog.templates.categoriesTable.'. $category)){
                    $view = 'larrock.catalog.templates.categoriesTable.'. $category;
                }else{
                    $view = config('larrock.catalog.templates.categoriesTable', 'larrock::front.catalog.items-table');
                }
            }else{
                if(view()->exists('larrock.catalog.templates.categoriesBlocks.'. $category)){
                    $view = 'larrock.catalog.templates.categoriesTable.'. $category;
                }else{
                    $view = config('larrock.catalog.templates.categoriesBlocks', 'larrock::front.catalog.items-4-3');
                }
            }

            return view($view, [
                'data' => $data['data'],
                'module_listCatalog' => $this->listCatalog($select_category),
                'sort' => $this->addSort(),
                'filter' => $this->addFilters($request, [$data['data']->id], $data['data']->get_tovarsActive())
            ]);
        }

        return abort(404);
    }


    /**
     * Вывод на страницу товаров из подразделов (при условии что товары в дочерних разделах). Либо вывод списка товаров
     *
     * @param Request $request
     * @param $category
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCategoryExpanded(Request $request, $category)
    {
        $select_category = last(\Route::current()->parameters());

        if(LarrockCatalog::getModel()->whereUrl($select_category)->first()){
            //Это товар, а не раздел
            return $this->getItem($request, $select_category);
        }

        $data['data'] = Cache::remember('getCategoryEx'. $select_category, 1440, function() use ($select_category){
            return LarrockCategory::getModel()->whereComponent('catalog')->whereActive(1)->whereUrl($select_category)
                ->with(['get_childActive.get_childActive'])->firstOrFail();
        });

        foreach ($data['data']->parent_tree as $category){
            if($category->active !== 1){
                return abort('404', 'Раздел не опубликован');
            }
        }

        $category_array = Cache::remember('categoryArray'. $select_category, 1440, function() use ($data){
            $category_array = collect([]);
            foreach($data['data']->get_childActive as $value){
                $category_array->push($value->id);
                foreach($value->get_childActive as $child_active){
                    $category_array->push($child_active->id);
                }
            }
            if(count($category_array) < 1){
                $category_array = [$data['data']->id];
            }
            return $category_array;
        });

        $data['data']->get_tovarsActive = LarrockCatalog::getModel()::whereActive(1)->whereHas('get_category', function ($q) use ($category_array){
            $q->whereIn('category.id', $category_array);
        });

        //Добавляем выборки по фильтрам
        foreach(LarrockCatalog::getRows() as $config_key => $config_value){
            if($config_value->filtered && $request->has($config_key)){
                if($request->has($config_key)){
                    $data['data']->get_tovarsActive->whereIn($config_key, $request->get($config_key));
                }
                //Помещаем на вывод методы с доступными для дальнейшего выбора фильтры
                $nameMethod = $config_key .'Allow';
                $data['data']->{$nameMethod} = $data['data']->get_tovarsActive->select($config_key)->get();
            }
        }

        $sort_cost = $request->cookie('sort_cost');
        if($sort_cost && $sort_cost !== 'none'){
            $data['data']->get_tovarsActive->orderBy('cost', $sort_cost);
        }

        $data['data']->get_tovarsActive = $data['data']->get_tovarsActive->select('catalog.*')
            ->paginate($request->cookie('perPage', config('larrock.catalog.DefaultItemsOnPage', 36)));

        if(count($data['data']->get_tovarsActive) === 0){
            abort(404, 'Товаров в разделе не найдено');
        }

        if(count($category_array) > 0){
            $data['data']->get_tovarsActive->setPath($data['data']->full_url);
        }

        if(file_exists(base_path(). '/vendor/fanamurov/larrock-discounts')){
            $discountHelper = new DiscountHelper();
            foreach ($data['data']->get_tovarsActive as $key => $item){
                $data['data']->get_tovarsActive->{$key} = $discountHelper->apply_discountsByTovar($item);
            }
        }

        if($request->cookie('vid', config('larrock.catalog.categoriesView'), 'blocks') === 'table'){
            if(view()->exists('larrock.catalog.templates.categoriesTable.'. $category)){
                $view = 'larrock.catalog.templates.categoriesTable.'. $category;
            }else{
                $view = config('larrock.catalog.templates.categoriesTable', 'larrock::front.catalog.items-table');
            }
        }else{
            if(view()->exists('larrock.catalog.templates.categoriesBlocks.'. $category)){
                $view = 'larrock.catalog.templates.categoriesTable.'. $category;
            }else{
                $view = config('larrock.catalog.templates.categoriesBlocks', 'larrock::front.catalog.items-4-3');
            }
        }

        return view($view, [
            'data' => $data['data'],
            'module_listCatalog' => $this->listCatalog($select_category),
            'sort' => $this->addSort(),
            'filter' => $this->addFilters($request, $category_array, $data['data']->get_tovarsActive)
        ]);
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
     * TODO: Переписать
     * Добавление фильтров для товаров каталога
     * @param Request $request
     * @param array $categories           Массив с разделами для поиска
     * @param array $data                 Массив с товарами
     * @return array
     */
    protected function addFilters(Request $request, $categories, $data)
    {
        $filters = [];
        foreach(LarrockCatalog::getRows() as $key => $value){
            if($value->filtered){
                //Фильтры из другой таблицы
                /*$table = $value['options_connect']['table'];
                $link_table = $value['options_connect']['table'].'_link';
                $link_name = $value['options_connect']['link_colomn_name'];

                //Собираем id разделов каталога для фильтров
                $category_list[] = $data['data']->id;
                foreach($data['data']->get_childActive as $child_value){
                    $category_list[] = $child_value->id;
                }

                //Фильтры из другой таблицы
                $data['filter'][$key]['values'] = \DB::table('catalog')
                    ->join($link_table, 'catalog.id', '=', 'catalog_id')
                    ->join($table, $table. '.id', '=', $link_table. '.'. $link_name)
                    ->join('category_catalog', 'category_catalog.catalog_id', '=', 'catalog.id')
                    ->whereIn('category_catalog.category_id', array_flatten($category_list))
                    ->groupBy([$table .'.title'])
                    ->get([$table .'.title']);
                $data['filter'][$key]['name'] = $value['title'];

                $methodName = $key .'Allow';
                if(isset($data['data']->$methodName)){
                    foreach($data['filter'][$key]['values'] as $key_value => $value_value){
                        foreach($data['data']->$methodName as $allow_value){
                            if($allow_value->title === $value_value->title){
                                $data['filter'][$key]['values'][$key_value]->allow = true;
                            }
                        }
                    }
                }
                if(count($data['filter'][$key]['values']) === 1){
                    $data['filter'][$key]['values'][0]->checked = true;
                    $data['filter'][$key]['values'][0]->allow = NULL;
                }*/

                $filters[$key]['values'] = LarrockCatalog::getModel()->whereActive(1)->whereHas('get_category', function ($q) use ($categories, $request){
                    $q->whereIn('category.id', $categories);
                })->groupBy($key)->get([$key]);
                $filters[$key]['name'] = $value->title;

                $methodName = $key .'Allow';
                if(isset($data->{$methodName})){
                    foreach($filters[$key]['values'] as $key_value => $value_value){
                        foreach($data->{$methodName} as $allow_value){
                            if($allow_value->{$key} === $value_value->{$key}){
                                $filters[$key]['values'][$key_value]->allow = true;
                            }
                        }
                        if($request->has($key) && count($request->except(['sort', 'vid'])) === 1){
                            $filters[$key]['values'][$key_value]->allow = true;
                        }
                    }
                }
                if(count($filters[$key]['values']) === 1){
                    $filters[$key]['values'][0]->checked = true;
                    $filters[$key]['values'][0]->allow = NULL;
                }
            }
        }
        return $filters;
    }


    /**
     * Вывод страницы товара
     *
     * @param $item
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getItem(Request $request, $item)
    {
        if(config('larrock.catalog.ShowItemPage', true) !== true){
            return abort(404, 'Страница товара отключена');
        }
        if(file_exists(base_path(). '/vendor/fanamurov/larrock-discounts')){
            $discountHelper = new DiscountHelper();
            $data['data'] = LarrockCatalog::getModel()->whereActive(1)->whereUrl($item)->with(['get_seo', 'get_category', 'getImages', 'getFiles'])->firstOrFail();
            $data['data'] = $discountHelper->apply_discountsByTovar($data['data']);
        }else{
            $data['data'] = LarrockCatalog::getModel()->whereActive(1)->whereUrl($item)->with(['get_seo', 'get_category', 'getImages', 'getFiles'])->firstOrFail();
        }

        foreach ($data['data']->get_category as $item_category){
            foreach ($item_category->parent_tree as $category){
                if($category->active !== 1){
                    return abort('404', 'Раздел не опубликован');
                }
            }
        }

        //Модуль списка разделов справа
        $data['module_listCatalog'] = $this->listCatalog($data['data']->get_category->first()->url);

        if($data['data']->get_seo){
            $data['seo']['title'] = $data['data']->get_seo->title;
        }else{
            $data['seo']['title'] = $data['data']->title;
        }

        return view()->first(['larrock::front.catalog.item.'. $item, 'larrock::front.catalog.item'], $data);
    }

    /**
     * Отдельная страница вывода результатов нечеткого поиска по каталогу
     *
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
        $getActiveCategory = LarrockCategory::getModel()->whereActive(1)->whereComponent('catalog')->whereParent(NULL)->with(['get_childActive.get_childActive.get_childActive'])->get();
        $tree = new Tree();
        $activeCategory = $tree->listActiveCategories($getActiveCategory);

        $data['data'] = LarrockCatalog::getModel()->search($words)->whereHas('get_category', function($q) use($activeCategory){
            $q->whereIn(LarrockCategory::getConfig()->table .'.id', $activeCategory);
        })->whereActive(1)->paginate($paginate);
        $data['words'] = $words;

        return view('larrock::front.catalog.items-search-result', $data);
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
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
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
            if(file_exists(base_path(). '/vendor/fanamurov/larrock-discount')){
                $discountHelper = new DiscountHelper();
                $get_tovar = $discountHelper->apply_discountsByTovar($get_tovar);
            }
            if($request->get('in_template', 'true') === 'true'){
                return view('larrock::front.modals.addToCart', ['data' => $get_tovar, 'app' => new CatalogComponent()]);
            }
            return response()->json($get_tovar);
        }
        return response('Товар не найден', 404);
    }

    /**
     * Данные для модуля выбора разделов каталога
     *
     * @param $category_url
     * @return mixed
     */
    public function listCatalog($category_url)
    {
        $data = Cache::remember('list_catalog'. $category_url, 1440, function() use ($category_url) {
            if($data['current'] = LarrockCategory::getModel()->whereUrl($category_url)->whereActive(1)->first()){
                $data['parent'] = LarrockCategory::getModel()->whereId($data['current']->parent)->whereActive(1)->first();
                $data['current_level'] = LarrockCategory::getModel()->whereParent($data['current']->parent)->whereActive(1)->get();
                $data['next_level'] = LarrockCategory::getModel()->whereParent($data['current']->id)->whereActive(1)->get();

                $data['parent_level'] = [];
                if($get_category = LarrockCategory::getModel()->whereId($data['current']->parent)->whereActive(1)->first()){
                    $data['parent_level'] = LarrockCategory::getModel()->whereParent($get_category->parent)->whereActive(1)->get();
                }
            }
            return $data;
        });
        return $data;
    }

    /**
     * Генерация YML-карты каталога
     * 
     * @return Response|CatalogController
     */
    public function YML()
    {
        $activeCategory = Cache::remember('listActiveCategoriesYML', 1440, function(){
            $getActiveCategory = LarrockCategory::getModel()->whereActive(1)->whereParent(NULL)
                ->whereComponent('catalog')->with(['get_childActive.get_childActive.get_childActive'])->get();
            $tree = new Tree();
            return $tree->listActiveCategories($getActiveCategory);
        });

        $data = Cache::remember('YMLcatalog', 1440, function() use ($activeCategory){
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