<?php

namespace Larrock\ComponentCatalog\Models;

use LarrockCategory;
use Larrock\Core\Component;
use Cache;
use Illuminate\Database\Eloquent\Model;
use LarrockFeed;
use Larrock\Core\Helpers\Plugins\RenderPlugins;
use Larrock\Core\Traits\GetLink;
use Nicolaslopezj\Searchable\SearchableTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use LarrockCatalog;
use Larrock\Core\Traits\GetFilesAndImages;
use Larrock\Core\Traits\GetSeo;
use Spatie\MediaLibrary\Media;

/**
 * App\Models\Catalog
 *
 * @property integer $id
 * @property integer $group
 * @property string $title
 * @property string $short
 * @property string $description
 * @property integer $category
 * @property string $url
 * @property string $what
 * @property float $cost
 * @property float $cost_old
 * @property string $manufacture
 * @property integer $position
 * @property string $articul
 * @property integer $active
 * @property integer $nalichie
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog find($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereGroup($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereShort($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereWhat($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereCost($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereCostOld($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereManufacture($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog wherePosition($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereArticul($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereNalichie($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereUpdatedAt($value)
 * @property string $vid_raz
 * @property string $razmer
 * @property string $weight
 * @property string $vid_up
 * @property string $date_vilov
 * @property string $sertifikacia
 * @property string $mesto
 * @property string $min_part
 * @property-read mixed $full_url
 * @property-read mixed $class_element
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereVidRaz($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereRazmer($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereVidUp($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereDateVilov($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereSertifikacia($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereMesto($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereMinPart($value)
 * @mixin \Eloquent
 * @property-read mixed $full_url_category
 * @property-read mixed $url_to_search
 * @property-read mixed $first_image
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog search($search, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog searchRestricted($search, $restriction, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @property string $print_vid
 * @property string $razmer_h
 * @property string $razmer_w
 * @property string $size
 * @property string $maket
 * @property integer $sales
 * @property integer $label_sale
 * @property integer $label_new
 * @property integer $label_popular
 * @property integer $user_id
 * @property mixed $description_render
 * @property mixed|null $description_item_on_link
 * @property mixed $short_render
 * @property-read mixed $cut_description
 * @property-read mixed $cost_discount
 * @property-read mixed $sizes
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog wherePrintVid($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereRazmerH($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereRazmerW($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereSize($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereMaket($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereSales($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereLabelSale($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereLabelNew($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereLabelPopular($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\Catalog whereUserId($value)
 */
class Catalog extends Model implements HasMediaConversions
{
    /**
     * @var $this Component
     */
    protected $config;
    
    use HasMediaTrait;
    use GetFilesAndImages;
    use GetSeo;
    use GetLink;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct();
        $columns = Cache::rememberForever('fillableCatalog', function(){
            $columns = \Schema::getColumnListing('catalog');
            $columns = collect($columns)->except(['id', 'created_at', 'updated_at']);
            return $columns->toArray();
        });

        $this->fillable(LarrockCatalog::addFillableUserRows($columns));
        $this->bootIfNotBooted();
        $this->syncOriginal();
        $this->fill($attributes);
        $this->config = LarrockCatalog::getConfig();
        $this->table = LarrockCatalog::getTable();
    }

    use SearchableTrait;

    // no need for this, but you can define default searchable columns:
    protected $searchable = [
        'columns' => [
            'catalog.title' => 10
        ]
    ];

    protected $casts = [
        'position' => 'integer',
        'active' => 'integer',
        'cost' => 'float',
        'cost_old' => 'float',
        'nalichie' => 'integer'
    ];

    protected $appends = [
        'full_url',
        'full_url_category',
        'url_to_search',
        'class_element',
        'first_image'
    ];

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Alias
     * @return mixed
     */
    public function get_category()
    {
        return $this->getLink(LarrockCategory::getModelName());
    }

    /**
     * Alias
     * @return mixed
     */
    public function getCategoryActive()
    {
        return $this->getLink(LarrockCategory::getModelName())->where(LarrockCategory::getTable() .'.active', '=', 1);
    }

    public function getFullUrlAttribute()
    {
        return Cache::rememberForever('url_catalog'. $this->id, function() {
            $url = '/catalog';
            foreach ($this->getLink(LarrockCategory::getModelName())->first()->parent_tree as $category){
                $url .= '/'. $category->url;
            }
            $url .= '/'. $this->url;
            return $url;
        });
    }

    public function getFullUrlCategoryAttribute()
    {
        if($get_category = $this->getLink(LarrockCategory::getModelName())->first()){
            return $get_category->full_url;
        }
        return NULL;
    }

    public function getDescriptionItemOnLinkAttribute()
    {
        if(config('larrock.catalog.DescriptionCatalogItemLink')){
            return LarrockFeed::getModel()->find($this->description_link);
        }
        return NULL;
    }

    public function getUrlToSearchAttribute()
    {
        return '/search/catalog/serp/'. \Request::get('q');
    }

    public function getClassElementAttribute()
    {
        return 'product';
    }

    public function getCutDescriptionAttribute()
    {
        if( !empty($this->short)){
            return str_limit(strip_tags($this->short), 150, '...');
        }
        return str_limit(strip_tags($this->description), 150, '...<a href="'. $this->full_url .'">далее</a>');
    }

     /**
     * Замена тегов плагинов на их данные
     * @return mixed
     */
    public function getShortRenderAttribute()
    {
        $cache_key = 'ShortRender'. $this->table.'-'. $this->id;
        if(\Auth::check()){
            $cache_key .= '-'. \Auth::user()->role->first()->level;
        }

        return \Cache::rememberForever($cache_key, function(){
            $renderPlugins = new RenderPlugins($this->short, $this);
            $render = $renderPlugins->renderBlocks()->renderImageGallery()->renderFilesGallery();
            return $render->rendered_html;
        });
    }

    /**
     * Замена тегов плагинов на их данные
     * @return mixed
     */
    public function getDescriptionRenderAttribute()
    {
        $cache_key = 'DescriptionRender'. $this->table.'-'. $this->id;
        if(\Auth::check()){
            $cache_key .= '-'. \Auth::user()->role->first()->level;
        }

        return \Cache::rememberForever($cache_key, function(){
            $renderPlugins = new RenderPlugins($this->description, $this);
            $render = $renderPlugins->renderBlocks()->renderImageGallery()->renderFilesGallery();
            return $render->rendered_html;
        });
    }

    /**
     * Перезаписываем метод из HasMediaTrait, добавляем кеш
     * @param string $collectionName
     * @return mixed
     */
    public function loadMedia(string $collectionName)
    {
        $cache_key = sha1('loadMediaCache'. $collectionName . $this->id . $this->getConfig()->getModelName());
        return Cache::rememberForever($cache_key, function () use ($collectionName) {
            $collection = $this->exists
                ? $this->media
                : collect($this->unAttachedMediaLibraryItems)->pluck('media');

            return $collection
                ->filter(function (Media $mediaItem) use ($collectionName) {
                    if ($collectionName == '') {
                        return true;
                    }

                    return $mediaItem->collection_name === $collectionName;
                })
                ->sortBy('order_column')
                ->values();
        });
    }
}