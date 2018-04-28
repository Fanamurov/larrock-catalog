<?php

namespace Larrock\ComponentCatalog;

use Cache;
use LarrockCatalog;
use LarrockCategory;
use Larrock\Core\Component;
use Larrock\Core\Helpers\Tree;
use Larrock\Core\Models\Config;
use Larrock\ComponentCatalog\Models\Param;
use Larrock\ComponentCatalog\Models\Catalog;
use Larrock\ComponentCategory\Models\Category;
use Larrock\Core\Helpers\FormBuilder\FormTags;
use Larrock\Core\Helpers\FormBuilder\FormInput;
use Larrock\Core\Helpers\FormBuilder\FormHidden;
use Larrock\Core\Helpers\FormBuilder\FormSelect;
use Larrock\Core\Helpers\FormBuilder\FormCheckbox;
use Larrock\Core\Helpers\FormBuilder\FormTextarea;

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
        if (file_exists(base_path().'/vendor/fanamurov/larrock-wizard')) {
            $this->mergeWizardConfig();
        }

        return $this->rows;
    }

    protected function addRows()
    {
        $row = new FormTags('category', 'Раздел');
        $this->setRow($row->setValid('required')
            ->setModels(Catalog::class, Category::class)
            ->setModelChildWhere('component', 'catalog'));

        $row = new FormInput('title', 'Название товара');
        $this->setRow($row->setValid('max:255|required')->setTypo()->setFillable());

        $row = new FormTags('param', 'Варианты поставки товара');
        $this->setRow($row->setModels(Catalog::class, Param::class)
            ->setAllowCreate()->setCostValue()->setFiltered()->setHelp('Данное поле переопределяет цену товара. 
            Стандартное поле "Цена" учитываться не будет. Внесение цен модификаций товара доступно после сохранения'));

        $row = new FormInput('cost', 'Цена');
        $this->setRow($row->setValid('max:15')->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')
            ->setInTableAdminEditable()->setSorted()->setFillable());

        $row = new FormInput('cost_old', 'Старая цена');
        $this->setRow($row->setValid('max:15')->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')
            ->setFillable()->setInTableAdminEditable());

        $row = new FormSelect('what', 'Мера измерений');
        $this->setRow($row->setValid('max:15|required')->setAllowCreate()
            ->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')
            ->setConnect(Catalog::class, null, 'what')
            ->setDefaultValue('руб./шт')->setFillable());

        $row = new FormTextarea('short', 'Короткое описание');
        $this->setRow($row->setTypo()->setFillable());

        $row = new FormTextarea('description', 'Полное описание');
        $this->setRow($row->setTypo()->setFillable());

        $row = new FormInput('manufacture', 'Производитель');
        $this->setRow($row->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')
            ->setFillable()->setFiltered()->setTemplate('in_card'));

        $row = new FormInput('articul', 'Артикул');
        $this->setRow($row->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')
            ->setTemplate('in_card')->setFillable());

        $row = new FormInput('description_link', 'ID материала Feed для описания');
        $this->setRow($row->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormCheckbox('label_new', 'Метка нового');
        $this->setRow($row->setTab('tags', 'Метки')
            ->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormCheckbox('label_popular', 'Метка популярное');
        $this->setRow($row->setTab('tags', 'Метки')
            ->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormInput('label_sale', 'Метка скидка (%)');
        $this->setRow($row->setTab('tags', 'Метки')
            ->setCssClassGroup('uk-width-1-2 uk-width-1-3@m')->setFillable());

        $row = new FormHidden('user_id', 'user_id');
        $this->setRow($row->setDefaultValue(null)->setFillable());

        return $this;
    }

    /**
     * Объединение конфига компонента с конфигом каталога из Wizard.
     * @return $this
     */
    public function mergeWizardConfig()
    {
        $data = \Cache::rememberForever('wizard_config', function () {
            if ($config = ! Config::whereType('wizard')->whereName('catalog')->first()) {
                return 'IGNORE';
            }

            return $config;
        });

        if ($data && $data !== 'IGNORE') {
            foreach ($data->value as $wizard_key => $wizard_item) {
                if (isset($this->rows[$wizard_item['db']])) {
                    if ($wizard_item['slug'] && ! empty($wizard_item['slug'])) {
                        $this->rows[$wizard_item['db']]->title = $wizard_item['slug'];
                    }
                    if ($wizard_item['filters']) {
                        if ($wizard_item['filters'] === 'lilu') {
                            $this->rows[$wizard_item['db']]->filtered = true;
                        }
                        if ($wizard_item['filters'] === 'sort') {
                            $this->rows[$wizard_item['db']]->sorted = true;
                        }
                    }
                    if ($wizard_item['template']) {
                        $this->rows[$wizard_item['db']]->template = $wizard_item['template'];
                    }
                } else {
                    //Добавляем поля созданные в визарде
                    if ($wizard_item['db']) {
                        if (empty($wizard_item['slug'])) {
                            $wizard_item['slug'] = $wizard_key;
                        }
                        $row = new FormInput($wizard_key, $wizard_item['slug']);
                        if ($wizard_item['filters']) {
                            if ($wizard_item['filters'] === 'lilu') {
                                $row->filtered = true;
                            }
                            if ($wizard_item['filters'] === 'sort') {
                                $row->sorted = true;
                            }
                        }
                        if ($wizard_item['template']) {
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
        $count = \Cache::rememberForever('count-data-admin-'.LarrockCatalog::getName(), function () {
            return LarrockCatalog::getModel()->count(['id']);
        });
        $dropdown = Cache::rememberForever('dropdownAdminMenu'.LarrockCatalog::getName(), function () {
            return LarrockCategory::getModel()->whereComponent('catalog')->whereLevel(1)
                ->orderBy('position', 'desc')->get(['id', 'title', 'url']);
        });
        $push = collect();
        if (file_exists(base_path().'/vendor/fanamurov/larrock-wizard')) {
            $push->put('Wizard - импорт товаров', '/admin/wizard');
        }
        if (file_exists(base_path().'/vendor/fanamurov/larrock-discount')) {
            $push->put('Скидки', '/admin/discount');
        }

        return view('larrock::admin.sectionmenu.types.dropdown', ['count' => $count, 'app' => LarrockCatalog::getConfig(),
            'url' => '/admin/'.LarrockCatalog::getName(), 'dropdown' => $dropdown, 'push' => $push, ]);
    }

    public function createSitemap()
    {
        $tree = new Tree();
        if ($activeCategory = $tree->listActiveCategories(LarrockCategory::getModel()->whereActive(1)
            ->whereComponent('catalog')->whereParent(null)->get())) {
            $table = LarrockCategory::getTable();

            return LarrockCatalog::getModel()->whereActive(1)->whereHas('getCategory', function ($q) use ($activeCategory, $table) {
                $q->where($table.'.sitemap', '=', 1)->whereIn($table.'.id', $activeCategory);
            })->get();
        }

        return [];
    }

    public function toDashboard()
    {
        $data = Cache::rememberForever('LarrockCatalogItemsDashboard', function () {
            return LarrockCatalog::getModel()->latest('updated_at')->take(5)->get();
        });

        return view('larrock::admin.dashboard.catalog', ['component' => LarrockCatalog::getConfig(), 'data' => $data]);
    }
}
