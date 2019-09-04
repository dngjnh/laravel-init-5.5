<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dingo\Api\Exception\Handler;

class DingoApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        app(Handler::class)->register(function (\Illuminate\Auth\Access\AuthorizationException $exception) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('此操作未经授权');
        });
    }
}
