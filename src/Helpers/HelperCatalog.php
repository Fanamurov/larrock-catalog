<?php

namespace Larrock\ComponentCatalog\Helpers;

use Larrock\ComponentCategory\Models\Category;
use Larrock\Core\Models\Config as Model_Config;
use Cache;

/**
 * Class HelperCatalog
 * @package Larrock\ComponentCatalog\Helpers
 */
class HelperCatalog{

    protected $config;
    protected $wizard;

    public function listCatalog($category_url)
    {
        $data = Cache::remember('list_catalog'. $category_url, 1440, function() use ($category_url) {
            if($data['current'] = Category::whereUrl($category_url)->first()){
                $data['current_level'] = Category::whereParent($data['current']->parent)->get();
                $data['next_level'] = Category::whereParent($data['current']->id)->get();
                if(count($data['next_level']) < 1){
                    $data['current'] = Category::whereId($data['current']->parent)->first();
                    $data['next_level'] = $data['current_level'];
                    $data['current_level'] = Category::whereParent($data['current']->parent)->get();
                }
            }
            return $data;
        });
        return $data;
    }

    /** TODO: Сейчас не используется */
    public function mergeCatalogConfig()
    {
        Cache::forget('mergeCatalogConfig');
        return Cache::remember('mergeCatalogConfig', 1440, function(){
            $catalog = $config = \Config::get('components.catalog');
            if($wizard = Model_Config::whereType('wizard')->first()){
                $wizard_config = $wizard->value;

                if(isset($wizard->value)){
                    foreach ($wizard->value as $wizard) {
                        if(array_key_exists('db', $wizard) && !empty($wizard['db'])){
                            $config['rows'][$wizard['db']]['title'] = $wizard['slug'];
                            if( !isset($config['rows']['title']['type'])){
                                $config['rows'][$wizard['db']]['type'] = $wizard['admin'];
                                if(empty($config['rows'][$wizard['db']]['type'])){
                                    $config['rows'][$wizard['db']]['type'] = 'text';
                                }
                            }
                            if (array_get($wizard, 'filters') === 'lilu') {
                                $config['rows'][$wizard['db']]['filter'] = true;
                            } elseif (array_get($wizard, 'filters') === 'sort') {
                                $config['rows'][$wizard['db']]['sort'] = true;
                            } elseif (array_get($wizard, 'filters') === 'all') {
                                $config['rows'][$wizard['db']]['filter'] = true;
                                $config['rows'][$wizard['db']]['sort'] = true;
                            }
                        }
                    }
                }
            }else{
                $wizard_config = [];
            }

            return ['config' => $config, 'wizard' => $wizard_config, 'catalog' => $catalog];
        });
    }
}