<?php

namespace App\Providers;

use Dingo\Api\Contract\Auth\Provider as ServiceProvider;
use Dingo\Api\Routing\Route;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class DingoAuthPassportServiceProvider implements ServiceProvider
{
    /**
     * The instance of the Passport TokenGuard that handles authentication
     * for API requests.
     *
     * @var Illuminate\Contracts\Auth\Guard
     */
    protected $guard;

    /**
     * Create a new instance using the Guard implementation configured for
     * Passport.
     *
     * @param AuthManager $auth Used to fetch the Passport Guard
     */
    public function __construct(AuthManager $auth)
    {
        // This should match the name of the "guard" set in config/auth.php
        // for API requests that uses the "passport" driver:
        $this->guard = $auth->guard('api');
    }

    /**
     * Authenticate the request and return the authenticated user instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Dingo\Api\Routing\Route $route
     *
     * @return mixed
     */
    public function authenticate(Request $request, Route $route)
    {
        if ($this->guard->check()) {
            return $this->guard->user();
        }

        throw new UnauthorizedHttpException('Not authenticated via Passport.', '授权无效，请重新登录');
    }
}
