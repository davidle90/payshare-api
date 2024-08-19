<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\helpers;
use App\Http\Filters\V1\GroupFilter;
use App\Models\Group;
use App\Http\Requests\Api\V1\StoreGroupRequest;
use App\Http\Requests\Api\V1\UpdateGroupRequest;
use App\Http\Resources\V1\GroupResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class GroupController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(GroupFilter $filters)
    {
        if(Gate::authorize('show-all-groups')){
            return GroupResource::collection(Group::filter($filters)->paginate());
        }
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
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        if(Gate::authorize('show-group', $group)) {

            if($this->include('members')) {
                $group->load('members');
            }

            if($this->include('payments')) {
                $group->load('payments');
            }

            return new GroupResource($group);
        }
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
    }

    public function add_members(Request $request, Group $group)
    {
        if(Gate::authorize('member-group', $group)){
            $member_ids = $request->input('data.attributes.member_ids');
            $group->members()->syncWithoutDetaching($member_ids);

            return $this->ok('Members added');
        }
    }

    public function remove_members(Request $request, Group $group)
    {
        if(Gate::authorize('member-group', $group)){
            $member_ids = $request->input('data.attributes.member_ids');
            $group->members()->detach($member_ids);

            return $this->ok('Members removed');
        }
    }

    public function test(Group $group)
    {

        return helpers::calculate_balance($group);
    }
}
