<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\Models\Permission::observe(\App\Observers\PermissionObserver::class);
        \App\Models\Role::observe(\App\Observers\RoleObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
