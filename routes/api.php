<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app(\Dingo\Api\Routing\Router::class);

$api->version('v1', function ($api) {
    // 认证相关
    $api->group([
        'prefix' => '/auth',
    ], function ($api) {
        // 退出登录
        $api->put('/logout', 'App\Http\Controllers\Api\V1\AuthController@logout');

        // 当前登录者信息
        $api->get('/me', 'App\Http\Controllers\Api\V1\AuthController@me');

        // 关联的角色
        $api->get('/roles', 'App\Http\Controllers\Api\V1\AuthController@roles');

        // 关联的权限
        $api->get('/permissions', 'App\Http\Controllers\Api\V1\AuthController@permissions');
    });

    // 权限
    $api->resource('/permissions', 'App\Http\Controllers\Api\V1\PermissionController', ['only' => [
        'index',
        'show',
        'update',
    ]]);

    // 角色
    $api->resource('/roles', 'App\Http\Controllers\Api\V1\RoleController', ['only' => [
        'index',
        'store',
        'show',
        'update',
        'destroy',
    ]]);

    // 用户
    $api->resource('/users', 'App\Http\Controllers\Api\V1\UserController', ['only' => [
        'index',
        'store',
        'show',
        'update',
        'destroy',
    ]]);
});
