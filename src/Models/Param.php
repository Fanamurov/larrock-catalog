<?php

namespace Larrock\ComponentCatalog\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Larrock\ComponentCatalog\Models.
 *
 * @property int $id
 * @property string $title
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 */
class Param extends Model
{
    protected $table = 'option_param';

    protected $fillable = ['title'];
}
