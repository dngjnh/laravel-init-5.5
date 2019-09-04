<?php

namespace App\Policies;

use App\Policies\Policy;
use App\Models\User;
use App\Models\Permission;

class PermissionPolicy extends Policy
{
    /**
     * Determine whether the user can view the permissions.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        if ($user->can('permissions_index')) {
            return true;
        }
    }
    /**
     * Determine whether the user can view the permission.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return mixed
     */
    public function show(User $user, Permission $permission)
    {
        if ($user->can('permissions_show')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create permissions.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function store(User $user)
    {
        if ($user->can('permissions_store')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the permission.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return mixed
     */
    public function update(User $user, Permission $permission)
    {
        if ($user->can('permissions_update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the permission.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return mixed
     */
    public function destroy(User $user, Permission $permission)
    {
        if ($user->can('permissions_destroy')) {
            return true;
        }
    }
}
