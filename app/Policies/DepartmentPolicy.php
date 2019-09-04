<?php

namespace App\Policies;

use App\Policies\Policy;
use App\Models\User;
use App\Models\Department;

class DepartmentPolicy extends Policy
{
    /**
     * Determine whether the user can view the departments.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        if ($user->can('departments_index')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the department.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return mixed
     */
    public function show(User $user, Department $department)
    {
        if ($user->can('departments_show')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create departments.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function store(User $user)
    {
        if ($user->can('departments_store')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the department.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return mixed
     */
    public function update(User $user, Department $department)
    {
        if ($user->can('departments_update')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the department.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return mixed
     */
    public function destroy(User $user, Department $department)
    {
        if ($user->can('departments_destroy')) {
            return true;
        }
    }
}
