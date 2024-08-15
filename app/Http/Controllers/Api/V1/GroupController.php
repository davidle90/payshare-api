<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\GroupFilter;
use App\Models\Group;
use App\Http\Requests\Api\V1\StoreGroupRequest;
use App\Http\Requests\Api\V1\UpdateGroupRequest;
use App\Http\Resources\V1\GroupResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class GroupController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(GroupFilter $filters, Request $request)
    {
        $user_id = $request->user()->id;

        $groups = Group::whereHas('members', function ($query) use ($user_id) {
            $query->where('member_id', $user_id);
        })->filter($filters)->paginate();

        return GroupResource::collection($groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        if(Gate::authorize('store-group')){

            $group = DB::transaction(function() use ($request) {
                $group = Group::create($request->mappedAttributes());
                $group->members()->attach($group->owner_id);
                return $group;
            });

            return new GroupResource($group);
        }

        return $this->notAuthorized('You are not authorized to create this resource.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $group_id)
    {
        $user_id = $request->user()->id;
        $is_admin = $request->user()->is_admin;
        $group = Group::findOrFail($group_id);
        $member_ids = $group->members()->pluck('member_id')->toarray();

        if(!in_array($user_id, $member_ids) && !$is_admin){
            return $this->notAuthorized('You are not authorized show this resource.');
        }

        if($this->include('members')) {
            $group->load('members');
        }
        if($this->include('payments')) {
            $group->load('payments');
        }

        return new GroupResource($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        if(Gate::authorize('update-group', $group)) {

            $group->update($request->mappedAttributes());

            return new GroupResource($group);
        }

        return $this->notAuthorized('You are not authorized to update this resource.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        if(Gate::authorize('delete-group', $group)) {

            foreach($group->payments as $payment){
                $payment->contributors()->delete();
                $payment->participants()->detach();
                $payment->delete();
            }

            $group->members()->detach();
            $group->delete();

            return $this->ok('Group successfully deleted.');
        }

        return $this->notAuthorized('You are not authorized to delete this resource.');
    }
}
