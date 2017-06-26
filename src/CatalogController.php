<?php

namespace Larrock\ComponentCatalog;

use Illuminate\Support\Facades\Cookie;
use Larrock\ComponentDiscount\Helpers\DiscountHelper;
use App\Http\Controllers\Controller;
use Larrock\ComponentCatalog\Helpers\HelperCatalog;
use Breadcrumbs;
use Cache;
use Illuminate\Http\Request;
use Larrock\ComponentCatalog\Models\Catalog;
use Larrock\ComponentCategory\Models\Category;
use Illuminate\Http\Response;
use Session;

class CatalogController extends Controller
{
	protected $config;

	public function __construct()
	{
        $Component = new CatalogComponent();
        $this->config = $Component->shareConfig();

		Breadcrumbs::register('catalog.index', function($breadcrumbs){
			$breadcrumbs->push('Каталог', '/catalog');
		});
	}

    /**
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function getCategoryRoot()
    {
        $data = Cache::remember('getTovars_root', 1440, function(){
            $data['data'] = Category::whereId(11)->whereActive(1)->with(['get_childActive'])->orderBy('position', 'DESC')->first();
            return $data;
        });

        return view('larrock::front.catalog.root', $data);
    }

    /**
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getMainCategory()
	{
	    $data['data'] = Cache::remember('getTovars_main', 1440, function(){
            return Category::whereComponent('catalog')->whereLevel(2)->whereActive(1)->with(['get_parent'])->orderBy('position', 'DESC')->get();
	    });

		/*$seofish = Cache::remember('seofish_mod', 1440, function() {
		    return Feed::whereCategory(2)->whereActive(1)->orderBy('position', 'DESC')->get();
		});
		\View::share('seofish', $seofish);

		$data = Cache::remember('getTovars_main_add_seo', 1440, function() use ($seofish, $data){
			if(isset($seofish->first()->title)){
				$data['seo']['title'] = $seofish->first()->title;
			}else{
				$data['seo']['title'] = 'Каталог';
			}
			return $data;
		});*/

		return view('larrock::front.catalog.categorys', $data);
	}

	/**
	 * Вывод на страницу товаров с подразделов
	 * @param Request     $request
	 * @param             $category
	 * @param null        $child
	 * @param null        $grandson
	 *
	 * @return mixed
	 */
	public function getCategoryExpanded(Request $request, $category, $child = NULL, $grandson = NULL)
	{
	    $HelperCatalog = new HelperCatalog();
		//Cache::flush();
		$paginate = $request->cookie('perPage', 24);
		$sort_cost = $request->cookie('sort_cost');

		//Смотрим какой раздел выбираем для работы
		//Первый уровень: /Раздел
		$select_category = $category;
		if($child){
			//Вложенный раздел: /Раздел/Подраздел
			$select_category = $child;
			if($grandson){
				//Вложенный раздел: /Раздел/Подраздел/Подраздел
				$select_category = $grandson;
			}
		}

		//Модуль списка разделов справа
		$data['module_listCatalog'] = $HelperCatalog->listCatalog($select_category);

        if(Catalog::whereUrl($select_category)->first()){
            //Это товар, а не раздел
            return $this->getItem($select_category, $data['module_listCatalog']);
        }

        $category_array = collect([]);
        $output = Category::whereComponent('catalog')->whereActive(1)->whereUrl($select_category)->with(['get_childActive'])->first();
        foreach($output->get_childActive as $value){
            $category_array->push($value->id);
            foreach($value->get_childActive as $child_active){
                $category_array->push($child_active->id);
            }
        }

        if(count($category_array) < 1){
            $category_array = [$output->id];
        }

		$cache_key = sha1('getCategoryExp'. $select_category .'_'. $request->get('page', 1) .'_'. $sort_cost .'_'. $paginate);
		Cache::forget($cache_key);
		$data['data'] = Cache::remember($cache_key, 1440, function() use ($select_category, $paginate, $sort_cost, $category_array, $output, $request) {
            $output = Category::whereComponent('catalog')->whereActive(1)->whereUrl($select_category)->with(['get_childActive.get_childActive'])->first();
            if( !$output){
                return FALSE;
            }

            $output->get_tovarsActive = Catalog::whereActive(1)->whereHas('get_category', function ($q) use ($category_array){
                $q->whereIn('category.id', $category_array);
            });

			//Ловим фильтры
			foreach($this->config->rows as $config_key => $config_value){
				if($config_value->filtered && $request->has($config_key)){
                    $output->get_tovarsActive->whereIn($config_key, $request->get($config_key));
				}
			}

			foreach($this->config->rows as $config_key => $config_value){
				if($config_value->filtered){
					//Помещаем на вывод методы с доступными для дальнейшего выбора фильтры
					$nameMethod = $config_key .'Allow';
					$output->{$nameMethod} = $output->get_tovarsActive->select($config_key)->get();
				}
			}

			if($sort_cost !== 'none'){
				$output->get_tovarsActive->orderBy('cost', $sort_cost);
			}

			$output->get_tovarsActive = $output->get_tovarsActive->select('catalog.*')->groupBy('catalog.id')->paginate($paginate);

			//TODO: Переписать взаимодействие с фильтрами
			if(count($category_array) > 0){
				$output->get_tovarsActive->setPath($output->full_url);
			}

			return $output;
		});

		if(count($data['data']->get_tovarsActive) === 0){
			abort(404, 'Страница не найдена');
		}

		if( !$data['data']){
			//Раздела с таким url нет, значит ищем товар
			return $this->getItem($select_category, $data['module_listCatalog']);
		}

        if($data['data']->level === 3 && !$grandson){
            return abort(404, 'Раздел каталога не найден');
        }
        if($data['data']->level === 2 && !$child){
            return abort(404, 'Раздел каталога не найден');
        }

		//Сканим возможные фильтры
        Cache::forget('filters'. $select_category);
		$data['filter'] = Cache::remember('filters'. $select_category, 1440, function() use ($data, $category_array, $request) {
			$data['filter'] = [];
			foreach($this->config->rows as $key => $value){
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

                    $data['filter'][$key]['values'] = Catalog::whereActive(1)->whereHas('get_category', function ($q) use ($category_array, $request){
                        $q->whereIn('category.id', $category_array);
                    })->groupBy($key)->get([$key]);
                    $data['filter'][$key]['name'] = $value['title'];

                    $methodName = $key .'Allow';
                    if(isset($data['data']->{$methodName})){
                        foreach($data['filter'][$key]['values'] as $key_value => $value_value){
                            foreach($data['data']->{$methodName} as $allow_value){
                                if($allow_value->{$key} === $value_value->{$key}){
                                    $data['filter'][$key]['values'][$key_value]->allow = true;
                                }
                            }
                            if($request->has($key) && count($request->except(['sort', 'vid'])) === 1){
                                $data['filter'][$key]['values'][$key_value]->allow = true;
                            }
                        }
                    }
                    if(count($data['filter'][$key]['values']) === 1){
                        $data['filter'][$key]['values'][0]->checked = true;
                        $data['filter'][$key]['values'][0]->allow = NULL;
                    }
				}
			}
			return $data['filter'];
		});

        $data['sort'] = [];
        foreach($this->config->rows as $key => $value) {
            if ($value->sorted) {
                $data['sort'][$key]['name'] = trim($value->title);
                $data['sort'][$key]['values'] = ['1<span class="divider">→</span>9', 'Без сортировки', '9<span class="divider">→</span>1'];
                $data['sort'][$key]['data'] = ['asc', 'none', 'desc'];
            }
        }

		Breadcrumbs::register('catalog.category', function($breadcrumbs, $data)
		{
		    //TODO: Переписать на parent_tree
			//$breadcrumbs->parent('catalog.index');
			if($data->level !== 1 &&
				$get_parent = Category::whereComponent('catalog')->whereId($data->parent)->first()){
				if($get_parent->level !== 1
					&& $get_granddad = Category::whereType('catalog')->whereId($get_parent->parent)->first()){
					$breadcrumbs->push($get_granddad->title, '/');
				}
				$breadcrumbs->push($get_parent->title, $get_parent->full_url);
			}
			$breadcrumbs->push($data->title);
		});

        if(file_exists(base_path(). '/vendor/fanamurov/larrock-discounts')){
            $discountHelper = new DiscountHelper();
            foreach ($data['data']->get_tovarsActive as $key => $item){
                $data['data']->get_tovarsActive->{$key} = $discountHelper->apply_discountsByTovar($item);
            }
        }

		if($request->cookie('vid') === 'table'){
			return view('larrock::front.catalog.items-table', $data);
		}
        return view('larrock::front.catalog.items-4-3', $data);
	}

    /**
     * @param $item
     * @param $module_listCatalog
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function getItem($item, $module_listCatalog)
	{
        if(file_exists(base_path(). '/vendor/fanamurov/larrock-discounts')){
            $discountHelper = new DiscountHelper();
            $data['data'] = Catalog::whereUrl($item)->with(['get_seo', 'get_category', 'getImages', 'getFiles'])->firstOrFail();
            $data['data'] = $discountHelper->apply_discountsByTovar($data['data']);
        }else{
            $data['data'] = Catalog::whereUrl($item)->with(['get_seo', 'get_category', 'getImages', 'getFiles'])->firstOrFail();
        }

        //Модуль с товарами из этого же раздела
        /*$key = sha1('modincat_'. $data['data']->id .'_'. $item);
        $data['module_in_category'] = Cache::remember($key, 1440, function() use ($data, $discountHelper) {
            $data = Category::whereId($data['data']->get_category->first()->id)
                ->with(['get_tovarsActive' => function($query) use ($data, $discountHelper){$query->orderByRaw('RAND()')->where('id', '!=', $data['data']->id)->take(6);}])->first();
            foreach ($data->get_tovarsActive as $key => $item_mod){
                $data->get_tovarsActive[$key] = $discountHelper->apply_discountsByTovar($item_mod);
            }
            return $data;
        });*/

		Breadcrumbs::register('catalog.item', function($breadcrumbs, $data)
		{
			$breadcrumbs->parent('catalog.index');
			$get_category = $data->get_category->first();
			if($get_category->level !== 1){
				$parent = $get_category->get_parent;
				if($parent->level !== 1){
					$grandpa = $parent->get_parent;
					$breadcrumbs->push($grandpa->title, $parent->full_url);
				}else{
					$breadcrumbs->push($parent->title, $parent->full_url);
				}
			}
			$breadcrumbs->push($get_category->title, $get_category->full_url);
			$breadcrumbs->push($data->title);
		});

		//Модуль списка разделов справа
		$data['module_listCatalog'] = $module_listCatalog;

		if($data['data']->get_seo){
			$data['seo']['title'] = $data['data']->get_seo->title;
		}else{
			$data['seo']['title'] = $data['data']->title;
		}

		return view('larrock::front.catalog.item', $data);
	}

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function searchItem(Request $request)
	{
		$query = $request->get('q');
		if( !$query && $query === ''){
			return \Response::json(array(), 400);
		}

		$search = Catalog::search($query)->with(['get_category'])->whereActive(1)->groupBy('title')->get()->toArray();
		return \Response::json($search);
	}

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function searchCategory(Request $request)
	{
		$query = $request->get('q');
		if( !$query && $query === ''){
			return \Response::json(array(), 400);
		}

		$search = Catalog::search($query)->whereActive(1)->get()->toArray();
		return \Response::json($search);
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
			\Alert::add('danger', 'Вы не указали искомое слово');
		}
		$paginate = Cookie::get('perPage', 24);

		$data['data'] = Catalog::search($words)->with(['get_category'])->whereActive(1)->paginate($paginate);
		$data['words'] = $words;

		Breadcrumbs::register('catalog.search', function($breadcrumbs) use ($words){
			$breadcrumbs->push('Поиск по каталогу');
			$breadcrumbs->push('Поиск по слову "'. $words .'"');
		});

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
        $response->withCookie(cookie('vid', $request->get('q', 'cards'), 45000));
        Session::flash('vid', $request->get('q', 'cards'));
        return $response;
    }

    /**
     * Ajax
     * @param Request $request
     * @param DiscountHelper $discountHelper
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function getTovar(Request $request, DiscountHelper $discountHelper)
    {
        if($get_tovar = Catalog::whereId($request->get('id', 33))->with(['get_category'])->first()){
            $get_tovar = $discountHelper->apply_discountsByTovar($get_tovar);
            if($request->get('in_template', 'true') === 'true'){
                return view('larrock::front.modals.addToCart', ['data' => $get_tovar, 'app' => new CatalogComponent()]);
            }
            return response()->json($get_tovar);
        }
        return response('Товар не найден', 404);
    }
}
