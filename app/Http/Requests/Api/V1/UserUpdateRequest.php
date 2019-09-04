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
