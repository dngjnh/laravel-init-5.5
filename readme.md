# Laravel Init 5.5

## 目录

[TOC]

## 简介

整合了大多数项目通用功能的 Laravel 5.5 初始项目，用于快速安装、接入具体业务。

- 用户认证使用 `laravel/passport`
- 接口开发使用 `dingo/api`
- 角色权限管理使用 `spatie/laravel-permission`

## 版本信息

1. v1.0.0

   基于 Laravel 5.5。

## 使用

1. 下载本项目并重命名为你的项目名称。

2. 安装依赖包。

3. 复制 `.env.example` 文件创建 `.env` 环境配置文件。

4. 生成应用密钥

   ```bash
   $ php artisan key:generate
   ```

5. 配置项目数据库相关参数

6. 迁移数据库

   ```bash
   $ php artisan migrate
   ```

7. 创建生成安全访问令牌时所需的加密密钥，同时创建用于生成访问令牌的「个人访问」客户端和「密码授权」客户端

   ```bash
   $ php artisan passport:install
   ```

8. 配置 `.env` 里面的 `API_SUBTYPE`、`API_PREFIX`、`API_DEBUG`。

注意事项：

1. 项目的基础数据填充，使用迁移的形式，方便开发及部署。

## 预先配置项

此处按步骤先后顺序列出配置项及影响的相关文件，以便根据项目实际情况，移除不必要的配置项。

### 自定义全局函数

函数文件为 `app/Support/helpers.php`，可后续自行增删改查。

### 设置时区

在 `config/app.php` 中，设置了：

```php
'timezone' => 'Asia/Hong_Kong',
```

### 本地化语言

安装 `overtrue/laravel-lang` 包。

### 解决接口跨域问题

1. 安装 `fruitcake/laravel-cors` 包。

2. 修改 `config/cors.php` 中使用 CORS 服务的路径：

   ```php
   'paths' => [
       'api/*',
   ],
   ```

### 修改项目数据库模型的放置路径

修改项目数据库模型的放置路径，由 `app` 改为 `app\Models`。

- 将 `app\User.php` 移动为 `app\Models\User.php`。

  把

  ```php
  namespace App;
  ```

  改为

  ```php
  namespace App\Models;
  ```

- 更新 `app/Http/Controllers/Auth/RegisterController.php`：

  把

  ```php
  use App\User;
  ```

  改为

  ```php
  use App\Models\User;
  ```

  把

  ```php
  * @return \App\User
  ```

  改为

  ```php
  * @return \App\Models\User
  ```

- 更新 `config/auth.php`

  把

  ```php
  'model' => App\User::class,
  ```

  改为

  ```php
  'model' => App\Models\User::class,
  ```

- 更新 `config/services.php`

  把

  ```php
  'stripe' => [
      'model' => App\User::class,
      'key' => env('STRIPE_KEY'),
      'secret' => env('STRIPE_SECRET'),
  ],
  ```

  改为

  ```php
  'stripe' => [
      'model' => App\Models\User::class,
      'key' => env('STRIPE_KEY'),
      'secret' => env('STRIPE_SECRET'),
  ],
  ```

- 更新 `database/factories/UserFactory.php`

  把

  ```php
  $factory->define(App\User::class, function (Faker $faker) {
  ```

  改为

  ```php
  $factory->define(App\Models\User::class, function (Faker $faker) {
  ```

### 填充管理员账号 admin

1. 新建数据库迁移文件

   ```bash
   $ php artisan make:migration seed_users_table
   ```

2. 修改内容如下：

   ```php
   <?php

   use Illuminate\Support\Facades\Schema;
   use Illuminate\Support\Facades\DB;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Database\Migrations\Migration;
   use App\Models\User;

   class SeedUsersTable extends Migration
   {
       /**
        * Run the migrations.
        *
        * @return void
        */
       public function up()
       {
           $user = factory(User::class)->make();
           $user_array = $user->makeVisible(['password', 'remember_token'])->toArray();

           User::insert($user_array);

           // admin
           $user = User::find(1);
           $user->name = 'admin';
           $user->email = 'admin@localhost.test';
           $user->save();
       }

       /**
        * Reverse the migrations.
        *
        * @return void
        */
       public function down()
       {
           DB::table('users')->truncate();
       }
   }
   ```

### Passport OAuth 认证

1. 安装 `laravel/passport` 包

   ```bash
   $ composer require laravel/passport=~4.0
   ```

2. 运行 Passport 的迁移命令来自动创建存储客户端和令牌的数据表

   ```bash
   $ php artisan migrate
   ```

3. 创建生成安全访问令牌时所需的加密密钥，同时创建用于生成访问令牌的「个人访问」客户端和「密码授权」客户端

   ```bash
   $ php artisan passport:install
   ```

4. 将 `Laravel\Passport\HasApiTokens` Trait 添加到 `App\Models\User` 模型中

   ```php
   // ...
   use Laravel\Passport\HasApiTokens;
   // ...
   use Notifiable;
   ```

5. 在 `AuthServiceProvider` 的 `boot` 方法中调用 `Passport::routes` 函数。这个函数会注册发出访问令牌并撤销访问令牌、客户端和个人访问令牌所必需的路由。同时定义令牌的有效期。

   ```php
   // ...
   use Laravel\Passport\Passport;
   use Laravel\Passport\RouteRegistrar;
   use Carbon\Carbon;
   // ...
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
   ```

6. 配置文件 `config/auth.php` 中授权看守器 `guards` 的 `api` 的 `driver` 选项改为 `passport`

   ```php
   'guards' => [
       'web' => [
           'driver' => 'session',
           'provider' => 'users',
       ],

       'api' => [
           'driver' => 'passport',
           'provider' => 'users',
       ],
   ],
   ```

7. 在 `app\Models\User.php` 中新增方法，自定义 Passport 获取用户实例所用的默认字段和错误提示信息

   ```php
   <?php
   
   namespace App\Models;
   
   // ...
   use League\OAuth2\Server\Exception\OAuthServerException;
   
   class User extends Authenticatable
   {
       // ...
       use HasApiTokens;
       // ...
       /**
        * 为 Passport 获取用户实例
        * 默认 email 字段
        *
        * @param string $username
        * @return object
        */
       public function findForPassport(string $username)
       {
           $user = $this->where('email', $username)->first();
           if (empty($user)) {
               throw new OAuthServerException('用户不存在', 6, 'invalid_username', 401);
           }
   
           return $user;
       }
   
       /**
        * 为 Passport 验证用户是否有效
        *
        * @param string $password
        * @return true
        * @throws \Exception
        */
       public function validateForPassportPasswordGrant($password)
       {
           $hasher = app(\Illuminate\Contracts\Hashing\Hasher::class);
           if (!$hasher->check($password, $this->getAuthPassword())) {
               throw new OAuthServerException('密码不正确', 6, 'incorrect_password', 401);
           }
   
           // if ($this->id != 1 && $this->user_status_id != 1) {
           //     throw new OAuthServerException('用户状态异常，请联系管理员', 6, 'abnormal_status', 401);
           // }
   
           return true;
       }
       // ...
   }
   ```

8. 创建 `OauthAccessToken` 模型

   ```php
   <?php

   namespace App\Models;

   use Laravel\Passport\Token as Model;

   class OauthAccessToken extends Model
   {
       //
   }
   ```

9. 创建 `OauthRefreshToken` 模型

   ```php
   <?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Model;

   class OauthRefreshToken extends Model
   {
       /**
        * The table associated with the model.
        *
        * @var string
        */
       protected $table = 'oauth_refresh_tokens';

       /**
        * The "type" of the auto-incrementing ID.
        *
        * @var string
        */
       protected $keyType = 'string';

       /**
        * Indicates if the IDs are auto-incrementing.
        *
        * @var bool
        */
       public $incrementing = false;

       /**
        * The attributes that should be cast to native types.
        *
        * @var array
        */
       protected $casts = [
           'revoked' => 'bool',
       ];

       /**
        * The attributes that should be mutated to dates.
        *
        * @var array
        */
       protected $dates = [
           'expires_at',
       ];

       /**
        * Indicates if the model should be timestamped.
        *
        * @var bool
        */
       public $timestamps = false;

       /**
        * The guarded attributes on the model.
        *
        * @var array
        */
       protected $guarded = [];
   }
   ```

10. Passport 在发出访问令牌和刷新令牌时触发事件。 在应用程序的 `EventServiceProvider` 中为这些事件追加监听器，可以通过触发这些事件来修改或删除数据库中的其他访问令牌

    ```php
    protected $listen = [
       \Laravel\Passport\Events\AccessTokenCreated::class => [
           \App\Listeners\RevokeOldTokens::class,
       ],

       \Laravel\Passport\Events\RefreshTokenCreated::class => [
           \App\Listeners\PruneOldTokens::class,
       ],
    ];
    ```

    创建 `app/Listeners/PruneOldTokens.php`：

    ```php
    <?php

    namespace App\Listeners;

    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Laravel\Passport\Events\RefreshTokenCreated;
    use App\Models\OauthRefreshToken;

    class PruneOldTokens
    {
       /**
        * Create the event listener.
        *
        * @return void
        */
       public function __construct()
       {
           //
       }

       /**
        * Handle the event.
        *
        * @param  RefreshTokenCreated  $event
        * @return void
        */
       public function handle(RefreshTokenCreated $event)
       {
           OauthRefreshToken::where('id', '<>', $event->refreshTokenId)
               ->where('access_token_id', '<>', $event->accessTokenId)
               ->update([
                   'revoked' => true,
               ]);
       }
    }
    ```

    创建 `app/Listeners/RevokeOldTokens.php`：

    ```php
    <?php

    namespace App\Listeners;

    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Laravel\Passport\Events\AccessTokenCreated;
    use App\Models\OauthAccessToken;

    class RevokeOldTokens
    {
       /**
        * Create the event listener.
        *
        * @return void
        */
       public function __construct()
       {
           //
       }

       /**
        * Handle the event.
        *
        * @param  AccessTokenCreated  $event
        * @return void
        */
       public function handle(AccessTokenCreated $event)
       {
           OauthAccessToken::where('id', '<>', $event->tokenId)
               ->where('user_id', '=', $event->userId)
               ->where('client_id', '=', $event->clientId)
               ->update([
                   'revoked' => true,
               ]);
       }
    }
    ```

### Dingo API 构建接口

1. 安装 `dingo/api` 包

   ```bash
   $ composer require dingo/api:2.2.3
   ```

   目前只能安装 2.2.3 版本，否则表单验证通过后会[报错](https://github.com/dingo/api/commit/37744e2093ffac8dff7918e7c98eebfc67fba337)：`Method validateResolved does not exist.`。

2. 发布和配置 `config/api.php`

   发布

   ```bash
   $ php artisan vendor:publish --provider="Dingo\Api\Provider\LaravelServiceProvider"
   ```

   配置接口路由默认使用的前缀 `prefix`。

3. 自定义认证服务提供者

   创建 `app/Providers/DingoAuthPassportServiceProvider.php`：

   ```php
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
   ```

   配置 `config/api.php` 里面的 `auth`：

   ```php
   'auth' => [
       'passport' => \App\Providers\DingoAuthPassportServiceProvider::class
   ],
   ```

   配置 `config/api.php` 里面的 `auth`：

   ```php
   'auth' => [
       'passport' => \App\Providers\DingoAuthPassportServiceProvider::class
   ],
   ```

4. 自定义 Dingo Api 认证路由中间组，使上面自定义的认证服务提供者能桥接认证服务。在 `app/Http/Kernel.php` 的 `$middlewareGroups` 数组中新增：

   ```php
   // Convenience group containing the auth middleware for Passport and
   // Dingo so that the custom 'App\Providers\PassportDingoAuthProvider'
   // can bridge the auth systems:
   // api.auth 和 auth:api 的顺序很重要
   'auth:api-combined' => [
       'api.auth', // Dingo
       'auth:api', // Passport
   ],
   ```

5. 第一条 Dingo Api 认证路由

   修改 `routes/api.php` 内容为：

   ```php
   <?php
   $api = app(\Dingo\Api\Routing\Router::class);
   
   $api->version('v1', function ($api) {
       // 获取当前登录者
       $api->get('/user', function () {
           return Auth::user();
       })->middleware('auth:api-combined');
   });
   ```

6. 自定义 Api 控制器基类

   创建 `app/Http/Controllers/Api/ApiController.php`：

   ```php
   <?php
   
   namespace App\Http\Controllers\Api;
   
   use App\Http\Controllers\Controller;
   use Dingo\Api\Routing\Helpers;
   use Illuminate\Http\Request;
   
   class ApiController extends Controller
   {
       use Helpers;
   }
   ```

7. 自定义 Dingo Api 服务提供者，用于自定义异常响应或其他

   创建 `app/Providers/DingoApiServiceProvider.php`：

   ```php
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
   ```

   在 `config/app.php` 的 `providers` 注册：

   ```php
   $providers = [
       // ...
       App\Providers\DingoApiServiceProvider::class,
   ];
   ```

8. 认证相关路由

   修改 `routes/api.php` 内容为：

   ```php
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
       });
   });
   ```

   创建 `app/Http/Controllers/Api/V1/AuthController.php`：

   ```php
   <?php
   
   namespace App\Http\Controllers\Api\V1;
   
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;
   use App\Http\Controllers\Api\ApiController as Controller;
   use App\Models\OauthAccessToken;
   
   class AuthController extends Controller
   {
       public function __construct()
       {
           $this->middleware('auth:api-combined', ['except' => [
               //
           ]]);
       }
   
       /**
        * 退出登录
        *
        * @return \Illuminate\Http\Response
        */
       public function logout()
       {
           $user = Auth::user();
           $token = $user->token();
   
           // 删除 access_token
           OauthAccessToken::where('user_id', '=', $token->user_id)
               ->where('client_id', '=', $token->client_id)
               ->update([
                   'revoked' => true,
               ]);
   
           return $this->response->noContent();
       }
   
       /**
        * 当前登录者信息
        *
        * @return \Illuminate\Http\Response
        */
       public function me()
       {
           $user = Auth::user();
   
           return $this->response->array($user->toArray());
       }
   }
   ```

### 安装 dngjnh/laravel-utility 包

```bash
$ composer require dngjnh/laravel-utility
```

### 用户的基本增删改查（观察器）

修改 `routes/api.php` 内容如下：

```php
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
    });

    // 用户
    $api->resource('/users', 'App\Http\Controllers\Api\V1\UserController', ['only' => [
        'index',
        'store',
        'show',
        'update',
        'destroy',
    ]]);
});
```

修改 `app/Models/User.php`，引用 `Dngjnh/LaravelUtility/Traits/ModelResourceTrait`。

创建用户模型观察器 `app/ObserversUserObserver.php`：

```php
<?php

namespace App\Observers;

use App\Models\User;
use Exception;

class UserObserver
{
    /**
     * Listen to the User retrieved event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function retrieved(User $user)
    {
        //
    }

    /**
     * Listen to the User creating event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function creating(User $user)
    {
        //
    }

    /**
     * Listen to the User created event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        //
    }

    /**
     * Listen to the User updating event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updating(User $user)
    {
        if ($user->id == 1) {
            // unset($user->name);
            // unset($user->email);

            if ($user->name != 'admin' || $user->email != 'admin@localhost.test') {
                throw new Exception('不允许修改管理员昵称和邮箱');
            }
        }
    }

    /**
     * Listen to the User updated event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Listen to the User saving event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function saving(User $user)
    {
        if ($user->getAttribute('id') != 1) {
            if (mb_stripos($user->name, 'admin') === 0) {
                throw new Exception('非管理员，用户名不能以「admin」开头，无论大小写');
            }
        }
    }

    /**
     * Listen to the User saved event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function saved(User $user)
    {
        //
    }

    /**
     * Listen to the User deleting event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        if ($user->id == 1) {
            throw new Exception('不允许删除管理员');
        }
    }

    /**
     * Listen to the User deleted event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Listen to the User restoring event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restoring(User $user)
    {
        //
    }

    /**
     * Listen to the User restored event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }
}
```

在 `app/Providers/AppServiceProvider.php` 中注册上面定义的观察器：

```php
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
```

创建接口请求基类 `app/Http/Controllers/Api/V1/UserController.php`：

```php
<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Dingo\Api\Exception\ResourceException;

class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData()
    {
        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     *
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ResourceException('请求参数有误', $validator->errors());
    }
}
```

创建用户创建请求验证类 `app/Http/Requests/Api/V1/UserStoreRequest.php`：

```php
<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\ApiRequest;

class UserStoreRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email',
            'password' => 'required|string|min:6|max:15',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => '昵称',
            'email' => '邮箱',
            'password' => '密码',
        ];
    }
}
```

创建用户修改请求验证类 `app/Http/Requests/Api/V1/UserUpdateRequest.php`：

```php
<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user),
            ],
            'password' => 'required|string|min:6|max:15',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => '昵称',
            'email' => '邮箱',
            'password' => '密码',
        ];
    }
}
```

创建用户控制器 `app/Http/Controllers/Api/V1/UserController.php`：

```php
<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\ApiController as Controller;
use App\Http\Requests\Api\V1\UserStoreRequest;
use App\Http\Requests\Api\V1\UserUpdateRequest;
use App\Models\User;
use Exception;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Dingo\Api\Exception\DeleteResourceFailedException;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api-combined', ['except' => [
            //
        ]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::forList(
            [],
            [
                'users.id',
                'users.name',
                'users.email',
                'users.created_at',
                'users.updated_at',
            ]
        );

        return $this->response->array($users->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\V1\UserStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            // 创建用户
            $data = $request->except(['roles']);
            $data['password'] = bcrypt($data['password']);
            $user = User::create($data);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw new StoreResourceFailedException($e->getMessage());
        }

        return $this->response->created();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::forList(
            $id,
            [
                'users.id',
                'users.name',
                'users.email',
                'users.created_at',
                'users.updated_at',
            ]
        )->first();

        return $this->response->array($user->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Api\V1\UserUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $user = User::find($id);

        DB::beginTransaction();
        try {

            // 更新用户
            $data = $request->input();
            $data['password'] = bcrypt($data['password']);
            $user->update($data);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw new UpdateResourceFailedException($e->getMessage());
        }

        return $this->response->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        DB::beginTransaction();
        try {

            $user->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw new DeleteResourceFailedException($e->getMessage());
        }

        return $this->response->noContent();
    }
}
```

### 引入角色权限控制（RBAC）

1. 安装 `spatie/laravel-permission`

   ```bash
   $ composer require spatie/laravel-permission
   ```

2. 发布数据库迁移文件和配置文件

   ```bash
   $ php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
   ```

3. 修改迁移文件如下：

   ```php
   <?php

   use Illuminate\Support\Facades\Schema;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Database\Migrations\Migration;

   class CreatePermissionTables extends Migration
   {
       /**
        * Run the migrations.
        *
        * @return void
        */
       public function up()
       {
           $tableNames = config('permission.table_names');
           $columnNames = config('permission.column_names');

           Schema::create($tableNames['permissions'], function (Blueprint $table) {
               $table->increments('id');
               $table->string('name');
               $table->string('guard_name');
               $table->string('description')->default('');
               $table->boolean('is_basic')->default(false)->comment('是否基本权限');
               $table->timestamps();
           });

           Schema::create($tableNames['roles'], function (Blueprint $table) {
               $table->increments('id');
               $table->string('name');
               $table->string('guard_name');
               $table->string('description')->default('');
               $table->boolean('is_basic')->default(false)->comment('是否基本角色');
               $table->timestamps();
           });

           Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames) {
               $table->unsignedInteger('permission_id');

               $table->string('model_type');
               $table->unsignedBigInteger($columnNames['model_morph_key']);
               $table->index([$columnNames['model_morph_key'], 'model_type', ], 'model_has_permissions_model_id_model_type_index');

               $table->foreign('permission_id')
                   ->references('id')
                   ->on($tableNames['permissions'])
                   ->onDelete('cascade');

               $table->primary(['permission_id', $columnNames['model_morph_key'], 'model_type'],
                       'model_has_permissions_permission_model_type_primary');
           });

           Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames) {
               $table->unsignedInteger('role_id');

               $table->string('model_type');
               $table->unsignedBigInteger($columnNames['model_morph_key']);
               $table->index([$columnNames['model_morph_key'], 'model_type', ], 'model_has_roles_model_id_model_type_index');

               $table->foreign('role_id')
                   ->references('id')
                   ->on($tableNames['roles'])
                   ->onDelete('cascade');

               $table->primary(['role_id', $columnNames['model_morph_key'], 'model_type'],
                       'model_has_roles_role_model_type_primary');
           });

           Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
               $table->unsignedInteger('permission_id');
               $table->unsignedInteger('role_id');

               $table->foreign('permission_id')
                   ->references('id')
                   ->on($tableNames['permissions'])
                   ->onDelete('cascade');

               $table->foreign('role_id')
                   ->references('id')
                   ->on($tableNames['roles'])
                   ->onDelete('cascade');

               $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
           });

           app('cache')
               ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
               ->forget(config('permission.cache.key'));
       }

       /**
        * Reverse the migrations.
        *
        * @return void
        */
       public function down()
       {
           $tableNames = config('permission.table_names');

           Schema::drop($tableNames['role_has_permissions']);
           Schema::drop($tableNames['model_has_roles']);
           Schema::drop($tableNames['model_has_permissions']);
           Schema::drop($tableNames['roles']);
           Schema::drop($tableNames['permissions']);
       }
   }
   ```

4. 填充权限和角色数据

   ```bash
   $ php artisan make:migration seed_permissions_and_roles_tables
   ```

   修改迁移内容如下：

   ```php
   <?php

   use Illuminate\Support\Facades\DB;
   use Illuminate\Support\Facades\Schema;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Eloquent\Model;
   use App\Models\Permission;
   use App\Models\Role;
   use App\Models\User;

   class SeedPermissionsAndRolesTables extends Migration
   {
       /**
        * Run the migrations.
        *
        * @return void
        */
       public function up()
       {
           // Reset cached roles and permissions
           app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

           // create permissions
           $this->createPermissions();

           // create roles and assign created permissions
           $this->createRoles();

           // assign role to user
           $this->assignRoles();
       }

       /**
        * Reverse the migrations.
        *
        * @return void
        */
       public function down()
       {
           // Reset cached roles and permissions
           app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

           // 清空所有数据表数据
           $tableNames = config('permission.table_names');

           Model::unguard();

           // disable foreign key check for this connection
           DB::statement('SET FOREIGN_KEY_CHECKS=0;');

           DB::table($tableNames['role_has_permissions'])->truncate();
           DB::table($tableNames['model_has_roles'])->truncate();
           DB::table($tableNames['model_has_permissions'])->truncate();
           DB::table($tableNames['roles'])->truncate();
           DB::table($tableNames['permissions'])->truncate();

           DB::statement('SET FOREIGN_KEY_CHECKS=1;');

           Model::reguard();
       }

       /**
        * 创建权限
        */
       protected function createPermissions()
       {
           Permission::create([
               'name' => 'permissions_index',
               'guard_name' => 'api',
               'description' => '查看权限列表',
               'is_basic' => true,
           ]);
           Permission::create([
               'name' => 'permissions_show',
               'guard_name' => 'api',
               'description' => '查看权限详情',
               'is_basic' => true,
           ]);
           Permission::create([
               'name' => 'permissions_store',
               'guard_name' => 'api',
               'description' => '创建权限',
               'is_basic' => true,
           ]);
           Permission::create([
               'name' => 'permissions_update',
               'guard_name' => 'api',
               'description' => '更新权限',
               'is_basic' => true,
           ]);
           Permission::create([
               'name' => 'permissions_destroy',
               'guard_name' => 'api',
               'description' => '删除权限',
               'is_basic' => true,
           ]);

           Permission::create([
               'name' => 'roles_index',
               'guard_name' => 'api',
               'description' => '查看角色列表',
               'is_basic' => true,
           ]);
           Permission::create([
               'name' => 'roles_show',
               'guard_name' => 'api',
               'description' => '查看角色详情',
               'is_basic' => true,
           ]);
           Permission::create([
               'name' => 'roles_store',
               'guard_name' => 'api',
               'description' => '创建角色',
               'is_basic' => true,
           ]);
           Permission::create([
               'name' => 'roles_update',
               'guard_name' => 'api',
               'description' => '更新角色',
               'is_basic' => true,
           ]);
           Permission::create([
               'name' => 'roles_destroy',
               'guard_name' => 'api',
               'description' => '删除角色',
               'is_basic' => true,
           ]);

           Permission::create([
               'name' => 'users_index',
               'guard_name' => 'api',
               'description' => '查看用户列表',
               'is_basic' => true,
           ]);
           Permission::create([
               'name' => 'users_show',
               'guard_name' => 'api',
               'description' => '查看用户详情',
               'is_basic' => true,
           ]);
           Permission::create([
               'name' => 'users_store',
               'guard_name' => 'api',
               'description' => '创建用户',
               'is_basic' => true,
           ]);
           Permission::create([
               'name' => 'users_update',
               'guard_name' => 'api',
               'description' => '更新用户',
               'is_basic' => true,
           ]);
           Permission::create([
               'name' => 'users_destroy',
               'guard_name' => 'api',
               'description' => '删除用户',
               'is_basic' => true,
           ]);
       }

       /**
        * 创建角色并分配权限
        */
       protected function createRoles()
       {
           // 管理员
           $role = Role::create([
               'name' => 'administrator',
               'guard_name' => 'api',
               'description' => '管理员',
               'is_basic' => true,
           ]);
           $role->givePermissionTo(Permission::where('guard_name', 'api')->get());

           // 测试员
           $role = Role::create([
               'name' => 'test',
               'guard_name' => 'api',
               'description' => '测试员',
               'is_basic' => true,
           ]);
           $role->givePermissionTo([
               'users_index',
               'users_show',
           ]);
       }

       /**
        * 为用户分配角色
        *
        */
       protected function assignRoles()
       {
           // 管理员
           $role = Role::find(1);
           $user = User::find(1);
           $user->assignRole($role);
       }
   }
   ```

5. 添加 `Spatie\Permission\Traits\HasRoles` trait 到 `User` 模型，指定 `guard_name` 并且添加用户更新角色的方法：

   ```php
   <?php

   namespace App\Models;

   use Illuminate\Notifications\Notifiable;
   use Illuminate\Foundation\Auth\User as Authenticatable;
   use Laravel\Passport\HasApiTokens;
   use Dngjnh\LaravelUtility\Traits\ModelResourceTrait;
   use Spatie\Permission\Traits\HasRoles;
   use Exception;

   class User extends Authenticatable
   {
       use Notifiable, HasApiTokens, ModelResourceTrait, HasRoles;

       protected $guard_name = 'api';

       /**
        * The attributes that are mass assignable.
        *
        * @var array
        */
       protected $fillable = [
           'name',
           'email',
           'password',
       ];

       /**
        * The attributes that should be hidden for arrays.
        *
        * @var array
        */
       protected $hidden = [
           'password',
           'remember_token',
       ];

       /**
        * 为 Passport 获取用户实例
        * 默认 email 字段
        *
        * @param string $username
        * @return object
        */
       public function findForPassport(string $username)
       {
           $user = $this->where('email', $username)->first();
           if (empty($user)) {
               throw new OAuthServerException('用户不存在', 6, 'invalid_username', 401);
           }

           return $user;
       }

       /**
        * 为 Passport 验证用户是否有效
        *
        * @param string $password
        * @return true
        * @throws \Exception
        */
       public function validateForPassportPasswordGrant($password)
       {
           $hasher = app(\Illuminate\Contracts\Hashing\Hasher::class);
           if (!$hasher->check($password, $this->getAuthPassword())) {
               throw new OAuthServerException('密码不正确', 6, 'incorrect_password', 401);
           }

           // if ($this->id != 1 && $this->user_status_id != 1) {
           //     throw new OAuthServerException('用户状态异常，请联系管理员', 6, 'abnormal_status', 401);
           // }

           return true;
       }

       /**
        * 更新角色
        * 管理员必须包含管理员角色
        *
        * @param array $roles
        * @throws \Exception
        */
       public function syncRolesWithChecking($roles)
       {
           if ($this->id == 1) {
               if (!in_array('administrator', $roles)) {
                   throw new Exception('管理员必须关联管理员角色');
               }
           }
           $this->syncRoles($roles);
       }
   }
   ```

6. 创建 Permission 模型：

   ```php
   <?php

   namespace App\Models;

   use Spatie\Permission\Models\Permission as Model;
   use Dngjnh\LaravelUtility\Traits\ModelResourceTrait;

   class Permission extends Model
   {
       use ModelResourceTrait;

       /**
        * The attributes that should be cast to native types.
        *
        * @var array
        */
       protected $casts = [
           'is_basic' => 'boolean',
       ];
   }
   ```

7. 创建 Permission 模型观察器：

   ```php
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

   ```

8. 创建 Role 模型：

   ```php
   <?php

   namespace App\Models;

   use Spatie\Permission\Models\Role as Model;
   use Dngjnh\LaravelUtility\Traits\ModelResourceTrait;
   use Exception;

   class Role extends Model
   {
       use ModelResourceTrait;

       /**
        * The attributes that should be cast to native types.
        *
        * @var array
        */
       protected $casts = [
           'is_basic' => 'boolean',
       ];

       /**
        * 更新权限
        * 管理员必须拥有所有基本权限
        *
        * @param array $permissions
        * @throws \Exception
        */
       public function syncPermissionsWithChecking($permissions)
       {
           if ($this->name === 'administrator') {
               $basicPermissions = Permission::where('is_basic', true)->pluck('name')->toArray();
               if (!empty(array_diff($basicPermissions, $permissions))) {
                   throw new Exception('管理员角色必须包含所有基本权限');
               }
           }
           $this->syncPermissions($permissions);
       }
   }
   ```

9. 创建 Role 模型观察器：

   ```php
   <?php

   namespace App\Observers;

   use App\Models\Role;
   use Exception;

   class RoleObserver
   {
       /**
        * Listen to the Role retrieved event.
        *
        * @param  \App\Models\Role  $role
        * @return void
        */
       public function retrieved(Role $role)
       {
           //
       }

       /**
        * Listen to the Role creating event.
        *
        * @param  \App\Models\Role  $role
        * @return void
        */
       public function creating(Role $role)
       {
           //
       }

       /**
        * Listen to the Role created event.
        *
        * @param  \App\Models\Role  $role
        * @return void
        */
       public function created(Role $role)
       {
           //
       }

       /**
        * Listen to the Role updating event.
        *
        * @param  \App\Models\Role  $role
        * @return void
        */
       public function updating(Role $role)
       {
           if ($role->is_basic) {
               if ($role->isDirty(['name'])) {
                   throw new Exception('基本角色不支持修改名称');
               }
           }
       }

       /**
        * Listen to the Role updated event.
        *
        * @param  \App\Models\Role  $role
        * @return void
        */
       public function updated(Role $role)
       {
           //
       }

       /**
        * Listen to the Role saving event.
        *
        * @param  \App\Models\Role  $role
        * @return void
        */
       public function saving(Role $role)
       {
           //
       }

       /**
        * Listen to the Role saved event.
        *
        * @param  \App\Models\Role  $role
        * @return void
        */
       public function saved(Role $role)
       {
           app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
       }

       /**
        * Listen to the Role deleting event.
        *
        * @param  \App\Models\Role  $role
        * @return void
        */
       public function deleting(Role $role)
       {
           if ($role->is_basic) {
               throw new Exception('不能删除基本角色');
           }
       }

       /**
        * Listen to the Role deleted event.
        *
        * @param  \App\Models\Role  $role
        * @return void
        */
       public function deleted(Role $role)
       {
           app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
       }

       /**
        * Listen to the Role restoring event.
        *
        * @param  \App\Models\Role  $role
        * @return void
        */
       public function restoring(Role $role)
       {
           //
       }

       /**
        * Listen to the Role restored event.
        *
        * @param  \App\Models\Role  $role
        * @return void
        */
       public function restored(Role $role)
       {
           app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
       }
   }
   ```

10. 注册 `PermissionObserver` 和 `RoleObserver`：

    ```php
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
    ```

11. 修改 `app/Http/Controllers/Api/V1/AuthController.php`，添加查询当前登录者关联的权限和角色的方法：

    ```php
    /**
     * 关联的角色
     *
     * @return \Illuminate\Http\Response
     */
    public function roles()
    {
        $user = Auth::user();
        $roles = $user->roles;

        return $this->response->array($roles->toArray());
    }

    /**
     * 关联的权限
     *
     * @return \Illuminate\Http\Response
     */
    public function permissions()
    {
        $user = Auth::user();
        $permissions = $user->getAllPermissions()->sortBy('name')->values();

        return $this->response->array($permissions->toArray());
    }
    ```

12. 创建策略基类 `app/Policies/Policy.php`：

    ```php
    <?php

    namespace App\Policies;

    use App\Models\User;
    use Illuminate\Auth\Access\HandlesAuthorization;

    class Policy
    {
        use HandlesAuthorization;

        /**
         * Create a new policy instance.
         *
         * @return void
         */
        public function __construct()
        {
            //
        }

        public function before($user, $ability)
        {
            if ($user->hasRole('administrator')) {
                return true;
            }
        }
    }
    ```

13. 创建权限策略、角色策略、用户策略：

    ```php
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
    ```

    ```php
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
    ```

    ```php
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
    ```

14. 在 `app/Providers/AuthServiceProvider.php` 中注册上面创建的策略：

    ```php
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
    ```

15. 更新 `UserStoreRequest`、`UserUpdateRequest`、`UserController`，管理用户时，能对用户的关联的角色进行修改：

    `app/Http/Requests/Api/V1/UserStoreRequest.php`

    ```php
    // ...
    'roles' => 'sometimes|nullable|array',
    'roles.*' => 'required|string|exists:roles,name',
    // ...
    'roles' => '角色名称集合',
    'roles.*' => '角色名称',
    // ...
    ```

    `app/Http/Requests/Api/V1/UserUpdateRequest.php`

    ```php
    // ...
    'roles' => 'sometimes|nullable|array',
    'roles.*' => 'required|string|exists:roles,name',
    // ...
    'roles' => '角色名称集合',
    'roles.*' => '角色名称',
    // ...
    ```

    `app/Http/Controllers/Api/V1/UserController.php`，同时更新检查权限：

    ```php
    <?php

    namespace App\Http\Controllers\Api\V1;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use App\Http\Controllers\Api\ApiController as Controller;
    use App\Http\Requests\Api\V1\UserStoreRequest;
    use App\Http\Requests\Api\V1\UserUpdateRequest;
    use App\Models\User;
    use Exception;
    use Dingo\Api\Exception\StoreResourceFailedException;
    use Dingo\Api\Exception\UpdateResourceFailedException;
    use Dingo\Api\Exception\DeleteResourceFailedException;

    class UserController extends Controller
    {
        public function __construct()
        {
            $this->middleware('auth:api-combined', ['except' => [
                //
            ]]);
        }

        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            $this->authorize('index', User::class);

            $users = User::forList(
                [],
                [
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.created_at',
                    'users.updated_at',
                ]
            );

            return $this->response->array($users->toArray());
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param  \App\Http\Requests\Api\V1\UserStoreRequest  $request
         * @return \Illuminate\Http\Response
         */
        public function store(UserStoreRequest $request)
        {
            $this->authorize('store', User::class);

            DB::beginTransaction();
            try {

                // 创建用户
                $data = $request->except(['roles']);
                $data['password'] = bcrypt($data['password']);
                $user = User::create($data);

                // 更新角色
                $user->syncRolesWithChecking($request->input('roles'));

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                throw new StoreResourceFailedException($e->getMessage());
            }

            return $this->response->created();
        }

        /**
         * Display the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function show($id)
        {
            $user = User::forList(
                $id,
                [
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.created_at',
                    'users.updated_at',
                ],
                [],
                [
                    'roles',
                ]
            )->first();

            $this->authorize('show', $user);

            return $this->response->array($user->toArray());
        }

        /**
         * Update the specified resource in storage.
         *
         * @param  \App\Http\Requests\Api\V1\UserUpdateRequest  $request
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function update(UserUpdateRequest $request, $id)
        {
            $user = User::find($id);

            $this->authorize('update', $user);

            DB::beginTransaction();
            try {

                // 更新用户
                $data = $request->input();
                $data['password'] = bcrypt($data['password']);
                $user->update($data);

                // 更新角色
                $user->syncRolesWithChecking($request->input('roles'));

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                throw new UpdateResourceFailedException($e->getMessage());
            }

            return $this->response->noContent();
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function destroy($id)
        {
            $user = User::find($id);

            $this->authorize('destroy', $user);

            DB::beginTransaction();
            try {

                $user->delete();

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                throw new DeleteResourceFailedException($e->getMessage());
            }

            return $this->response->noContent();
        }
    }
    ```

16. 创建 `app/Http/Requests/Api/V1/PermissionUpdateRequest.php`、`app/Http/Requests/Api/V1/RoleStoreRequest.php`、`app/Http/Requests/Api/V1/RoleUpdateRequest.php`。

17. 创建 `app/Http/Controllers/Api/V1/PermissionController.php`、`app/Http/Controllers/Api/V1/RoleController.php`：

18. 补充路由，更新 `routes/api.php` 内容如下：

    ```php
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
    ```
