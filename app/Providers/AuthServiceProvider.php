<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\RouteRegistrar;
use Carbon\Carbon;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\Permission::class => \App\Policies\PermissionPolicy::class,
        \App\Models\Role::class => \App\Policies\RolePolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes(function (RouteRegistrar $routers) {
            $routers->forAccessTokens();
        });
        Passport::tokensExpireIn(Carbon::now()->addDays(5));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
    }
}
