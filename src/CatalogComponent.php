<?php

namespace Larrock\ComponentCatalog;

use Larrock\ComponentCatalog\Models\Catalog;
use Larrock\ComponentCatalog\Models\Param;
use Larrock\ComponentCategory\Models\Category;
use Larrock\Core\Helpers\FormBuilder\FormCategory;
use Larrock\Core\Helpers\FormBuilder\FormInput;
use Larrock\Core\Helpers\FormBuilder\FormSelect;
use Larrock\Core\Helpers\FormBuilder\FormTagsCreate;
use Larrock\Core\Helpers\FormBuilder\FormTextarea;
use Larrock\Core\Component;
use Larrock\Core\Models\Config;

class CatalogComponent extends Component
{
    public function __construct()
    {
        $this->name = $this->table = 'catalog';
        $this->title = 'Каталог';
        $this->description = 'Каталог товаров';
        $this->model = Catalog::class;
        $this->addRows()->addPositionAndActive()->isSearchable()->addPlugins();
    }

    protected function addPlugins()
    {
        $this->addPluginImages()->addPluginFiles()->addPluginSeo();
        return $this;
    }

    protected function addRows()
    {
        $row = new FormCategory('category', 'Раздел');
        $this->rows['category'] = $row->setValid('required')
            ->setConnect(Category::class, 'get_category')
            ->setWhereConnect('component', 'catalog')
            ->setAttached();

        $row = new FormInput('title', 'Название товара');
        $this->rows['title'] = $row->setValid('max:255|required')->setTypo();

        $row = new FormTextarea('short', 'Короткое описание');
        $this->rows['short'] = $row->setTypo();

        $row = new FormTextarea('description', 'Полное описание');
        $this->rows['description'] = $row->setTypo();

        $row = new FormInput('cost', 'Цена');
        $this->rows['cost'] = $row->setValid('max:15')->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setInTableAdminAjaxEditable();

        $row = new FormInput('cost_old', 'Старая цена');
        $this->rows['cost_old'] = $row->setValid('max:15')->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4');

        $row = new FormSelect('what', 'Мера измерений');
        $this->rows['what'] = $row->setValid('max:15|required')
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')
            ->setConnect(Catalog::class);

        $row = new FormInput('manufacture', 'Производитель');
        $this->rows['manufacture'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4');

        $row = new FormInput('articul', 'Артикул');
        $this->rows['articul'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setTemplate('in_card');

        /*$row = new FormCheckbox('label_new', 'Метка нового');
        $this->rows['label_new'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4');

        $row = new FormCheckbox('label_popular', 'Метка популярное');
        $this->rows['label_popular'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4');

        $row = new FormInput('label_sale', 'Метка скидка (%)');
        $this->rows['label_sale'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4');*/

        $row = new FormTagsCreate('param', 'Параметры товара');
        $this->rows['param'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')
            //->setConnect('option_vid', 'print_vid')
            //->setModelLink('get_vid', 'vid_id')
            ->setConnect(Param::class, 'get_param')
            ->setAttached()->setUserSelect();

        return $this;
    }

    /**
     * Объединение конфига компонента с конфигом каталога из Wizard
     * @return $this
     */
    public function mergeWizardConfig()
    {
        if($data = Config::whereType('wizard')->whereName('catalog')->first()){
            foreach ($data->value as $wizard_key => $wizard_item){
                if(isset($this->rows[$wizard_item['db']])){
                    if($wizard_item['slug'] && !empty($wizard_item['slug'])){
                        $this->rows[$wizard_item['db']]->title = $wizard_item['slug'];
                    }
                    if($wizard_item['filters']){
                        if($wizard_item['filters'] === 'lilu'){
                            $this->rows[$wizard_item['db']]->filtered = TRUE;
                        }
                        if($wizard_item['filters'] === 'sort'){
                            $this->rows[$wizard_item['db']]->sorted = TRUE;
                        }
                    }
                    if($wizard_item['template']){
                        $this->rows[$wizard_item['db']]->template = $wizard_item['template'];
                    }
                }else{
                    //Добавляем поля созданные в визарде
                    if($wizard_item['db']){
                        if( empty($wizard_item['slug'])){
                            $wizard_item['slug'] = $wizard_key;
                        }
                        $row = new FormInput($wizard_key, $wizard_item['slug']);
                        if($wizard_item['filters']){
                            if($wizard_item['filters'] === 'lilu'){
                                $row->filtered = TRUE;
                            }
                            if($wizard_item['filters'] === 'sort'){
                                $row->sorted = TRUE;
                            }
                        }
                        if($wizard_item['template']){
                            $row->setTemplate($wizard_item['template']);
                        }
                        $this->rows[$wizard_key] = $row;
                    }
                }
            }
        }
        return $this;
    }

    public function renderAdminMenu()
    {
        $count = \Cache::remember('count-data-admin-'. $this->name, 1440, function(){
            return Catalog::count(['id']);
        });
        $dropdown = Category::whereComponent('catalog')->whereLevel(0)->orderBy('position', 'desc')->get(['id', 'title', 'url']);
        $push = collect();
        if(in_array('Larrock\ComponentWizard\WizardComponent', get_declared_classes())){
            $push->put('Wizard - импорт товаров', '/admin/wizard');
        }
        if(in_array('Larrock\ComponentDiscount\DiscountComponent', get_declared_classes())){
            $push->put('Скидки', '/admin/discount');
        }
        return view('larrock::admin.sectionmenu.types.dropdown', ['count' => $count, 'app' => $this, 'url' => '/admin/'. $this->name, 'dropdown' => $dropdown, 'push' => $push]);
    }

    public function createSitemap()
    {
        return Catalog::whereActive(1)->whereHas('get_category', function ($q){
            $q->where('sitemap', '=', 1);
        })->get();
    }
}