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
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function getCategoryRoot()
    {
        $data = Cache::rememberForever('getTovars_root', function(){
            return LarrockCategory::getModel()->whereLevel(1)->whereActive(1)->whereComponent('catalog')
                ->orderBy('position', 'DESC')->orderBy('created_at', 'ASC')->get();
        });

        if(\count($data) === 0){
            throw new \Exception('Catalog categories not found', 404);
        }

        return view(config('larrock.views.catalog.categories', 'larrock::front.catalog.categories'), ['data' => $data]);
    }


    /**
     * Получение конкретного товара/товаров раздела/товаров разделов/списка разделов
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function getCategory(Request $request)
    {
        $select_category = last(\Route::current()->parameters());

        //Проверка разделов из url на опубликованность
        foreach (\Route::current()->parameters() as $param){
            if( !$category = LarrockCategory::getModel()->whereUrl($param)->first()){
                //Может это товар?
                if(LarrockCatalog::getModel()->whereUrl($select_category)->first()){
                    return $this->getItem($select_category);
                }
                throw new \Exception('Раздел '. $param .' не существует', 404);
            }
            if($category->active !== 1){
                throw new \Exception('Раздел '. $category .' не опубликован', 404);
            }
        }

        $data = Cache::rememberForever('getCategoryCatalog'. $select_category, function() use ($select_category) {
            return LarrockCategory::getModel()->whereComponent('catalog')->whereActive(1)->whereUrl($select_category)
                ->with(['get_childActive.get_childActive'])->first();
        });
        if( !$data){
            throw new \Exception('Раздел с url:'. $select_category .' не найден', 404);
        }

        foreach ($data->parent_tree as $category){
            if($category->active !== 1){
                throw new \Exception('Товаров в разделе не найдено', 404);
            }
        }

        if(config('larrock.catalog.categoryExpanded', TRUE) === TRUE) {
            $cache_key = sha1('categoryArrayExp'. $select_category);
            $category_array = Cache::rememberForever($cache_key, function() use ($data){
                $category_array = collect([]);
                foreach($data->get_childActive as $value){
                    if(config('larrock.catalog.categoryExpanded', TRUE) === TRUE) {
                        $category_array->push($value->id);
                        foreach ($value->get_childActive as $child_active) {
                            $category_array->push($child_active->id);
                        }
                    }
                }
                if(\count($category_array) < 1){
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

        if(\count($data->get_tovarsActive) === 0){
            if(config('larrock.catalog.categoryExpanded', TRUE) === TRUE) {
                throw new \Exception('Товаров в разделе не найдено', 404);
            }

            if( !$view = config('larrock.views.catalog.categoryUniq.'. $select_category)){
                $view = config('larrock.views.catalog.categories', 'larrock::front.catalog.categories');
            }
            return view($view, ['data' => $data]);
        }

        if(\count($category_array) > 0){
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
     * @throws \Exception
     */
    public function getItem($item)
    {
        //Проверка разделов из url на опубликованность
        foreach (\Route::current()->parameters() as $param){
            if(last(\Route::current()->parameters()) !== $param){
                if( !$category = LarrockCategory::getModel()->whereUrl($param)->first()){
                    throw new \Exception('Раздел '. $param .' не существует', 404);
                }
                if($category->active !== 1){
                    throw new \Exception('Раздел '. $category .' не опубликован', 404);
                }
            }
        }

        if(config('larrock.catalog.ShowItemPage', true) !== true){
            throw new \Exception('Страница товара отключена', 404);
        }
        $data = LarrockCatalog::getModel()->whereActive(1)->whereUrl($item)->with(['get_seo', 'get_category', 'getImages', 'getFiles'])->first();
        if( !$data){
            throw new \Exception('Товар с url:'. $item .' не найден', 404);
        }

        foreach ($data->get_category as $item_category){
            foreach ($item_category->parent_tree as $category){
                if($category->active !== 1 && in_array($category->url, \Route::current()->parameters())){
                    throw new \Exception('Раздел '. $category->title .' не опубликован', 404);
                }
            }
        }

        //Модуль списка разделов справа
        $listCatalog = new ListCatalog();
        $select_category = $data->get_category->first()->url;
        \View::share('module_listCatalog', $listCatalog->listCatalog($select_category));

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
            $q->whereIn(LarrockCategory::getTable() .'.id', $activeCategory);
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
     * Генерация YML-карты каталога
     * @return Response|CatalogController
     */
    public function YML()
    {
        $data = Cache::rememberForever('YMLcatalog', function(){
            $getActiveCategory = LarrockCategory::getModel()->whereActive(1)->whereParent(NULL)
                ->whereComponent('catalog')->with(['get_childActive.get_childActive.get_childActive'])->get();
            $tree = new Tree();
            $activeCategory = $tree->listActiveCategories($getActiveCategory);

            return LarrockCatalog::getModel()->whereActive(1)->whereHas('get_category', function($q) use($activeCategory){
                $q->whereIn(LarrockCategory::getTable() .'.id', $activeCategory);
            })->get();
        });

        $categories = Cache::rememberForever('YMLcategory', function(){
            return LarrockCategory::getModel()->whereActive(1)->whereComponent('catalog')->get();
        });

        return \Response::view('larrock::front.yml', ['data' => $data, 'categories' => $categories])->header('Content-Type', 'application/xml');
    }
}