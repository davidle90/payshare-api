<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\GroupFilter;
use App\Models\Group;
use App\Http\Requests\Api\V1\StoreGroupRequest;
use App\Http\Requests\Api\V1\UpdateGroupRequest;
use App\Http\Resources\V1\GroupResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

class GroupController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(GroupFilter $filters)
    {
        if(Gate::authorize('show-group')){
            return GroupResource::collection(Group::filter($filters)->paginate());
        }

        return $this->notAuthorized('You are not authorized to show this resource.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        if(Gate::authorize('store-group')){
            return new GroupResource(Group::create($request->mappedAttributes()));
        }

        return $this->notAuthorized('You are not authorized to create this resource.');
    }

    /**
     * Display the specified resource.
     */
    public function show($group_id)
    {
        try {
            $group = Group::findOrFail($group_id);

            if($this->include('users')) {
                return new GroupResource($group->load('users'));
            }

            return new GroupResource($group);

        } catch (ModelNotFoundException $exception) {
            return $this->error('Group not found', 404);
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

        return $this->notAuthorized('You are not authorized to update this resource.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        if(Gate::authorize('delete-group', $group)) {
            $group->delete();

            return $this->ok('Group successfully deleted.');
        }

        return $this->notAuthorized('You are not authorized to delete this resource.');
    }
}
