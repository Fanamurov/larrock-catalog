<?php

namespace Larrock\ComponentCatalog\Models;

use Larrock\ComponentCategory\Facades\LarrockCategory;
use Larrock\ComponentDiscount\Helpers\DiscountHelper;
use Cache;
use Illuminate\Database\Eloquent\Model;
use Larrock\Core\Models\Seo;
use Nicolaslopezj\Searchable\SearchableTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Larrock\ComponentCatalog\Facades\LarrockCatalog;
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
    use HasMediaTrait;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $columns = Cache::remember('fillableCatalog', 1440, function(){
            $columns = \Schema::getColumnListing('catalog');
            $columns = collect($columns)->except(['id', 'user_id', 'created_at', 'updated_at']);
            return $columns->toArray();
        });

        $this->fillable($columns);
        $this->bootIfNotBooted();
        $this->syncOriginal();
        $this->fill($attributes);
    }

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('110x110')
            ->height(110)->width(110)
            ->performOnCollections('images');

        $this->addMediaConversion('140x140')
            ->height(140)->width(140)
            ->performOnCollections('images');
    }

	use SearchableTrait;

	// no need for this, but you can define default searchable columns:
	protected $searchable = [
		'columns' => [
			'catalog.title' => 10
		]
	];

    protected $table = 'catalog';

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

	public function get_category()
	{
		return $this->belongsToMany(LarrockCategory::getModelName(), 'category_catalog', 'catalog_id', 'category_id');
	}

	public function get_seo()
	{
		return $this->hasOne(Seo::class, 'seo_id_connect', 'id')->whereSeoTypeConnect('catalog');
	}

	public function getFullUrlAttribute()
	{
        return Cache::remember('url_catalog'. $this->id, 1440, function() {
            $url = '/catalog';
            foreach ($this->get_category()->first()->parent_tree as $category){
                $url .= '/'. $category->url;
            }
            $url .= '/'. $this->url;
            return $url;
		});
	}

	public function getFullUrlCategoryAttribute()
	{
		if($get_category = $this->get_category->first()){
			return $get_category->full_url;
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

	public function getImages()
	{
		return $this->hasMany('Spatie\MediaLibrary\Media', 'model_id', 'id')->where([['model_type', '=', LarrockCatalog::getModelName()], ['collection_name', '=', 'images']])->orderBy('order_column', 'DESC');
	}
	public function getFirstImage()
	{
		return $this->hasOne('Spatie\MediaLibrary\Media', 'model_id', 'id')->where([['model_type', '=', LarrockCatalog::getModelName()], ['collection_name', '=', 'images']])->orderBy('order_column', 'DESC');
	}

	public function getFirstImageAttribute()
	{
		$value = Cache::remember('image_f_tovar'. $this->id, 1440, function() {
			if($get_image = $this->getFirstImage()->first()){
				return $get_image->getUrl();
			}
            return '/_assets/_front/_images/empty_big.png';
		});
		return $value;
	}

    public function getFiles()
    {
        return $this->hasMany('Spatie\MediaLibrary\Media', 'model_id', 'id')->where([['model_type', '=', LarrockCatalog::getModelName()], ['collection_name', '=', 'files']])->orderBy('order_column', 'DESC');
    }

	public function getCutDescriptionAttribute()
	{
		if( !empty($this->short)){
			return str_limit(strip_tags($this->short), 150, '...');
		}
        return str_limit(strip_tags($this->description), 150, '...<a href="'. $this->full_url .'">далее</a>');
	}

    public function getCostDiscountAttribute()
    {
        $discountHelper = new DiscountHelper();
        return $discountHelper->getCostDiscount($this);
    }

    public function get_param()
    {
        return $this->belongsToMany(Param::class, 'option_param_link', 'catalog_id', 'param_id');
    }
}