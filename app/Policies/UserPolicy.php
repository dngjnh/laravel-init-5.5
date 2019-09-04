<?php

namespace App\Policies;

use App\Policies\Policy;
use App\Models\User;

class UserPolicy extends Policy
{
    /**
     * Determine whether the user can view the model list.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        if ($user->can('users_index')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function show(User $user, User $model)
    {
        if ($user->can('users_show')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function store(User $user)
    {
        if ($user->can('users_store')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        if ($user->can('users_update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function destroy(User $user, User $model)
    {
        if ($user->can('users_destroy')) {
            return true;
        }
    }
}
