<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\helpers;
use App\Http\Filters\V1\UserFilter;
use App\Models\User;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
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
    public function show($reference_id)
    {
        $user = User::where('reference_id', $reference_id)->first();

        if(!$user){
            return $this->error('User not found.', 404);
        }

        if($this->include('groups')) {
            $user->load('groups');
        }
        if($this->include('contributions')) {
            $user->load('contributions');
        }
        if($this->include('participations')) {
            $user->load('participations');
        }
        if($this->include('friends')) {
            $user->load('friends');
        }
        if($this->include('receivedFriendRequests')) {
            $user->load('receivedFriendRequests');
        }
        if($this->include('sentFriendRequests')) {
            $user->load('sentFriendRequests');
        }
        if($this->include('debtsIOwe')) {
            $user->load('debtsIOwe');
        }
        if($this->include('debtsOwedToMe')) {
            $user->load('debtsOwedToMe');
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

    // public function add_friends(Request $request, User $user)
    // {
    //     $friend_reference_ids = $request->get('friend_reference_ids', []);
    //     $friends = User::whereIn('reference_id', $friend_reference_ids)->get();

    //     if(isset($friends) || empty($friends)) {
    //         return $this->error('Friend(s) not found.', 404);
    //     }

    //     $friend_names = $friends->pluck('name')->toArray();
    //     $friend_ids = $friends->pluck('id')->toArray();

    //     $user->friends()->syncWithoutDetaching($friend_ids);

    //     return $this->ok('Friend(s) added.', $friend_names);
    // }

    // public function remove_friends(Request $request, User $user)
    // {
    //     $friend_reference_ids = $request->get('friend_reference_ids', []);
    //     $friends = User::whereIn('reference_id', $friend_reference_ids)->get();

    //     if(isset($friends) || empty($friends)) {
    //         return $this->error('Friend(s) not found.', 404);
    //     }

    //     $friend_names = $friends->pluck('name')->toArray();
    //     $friend_ids = $friends->pluck('id')->toArray();

    //     $user->friends()->detach($friend_ids);

    //     return $this->ok('Friend(s) removed.', $friend_names);
    // }
}
