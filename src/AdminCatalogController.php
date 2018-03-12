<?php

namespace Larrock\ComponentCatalog;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Larrock\Core\Traits\AdminMethods;
use Larrock\Core\Traits\AdminMethodsShow;
use LarrockCatalog;
use LarrockCart;

class AdminCatalogController extends Controller
{
    use AdminMethodsShow, AdminMethods;

	public function __construct()
	{
	    $this->shareMethods();
        $this->middleware(LarrockCatalog::combineAdminMiddlewares());
        $this->config = LarrockCatalog::shareConfig();
        \Config::set('breadcrumbs.view', 'larrock::admin.breadcrumb.breadcrumb');
	}

    public function getTovar(Request $request)
    {
        if($get_tovar = LarrockCatalog::getModel()->whereId($request->get('id'))->with(['getCategory'])->first()){
            if($request->get('in_template') === 'true'){
                $order = LarrockCart::getModel()->whereOrderId($request->get('order_id'))->first();
                return view('larrock::admin.cart.getItem-modal', ['order' => $order, 'data' => $get_tovar]);
            }
            return response()->json($get_tovar);
        }
        return response('Товар не найден', 404);
    }
}