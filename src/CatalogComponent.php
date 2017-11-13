<?php

namespace Larrock\ComponentCatalog;

use Larrock\ComponentCatalog\Facades\LarrockCatalog;
use Larrock\ComponentCatalog\Models\Catalog;
use Larrock\ComponentCatalog\Models\Param;
use Larrock\ComponentCategory\Facades\LarrockCategory;
use Larrock\ComponentCategory\Models\Category;
use Larrock\Core\Helpers\FormBuilder\FormCategory;
use Larrock\Core\Helpers\FormBuilder\FormHidden;
use Larrock\Core\Helpers\FormBuilder\FormInput;
use Larrock\Core\Helpers\FormBuilder\FormSelect;
use Larrock\Core\Helpers\FormBuilder\FormTagsCreate;
use Larrock\Core\Helpers\FormBuilder\FormTextarea;
use Larrock\Core\Component;
use Larrock\Core\Models\Config;
use Cache;

class CatalogComponent extends Component
{
    public function __construct()
    {
        $this->name = $this->table = 'catalog';
        $this->title = 'Каталог';
        $this->description = 'Каталог товаров';
        $this->model = \config('larrock.models.catalog', Catalog::class);
        $this->addRows()->addPositionAndActive()->isSearchable()->addPlugins();
    }

    protected function addPlugins()
    {
        $this->addPluginImages()->addPluginFiles()->addPluginSeo();
        return $this;
    }

    public function getRows()
    {
        if(file_exists(base_path(). '/vendor/fanamurov/larrock-wizard')){
            $this->mergeWizardConfig();
        }
        return $this->rows;
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
        $this->rows['cost'] = $row->setValid('max:15')->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')
            ->setInTableAdminAjaxEditable()->setSorted();

        $row = new FormInput('cost_old', 'Старая цена');
        $this->rows['cost_old'] = $row->setValid('max:15')->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4');

        $row = new FormSelect('what', 'Мера измерений');
        $this->rows['what'] = $row->setValid('max:15|required')->setAllowCreate()
            ->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')
            ->setConnect(Catalog::class)->setDefaultValue('руб./шт');

        $row = new FormInput('manufacture', 'Производитель');
        $this->rows['manufacture'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4');

        $row = new FormInput('articul', 'Артикул');
        $this->rows['articul'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setTemplate('in_card');

        $row = new FormInput('description_link', 'ID материала Feed для описания');
        $this->rows['description_link'] = $row->setCssClassGroup('uk-width-1-2 uk-width-medium-1-3 uk-width-large-1-4')->setFillable();

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

        $row = new FormHidden('user_id', 'user_id');
        $this->rows['user_id'] = $row->setDefaultValue(NULL);

        return $this;
    }

    /**
     * Объединение конфига компонента с конфигом каталога из Wizard
     * @return $this
     */
    public function mergeWizardConfig()
    {
        $data = \Cache::remember('wizard_config', 1440, function(){
            return Config::whereType('wizard')->whereName('catalog')->first();
        });

        if($data){
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
        $count = \Cache::remember('count-data-admin-'. LarrockCatalog::getName(), 1440, function(){
            return LarrockCatalog::getModel()->count(['id']);
        });
        $dropdown = LarrockCategory::getModel()->whereComponent('catalog')->whereLevel(1)->orderBy('position', 'desc')->get(['id', 'title', 'url']);
        $push = collect();
        if(file_exists(base_path(). '/vendor/fanamurov/larrock-wizard')){
            $push->put('Wizard - импорт товаров', '/admin/wizard');
        }
        if(file_exists(base_path(). '/vendor/fanamurov/larrock-discont')){
            $push->put('Скидки', '/admin/discount');
        }
        return view('larrock::admin.sectionmenu.types.dropdown', ['count' => $count, 'app' => LarrockCatalog::getConfig(), 'url' => '/admin/'. LarrockCatalog::getName(), 'dropdown' => $dropdown, 'push' => $push]);
    }

    public function createSitemap()
    {
        $tree = new Tree();
        if($activeCategory = $tree->listActiveCategories(LarrockCategory::getModel()->whereActive(1)->whereComponent('catalog')->whereParent(NULL)->get())){
            $table = LarrockCategory::getConfig()->table;

            return LarrockCatalog::getModel()->whereActive(1)->whereHas('get_category', function ($q) use ($activeCategory, $table){
                $q->where($table .'.sitemap', '=', 1)->whereIn($table .'.id', $activeCategory);
            })->get();
        }
        return [];
    }

    public function search($admin = NULL)
    {
        return Cache::remember('search'. $this->name. $admin, 1440, function() use ($admin){
            $data = [];
            if($admin){
                $items = LarrockCatalog::getModel()->with(['get_category'])->get(['id', 'title', 'url']);
            }else{
                $items = LarrockCatalog::getModel()->whereActive(1)->with(['getCategoryActive'])->get(['id', 'title', 'url']);
            }
            foreach ($items as $item){
                $data[$item->id]['id'] = $item->id;
                $data[$item->id]['title'] = $item->title;
                $data[$item->id]['full_url'] = $item->full_url;
                $data[$item->id]['component'] = $this->name;
                $data[$item->id]['category'] = NULL;
                if($admin){
                    if($item->get_category){
                        $data[$item->id]['category'] = $item->get_category->first()->title;
                    }
                }else{
                    if($item->get_categoryActive){
                        $data[$item->id]['category'] = $item->getCategoryActive->first()->title;
                    }
                }
            }
            if(count($data) === 0){
                return NULL;
            }
            return $data;
        });
    }
}