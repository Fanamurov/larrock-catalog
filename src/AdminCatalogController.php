<?php

namespace Larrock\ComponentCatalog;

use Breadcrumbs;
use Cache;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use JsValidator;
use Alert;
use Lang;
use Larrock\Core\Component;
use Validator;
use Redirect;
use View;
use Larrock\ComponentCategory\Facades\LarrockCategory;
use Larrock\ComponentCatalog\Facades\LarrockCatalog;
use Larrock\ComponentCart\Facades\LarrockCart;

class AdminCatalogController extends Controller
{
	public function __construct()
	{
        $this->config = LarrockCatalog::shareConfig();

        \Config::set('breadcrumbs.view', 'larrock::admin.breadcrumb.breadcrumb');
		Breadcrumbs::register('admin.'. LarrockCatalog::getName() .'.index', function($breadcrumbs){
			$breadcrumbs->push(LarrockCatalog::getTitle(), route('admin.catalog.index'));
		});
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return View
	 */
	public function index()
	{
		$data['categories'] = LarrockCategory::getModel()->whereComponent('catalog')->whereLevel(1)->orderBy('position', 'DESC')->orderBy('updated_at', 'ASC')->with(['get_child', 'get_parent'])->paginate(30);
		$data['nalichie'] = LarrockCatalog::getModel()->where('nalichie', '<', 1)->get();

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
		if($search_blank = LarrockCatalog::getModel()->whereUrl('novyy-material')->first()){
			return redirect()->to('/admin/catalog/'. $search_blank->id. '/edit');
		}

        $validator = Validator::make($request->all(), LarrockCatalog::getValid());
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

        LarrockCatalog::getModel()->fill($request->all());
        foreach ($this->config->rows as $row){
            if(get_class($row) === 'Larrock\Core\Helpers\FormBuilder\FormCheckbox'){
                $data->{$row->name} = $request->input($row->name, NULL);
            }
            if(get_class($row) === 'Larrock\Core\Helpers\FormBuilder\FormDate'){
                $data->{$row->name} = $request->input('date', date('Y-m-d'));
            }
        }
		$data->user_id = \Auth::getUser()->id;
        //$data->articul = 'AR'. $request->input('id', $this->component->model::max('id'));

		if($data->save()){
            $this->component->actionAttach($this->config, $data, $request);

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
        $data['app_category'] = LarrockCategory::getConfig();
        $data['category'] = LarrockCategory::getModel()->whereId($id)->with(['get_child', 'get_parent'])->firstOrFail();
        $data['data'] = LarrockCatalog::getModel()->whereHas('get_category', function ($q) use ($id){
            $q->where('category.id', '=', $id);
        })->orderByDesc('position')->orderBy('updated_at', 'ASC')->paginate('50');


		Breadcrumbs::register('admin.catalog.category', function($breadcrumbs, $data)
		{
            $breadcrumbs->parent('admin.catalog.index');
            foreach($data->parent_tree as $item){
                $breadcrumbs->push($item->title, '/admin/'. LarrockCatalog::getName() .'/'. $item->id);
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
        $data['data'] = LarrockCatalog::getModel()->with(['get_category', 'getFiles', 'getImages'])->findOrFail($id);
        $data['app'] = LarrockCatalog::tabbable($data['data']);

        $validator = JsValidator::make(Component::_valid_construct(LarrockCatalog::getConfig(), 'update', $id));
        View::share('validator', $validator);

		Breadcrumbs::register('admin.catalog.edit', function($breadcrumbs, $data)
		{
			$breadcrumbs->parent('admin.'. LarrockCatalog::getName() .'.index');
            foreach($data->get_category[0]->parent_tree as $item){
                $breadcrumbs->push($item->title, '/admin/'. LarrockCatalog::getName() .'/'. $item->id);
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
        $validator = Validator::make($request->all(), Component::_valid_construct(LarrockCatalog::getConfig(), 'update', $id));
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

        $data = LarrockCatalog::getModel()->find($id);
		$data->fill($request->all());
        foreach (LarrockCatalog::getRows() as $row){
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
            LarrockCatalog::actionAttach(LarrockCatalog::getConfig(), $data, $request);

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
		if($data = LarrockCatalog::getModel()->find($id)){
            $name = $data->title;
            $data->clearMediaCollection();
            if($data->delete()){
                $this->component->actionAttach($this->config, $data, $request);

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
		$search_id = LarrockCatalog::getModel()->whereId($id)->with(['get_category', 'getImages', 'getFiles'])->first();
		$search_id->url = str_slug($search_id->title) .'-'. LarrockCatalog::getModel()->max('id');
		$add_data = $search_id->toArray();

        foreach (LarrockCatalog::getRows() as $row){
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
        if($get_tovar = LarrockCatalog::getModel()->whereId($request->get('id'))->with(['get_category'])->first()){
            if($request->get('in_template') === 'true'){
                $order = LarrockCart::getModel()->whereOrderId($request->get('order_id'))->first();
                return view('admin.cart.getItem-modal', ['order' => $order, 'data' => $get_tovar]);
            }
            return response()->json($get_tovar);
        }
        return response('Товар не найден', 404);
    }
}
