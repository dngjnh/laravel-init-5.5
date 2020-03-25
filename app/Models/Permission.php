<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as Model;
use Dngjnh\LaravelUtility\Traits\ModelResourceTrait;

class Permission extends Model
{
    use ModelResourceTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_basic' => 'boolean',
    ];
}
