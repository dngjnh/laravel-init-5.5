<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\PermissionUpdateRequest;
use App\Models\Permission;
use Exception;
use Dingo\Api\Exception\UpdateResourceFailedException;

class PermissionController extends ApiController
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
        $this->authorize('index', Permission::class);

        $permissions = Permission::forList();

        return $this->response->array($permissions->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = Permission::forList($id)->first();

        $this->authorize('show', $permission);

        return $this->response->array($permission->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Api\V1\PermissionUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionUpdateRequest $request, $id)
    {
        $permission = Permission::find($id);

        $this->authorize('update', $permission);

        DB::beginTransaction();
        try {

            $permission->update($request->only(['description']));

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
        //
    }
}
