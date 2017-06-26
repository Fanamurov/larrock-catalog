<?php

namespace Larrock\ComponentCatalog\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CategoryCatalog
 *
 * @property integer $category_id
 * @property integer $catalog_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\CategoryCatalog whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\CategoryCatalog whereCatalogId($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\CategoryCatalog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\CategoryCatalog whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property integer $id
 * @method static \Illuminate\Database\Query\Builder|\Larrock\ComponentCatalog\Models\CategoryCatalog whereId($value)
 */
class CategoryCatalog extends Model
{
    protected $table = 'category_catalog';

	protected $fillable = ['category_id', 'catalog_id'];
}
