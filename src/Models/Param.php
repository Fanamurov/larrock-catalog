<?php

namespace Larrock\ComponentCatalog\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vid
 *
 * @property integer $id
 * @property string $title
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Vid whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Vid whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Vid whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Vid whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Param extends Model
{
    protected $table = 'option_param';

    protected $fillable = ['title'];
}
