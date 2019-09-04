<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\RoleStoreRequest;
use App\Http\Requests\Api\V1\RoleUpdateRequest;
use App\Models\Role;
use Exception;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Dingo\Api\Exception\DeleteResourceFailedException;

class RoleController extends ApiController
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
        $this->authorize('index', Role::class);

        $roles = Role::forList();

        return $this->response->array($roles->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\V1\RoleStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleStoreRequest $request)
    {
        $this->authorize('store', Role::class);

        DB::beginTransaction();
        try {

            // 创建角色
            $data = $request->only([
                'name',
                'description',
            ]);
            $data['guard_name'] = 'api';
            $role = Role::create($data);

            // 更新权限
            $role->syncPermissionsWithChecking($request->input('permissions'));

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
        $role = Role::forList(
            $id,
            ['*'],
            [],
            [
                'permissions',
            ]
        )->first();

        $this->authorize('show', $role);

        return $this->response->array($role->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Api\V1\RoleUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, $id)
    {
        $role = Role::find($id);

        $this->authorize('update', $role);

        DB::beginTransaction();
        try {

            // 更新角色
            $role->update($request->only([
                'name',
                'description',
            ]));

            // 更新权限
            $role->syncPermissionsWithChecking($request->input('permissions'));

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
        $role = Role::find($id);

        $this->authorize('destroy', $role);

        DB::beginTransaction();
        try {

            $role->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw new DeleteResourceFailedException($e->getMessage());
        }

        return $this->response->noContent();
    }
}
