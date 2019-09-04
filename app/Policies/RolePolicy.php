<?php

namespace App\Policies;

use App\Policies\Policy;
use App\Models\User;
use App\Models\Role;

class RolePolicy extends Policy
{
    /**
     * Determine whether the user can view the roles.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        if ($user->can('roles_index')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return mixed
     */
    public function show(User $user, Role $role)
    {
        if ($user->can('roles_show')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function store(User $user)
    {
        if ($user->can('roles_store')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return mixed
     */
    public function update(User $user, Role $role)
    {
        if ($user->can('roles_update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return mixed
     */
    public function destroy(User $user, Role $role)
    {
        if ($user->can('roles_destroy')) {
            return true;
        }
    }
}
