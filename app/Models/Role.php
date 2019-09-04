<?php

namespace App\Models;

use Spatie\Permission\Models\Role as Model;
use App\Models\Traits\ResourceTrait;
use Exception;

class Role extends Model
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

    /**
     * 更新权限
     * 管理员必须拥有所有基本权限
     *
     * @param array $permissions
     * @throws \Exception
     */
    public function syncPermissionsWithChecking($permissions)
    {
        if ($this->name === 'administrator') {
            $basicPermissions = Permission::where('is_basic', true)->pluck('name')->toArray();
            if (!empty(array_diff($basicPermissions, $permissions))) {
                throw new Exception('管理员角色必须包含所有基本权限');
            }
        }
        $this->syncPermissions($permissions);
    }
}
