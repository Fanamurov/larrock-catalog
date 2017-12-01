<?php

namespace Larrock\ComponentCatalog;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Larrock\Core\Traits\AdminMethodsCreate;
use Larrock\Core\Traits\AdminMethodsDestroy;
use Larrock\Core\Traits\AdminMethodsEdit;
use Larrock\Core\Traits\AdminMethodsUpdate;
use View;
use Larrock\ComponentCategory\Facades\LarrockCategory;
use Larrock\ComponentCatalog\Facades\LarrockCatalog;
use Larrock\ComponentCart\Facades\LarrockCart;

class AdminCatalogController extends Controller
{
    use AdminMethodsEdit, AdminMethodsUpdate, AdminMethodsDestroy, AdminMethodsCreate;

    protected $config;

	public function __construct()
	{
        $this->middleware(LarrockCatalog::combineAdminMiddlewares());
        $this->config = LarrockCatalog::shareConfig();
        \Config::set('breadcrumbs.view', 'larrock::admin.breadcrumb.breadcrumb');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return View
	 */
	public function index()
	{
		$data['categories'] = LarrockCategory::getModel()->whereComponent('catalog')->whereLevel(1)
            ->orderBy('position', 'DESC')->orderBy('updated_at', 'ASC')->with(['get_child', 'get_parent'])->paginate(30);
		$data['nalichie'] = LarrockCatalog::getModel()->where('nalichie', '<', 1)->get();

		return view('larrock::admin.catalog.index', $data);
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

		return view('larrock::admin.admin-builder.categories', $data);
	}

    public function getTovar(Request $request)
    {
        if($get_tovar = LarrockCatalog::getModel()->whereId($request->get('id'))->with(['get_category'])->first()){
            if($request->get('in_template') === 'true'){
                $order = LarrockCart::getModel()->whereOrderId($request->get('order_id'))->first();
                return view('larrock::admin.cart.getItem-modal', ['order' => $order, 'data' => $get_tovar]);
            }
            return response()->json($get_tovar);
        }
        return response('Товар не найден', 404);
    }
}