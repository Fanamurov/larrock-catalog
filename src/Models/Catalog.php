<?php

namespace Larrock\ComponentCatalog\Models;

use Larrock\ComponentDiscount\Helpers\DiscountHelper;
use Cache;
use Illuminate\Database\Eloquent\Model;
use Larrock\ComponentCategory\Models\Category;
use Larrock\Core\Models\Seo;
use Nicolaslopezj\Searchable\SearchableTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Larrock\ComponentCatalog;

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
        $columns = \Schema::getColumnListing('catalog');
        $hide_columns = ['id', 'user_id', 'created_at', 'updated_at'];
        foreach ($columns as $key => $value){
            if(in_array($value, $hide_columns, TRUE)){
                unset($columns[$key]);
            }
        }
        $this->fillable($columns);

        $this->bootIfNotBooted();

        $this->syncOriginal();

        $this->fill($attributes);
    }

    public function registerMediaConversions()
    {
        $this->addMediaConversion('110x110')
            ->setManipulations(['w' => 110, 'h' => 110])
            ->performOnCollections('images');

        $this->addMediaConversion('140x140')
            ->setManipulations(['w' => 140, 'h' => 140])
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
		return $this->belongsToMany(Category::class, 'category_catalog', 'catalog_id', 'category_id');
	}

	public function get_seo()
	{
		return $this->hasOne(Seo::class, 'id_connect', 'id')->whereTypeConnect('catalog');
	}

	public function getFullUrlAttribute()
	{
		$full_url = Cache::remember('url_catalog'. $this->id, 1440, function() {
			if($this->get_category->first()){
				if($search_parent = Category::whereId($this->get_category->first()->parent)->first()){
					if($search_parent_2 = Category::whereId($search_parent->parent)->first()){
						if($search_parent_3 = Category::whereId($search_parent->parent_2)->first()){
							return '/catalog/'. $search_parent_3->url .'/'. $search_parent_2->url .'/' . $search_parent->url .'/'. $this->get_category->first()->url .'/'. $this->url;
						}
                        return '/catalog/'. $search_parent_2->url .'/' . $search_parent->url .'/'. $this->get_category->first()->url .'/'. $this->url;
					}
                    return '/catalog/' . $search_parent->url .'/'. $this->get_category->first()->url .'/'. $this->url;
				}
                return '/catalog/'. $this->get_category->first()->url .'/'. $this->url;
			}
            return '/catalog/'. $this->url;
		});
		return $full_url;
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
        $config = new ComponentCatalog\CatalogComponent();
		return $this->hasMany('Spatie\MediaLibrary\Media', 'model_id', 'id')->where([['model_type', '=', $config->model], ['collection_name', '=', 'images']])->orderBy('order_column', 'DESC');
	}
	public function getFirstImage()
	{
        $config = new ComponentCatalog\CatalogComponent();
		return $this->hasOne('Spatie\MediaLibrary\Media', 'model_id', 'id')->where([['model_type', '=', $config->model], ['collection_name', '=', 'images']])->orderBy('order_column', 'DESC');
	}

	public function getFirstImageAttribute()
	{
		$value = Cache::remember('image_f_tovar'. $this->id, 1440, function() {
			if($get_image = $this->getMedia('images')->sortByDesc('order_column')->first()){
				return $get_image->getUrl();
			}
            return '/_assets/_front/_images/empty_big.png';
		});
		return $value;
	}

    public function getFiles()
    {
        $config = new ComponentCatalog\CatalogComponent();
        return $this->hasMany('Spatie\MediaLibrary\Media', 'model_id', 'id')->where([['model_type', '=', $config->model], ['collection_name', '=', 'files']])->orderBy('order_column', 'DESC');
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

    /*public function getSizesAttribute()
    {
        $value = '';
        $sizes = $this->get_sizes;
        return Cache::remember('getSizesAttribute'. $this->id, 1440, function() use ($sizes, $value) {
            if($sizes){
                $count = count($sizes)-1;
                foreach($sizes as $key => $value_arr){
                    $value .= $value_arr->title;
                    if($key !== $count){
                        $value .= ', ';
                    }
                }
            }
            return $value;
        });
    }*/

    public function get_param()
    {
        return $this->belongsToMany(Param::class, 'option_param_link', 'catalog_id', 'param_id');
    }
}
