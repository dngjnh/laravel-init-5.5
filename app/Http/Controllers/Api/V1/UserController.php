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

            $data = $request->input();
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
