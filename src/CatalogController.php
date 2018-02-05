<?php

namespace Larrock\ComponentCatalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use Larrock\ComponentCatalog\Helpers\Filters;
use Larrock\ComponentCatalog\Helpers\ListCatalog;
use Larrock\ComponentCatalog\Helpers\Sorters;
use Larrock\ComponentCatalog\Models\Catalog;
use Larrock\ComponentCatalog\Models\Param;
use Larrock\ComponentCategory\Facades\LarrockCategory;
use Illuminate\Routing\Controller;
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
        $filters = new Filters();
        $data->get_tovarsActive = $filters->getTovarsByFilters($request, $category_array);
        \View::share('filters', $filters->getFilters($data->get_tovarsActive->get()));

        $sorters = new Sorters();
        $data->get_tovarsActive = $sorters->applySorts($data->get_tovarsActive, $request);
        \View::share('sort', $sorters->getSorts());

        $listCatalog = new ListCatalog();
        \View::share('module_listCatalog', $listCatalog->listCatalog($select_category));

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