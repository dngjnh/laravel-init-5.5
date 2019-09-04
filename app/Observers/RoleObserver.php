<?php

namespace App\Observers;

use App\Models\Role;
use Exception;

class RoleObserver
{
    /**
     * Listen to the Role retrieved event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function retrieved(Role $role)
    {
        //
    }

    /**
     * Listen to the Role creating event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function creating(Role $role)
    {
        //
    }

    /**
     * Listen to the Role created event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function created(Role $role)
    {
        //
    }

    /**
     * Listen to the Role updating event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function updating(Role $role)
    {
        if ($role->is_basic) {
            if ($role->isDirty(['name'])) {
                throw new Exception('基本角色不支持修改名称');
            }
        }
    }

    /**
     * Listen to the Role updated event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function updated(Role $role)
    {
        //
    }

    /**
     * Listen to the Role saving event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function saving(Role $role)
    {
        //
    }

    /**
     * Listen to the Role saved event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function saved(Role $role)
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Listen to the Role deleting event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function deleting(Role $role)
    {
        if ($role->is_basic) {
            throw new Exception('不能删除基本角色');
        }
    }

    /**
     * Listen to the Role deleted event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function deleted(Role $role)
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Listen to the Role restoring event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function restoring(Role $role)
    {
        //
    }

    /**
     * Listen to the Role restored event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function restored(Role $role)
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
