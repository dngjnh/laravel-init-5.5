<?php

namespace App\Observers;

use App\Models\Permission;
use Exception;

class PermissionObserver
{
    /**
     * Listen to the Permission retrieved event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function retrieved(Permission $permission)
    {
        //
    }

    /**
     * Listen to the Permission creating event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function creating(Permission $permission)
    {
        //
    }

    /**
     * Listen to the Permission created event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function created(Permission $permission)
    {
        //
    }

    /**
     * Listen to the Permission updating event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function updating(Permission $permission)
    {
        //
    }

    /**
     * Listen to the Permission updated event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function updated(Permission $permission)
    {
        //
    }

    /**
     * Listen to the Permission saving event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function saving(Permission $permission)
    {
        //
    }

    /**
     * Listen to the Permission saved event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function saved(Permission $permission)
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Listen to the Permission deleting event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function deleting(Permission $permission)
    {
    }

    /**
     * Listen to the Permission deleted event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function deleted(Permission $permission)
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Listen to the Permission restoring event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function restoring(Permission $permission)
    {
        //
    }

    /**
     * Listen to the Permission restored event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function restored(Permission $permission)
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
