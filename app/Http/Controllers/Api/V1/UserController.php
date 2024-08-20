<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\helpers;
use App\Http\Filters\V1\UserFilter;
use App\Models\User;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(UserFilter $filters)
    {
        return UserResource::collection(User::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        if(Gate::authorize('store-user')){
            $user = User::create($request->mappedAttributes());
            $user->reference_id = helpers::generate_reference_id(3, $user->name, $user->id);
            $user->save();
            return new UserResource($user);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if($this->include('groups')) {
            $user->load('groups');
        }
        if($this->include('contributions')) {
            $user->load('contributions');
        }
        if($this->include('participations')) {
            $user->load('participations');
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if(Gate::authorize('update-user', $user)) {

            $user->update($request->mappedAttributes());

            return new UserResource($user);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if(Gate::authorize('delete-user', $user)) {

            $user->delete();

            return $this->ok('User successfully deleted.');
        }
    }
}
