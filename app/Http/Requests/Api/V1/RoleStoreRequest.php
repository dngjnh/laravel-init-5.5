<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\ApiRequest;

class RoleStoreRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'required|string|max:255',
            'permissions' => 'sometimes|nullable|array',
            'permissions.*' => 'required|string|exists:permissions,name',
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
        return [
            'name' => '名称',
            'description' => '描述',
            'permissions' => '权限集合',
            'permissions.*' => '权限名称',
        ];
    }
}
