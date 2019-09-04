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
