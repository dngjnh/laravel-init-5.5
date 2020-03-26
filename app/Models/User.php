<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Dngjnh\LaravelUtility\Traits\ModelResourceTrait;
use Spatie\Permission\Traits\HasRoles;
use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;

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
