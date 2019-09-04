<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as Model;
use App\Models\Traits\ResourceTrait;

class Permission extends Model
{
    use ResourceTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_basic' => 'boolean',
    ];
}
