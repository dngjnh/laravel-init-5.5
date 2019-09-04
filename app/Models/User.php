<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\Models\Traits\ResourceTrait;
use Spatie\Permission\Traits\HasRoles;
use Exception;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, ResourceTrait, HasRoles;

    protected $guard_name = 'api';

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

    /**
     * 更新角色
     * 管理员必须包含管理员角色
     *
     * @param array $roles
     * @throws \Exception
     */
    public function syncRolesWithChecking($roles)
    {
        if ($this->id == 1) {
            if (!in_array('administrator', $roles)) {
                throw new Exception('管理员必须关联管理员角色');
            }
        }
        $this->syncRoles($roles);
    }
}
