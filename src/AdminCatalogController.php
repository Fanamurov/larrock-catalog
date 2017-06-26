<?php

namespace Larrock\ComponentCatalog;

use Breadcrumbs;
use Cache;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use JsValidator;
use Alert;
use Lang;
use Larrock\ComponentCart\Models\Cart;
use Larrock\ComponentCatalog\Models\Catalog;
use Larrock\ComponentCategory\CategoryComponent;
use Larrock\ComponentCategory\Models\Category;
use Larrock\Core\Component;
use Validator;
use Redirect;
use View;

class AdminCatalogController extends Controller
{
	protected $config;

	public function __construct()
	{
        $Component = new CatalogComponent();
        $this->config = $Component->shareConfig();

        Breadcrumbs::setView('larrock::admin.breadcrumb.breadcrumb');
		Breadcrumbs::register('admin.'. $this->config->name .'.index', function($breadcrumbs){
			$breadcrumbs->push($this->config->title, route('admin.catalog.index'));
		});
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return View
	 */
	public function index()
	{
		$data['categories'] = Category::whereComponent('catalog')->whereLevel(1)->orderBy('position', 'DESC')->with(['get_child', 'get_parent'])->paginate(30);
		$data['nalichie'] = Catalog::where('nalichie', '<', 1)->get();

		return view('larrock::admin.catalog.index', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request                     $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
     */
	public function create(Request $request)
	{
		$create_data = Request::create('/admin/catalog', 'POST', [
			'title' => 'Новый материал',
			'url' => str_slug('novyy-material'),
			'what' => 'руб./шт.',
			'category' => [$request->get('category')],
			'active' => 0,
			'razmer_h' => 0,
			'razmer_w' => 0,
			'nalichie' => 9999
		]);
		Cache::flush();
		return $this->store($create_data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store(Request $request)
	{
		if($search_blank = Catalog::whereUrl('novyy-material')->first()){
			return redirect()->to('/admin/catalog/'. $search_blank->id. '/edit');
		}

        $validator = Validator::make($request->all(), $this->config->valid);
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

		$data = new Catalog();
		$data->fill($request->all());
        foreach ($this->config->rows as $row){
            if(get_class($row) === 'Larrock\Core\Helpers\FormBuilder\FormCheckbox'){
                $data->{$row->name} = $request->input($row->name, NULL);
            }
            if(get_class($row) === 'Larrock\Core\Helpers\FormBuilder\FormDate'){
                $data->{$row->name} = $request->input('date', date('Y-m-d'));
            }
        }
		$data->user_id = \Auth::getUser()->id;
        //$data->articul = 'AR'. $request->input('id', Catalog::max('id'));

		if($data->save()){
            $Component = new CatalogComponent();
            $Component->actionAttach($this->config, $data, $request);

            /* Нужно для копирования элемента. Смотреть метод copy() */
			if($request->has('images')){
				foreach($request->input('images') as $image){
					\Image::make(public_path() .'/media/Catalog/'. $image)
						->save(public_path() .'/image_cache/'. $data->id .'-'. $image);
					$data->addMedia(public_path() .'/image_cache/'. $data->id .'-'. $image)->preservingOriginal()->toMediaLibrary('images');
				}
			}
            Alert::add('successAdmin', Lang::get('apps.create.success-temp'))->flash();
            return Redirect::to('/admin/'. $this->config->name .'/'. $data->id .'/edit')->withInput();
		}
        Alert::add('errorAdmin', Lang::get('apps.create.error'));
        return back()->withInput();
	}

	/**
	 * Display the list resource of category.
	 *
	 * @param  int    $id
	 *
	 * @return View
	 */
	public function show($id)
	{
        $data['app_category'] = new CategoryComponent();
        $data['category'] = Category::whereId($id)->with(['get_child', 'get_parent'])->firstOrFail();
        $data['data'] = Catalog::whereHas('get_category', function ($q) use ($id){
            $q->where('category.id', '=', $id);
        })->orderByDesc('position')->orderByDesc('updated_at')->paginate('50');


		Breadcrumbs::register('admin.catalog.category', function($breadcrumbs, $data)
		{
            $breadcrumbs->parent('admin.catalog.index');
            foreach($data->parent_tree as $item){
                $breadcrumbs->push($item->title, '/admin/'. $this->config->name .'/'. $item->id);
            }
		});

		return view('larrock::admin.admin-builder.categories', $data);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 *
	 * @return View
	 */
	public function edit($id)
	{
        $data['data'] = Catalog::with(['get_category', 'getFiles', 'getImages'])->findOrFail($id);
        $data['app'] = $this->config->tabbable($data['data']);

        $validator = JsValidator::make(Component::_valid_construct($this->config, 'update', $id));
        View::share('validator', $validator);

		Breadcrumbs::register('admin.catalog.edit', function($breadcrumbs, $data)
		{
			$breadcrumbs->parent('admin.'. $this->config->name .'.index');
            foreach($data->get_category[0]->parent_tree as $item){
                $breadcrumbs->push($item->title, '/admin/'. $this->config->name .'/'. $item->id);
            }

			$breadcrumbs->push($data->title, route('admin.catalog.show', $data->id));
		});

		return view('larrock::admin.admin-builder.edit', $data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
	public function update(Request $request, $id)
	{
        $validator = Validator::make($request->all(), Component::_valid_construct($this->config, 'update', $id));
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

		$data = Catalog::find($id);
		$data->fill($request->all());
        foreach ($this->config->rows as $row){
            if(get_class($row) === 'Larrock\Core\Helpers\FormBuilder\FormCheckbox'){
                $data->{$row->name} = $request->input($row->name, NULL);
            }
            if(get_class($row) === 'Larrock\Core\Helpers\FormBuilder\FormDate'){
                $data->{$row->name} = $request->input('date', date('Y-m-d'));
            }
        }
		if($request->get('cost_old') === ''){
			$data->cost_old = NULL;
		}

		if($data->url === 'novyy-material'){
			$data->url = str_slug($data->title);
		}

		if($data->save()){
            $Component = new CatalogComponent();
            $Component->actionAttach($this->config, $data, $request);

            Alert::add('successAdmin', Lang::get('apps.update.success', ['name' => $request->input('title')]))->flash();
			\Cache::flush();
			return back();
		}
        Alert::add('warning', Lang::get('apps.update.nothing', ['name' => $request->input('title')]))->flash();
		return back()->withInput();
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
	public function destroy(Request $request, $id)
	{
		if($data = Catalog::find($id)){
            $name = $data->title;
            $data->clearMediaCollection();
            if($data->delete()){
                $Component = new CatalogComponent();
                $Component->actionAttach($this->config, $data, $request);

                Alert::add('successAdmin', Lang::get('apps.delete.success', ['name' => $name]))->flash();
                \Cache::flush();
            }else{
                Alert::add('errorAdmin', Lang::get('apps.delete.error', ['name' => $name]))->flash();
            }
        }else{
            Alert::add('errorAdmin', 'Такого материала больше нет')->flash();
        }

        if($request->get('place') === 'material'){
            return Redirect::to('/admin/'. $this->config->name);
        }
        return back();
	}

	/**
	 * Копирование товара
	 * @param                $id
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
	public function copy($id)
	{
		$search_id = Catalog::whereId($id)->with(['get_category', 'getImages', 'getFiles'])->first();
		$search_id->url = str_slug($search_id->title) .'-'. Catalog::max('id');
		$add_data = $search_id->toArray();

        foreach ($this->config->rows as $row){
            if($row->attached){
                $add_data[$row->name] = [];
                foreach($search_id->{$row->model_link}() as $value){
                    $add_data[$row->name][] = $value->id;
                }
            }
        }
		Cache::flush();

		return $this->store(Request::create('/admin/catalog', 'POST', $add_data));
	}

    public function getTovar(Request $request)
    {
        $Component = new CatalogComponent();
        $Component->shareConfig();
        if($get_tovar = Catalog::whereId($request->get('id'))->with(['get_category'])->first()){
            if($request->get('in_template') === 'true'){
                $order = Cart::whereOrderId($request->get('order_id'))->first();
                return view('admin.cart.getItem-modal', ['order' => $order, 'data' => $get_tovar]);
            }
            return response()->json($get_tovar);
        }
        return response('Товар не найден', 404);
    }
}
