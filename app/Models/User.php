<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\Models\Traits\ResourceTrait;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, ResourceTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 为 Passport 获取用户实例
     * 默认 email 字段
     *
     * @param string $username
     * @return object
     */
    // public function findForPassport(string $username)
    // {
    //     return $this->where('email', $username)->first();
    // }
}
