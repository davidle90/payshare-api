<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\GroupFilter;
use App\Models\Group;
use App\Http\Requests\Api\V1\StoreGroupRequest;
use App\Http\Requests\Api\V1\UpdateGroupRequest;
use App\Http\Resources\V1\GroupResource;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GroupFilter $filters)
    {
        return GroupResource::collection(Group::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        //
    }
}
