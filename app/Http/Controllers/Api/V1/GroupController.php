<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\helpers;
use App\Http\Filters\V1\GroupFilter;
use App\Models\Group;
use App\Http\Requests\Api\V1\StoreGroupRequest;
use App\Http\Requests\Api\V1\UpdateGroupRequest;
use App\Http\Resources\V1\GroupResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                $attributes = $request->mappedAttributes();
                $attributes['owner_id'] = Auth::user()->id;
                $group = Group::create($attributes);
                $group->reference_id = helpers::generate_reference_id(3, $group->name, $group->id);
                $group->save();
                $group->members()->attach($group->owner_id);
                return $group;
            });

            return new GroupResource($group);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($reference_id)
    {
        $group = Group::where('reference_id', $reference_id)->first();

        if(!$group){
            return $this->error('Group not found', 404);
        }

        if(Gate::authorize('member-group', $group)) {

            if($this->include('members')) {
                $group->load('members');
            }

            if($this->include('payments')) {
                $group->load('payments');
            }

            if($this->include('debts')) {
                $group->load('debts');
            }

            return new GroupResource($group);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, $reference_id)
    {
        $group = Group::where('reference_id', $reference_id)->first();

        if(!$group){
            return $this->error('Group not found', 404);
        }

        if(Gate::authorize('member-group', $group)) {

            $group->update($request->mappedAttributes());
            helpers::calculate_balance($group);
            helpers::update_total_expenses($group);

            return new GroupResource($group);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($reference_id)
    {

        $group = Group::where('reference_id', $reference_id)->first();

        if(!$group){
            return $this->error('Group not found', 404);
        }

        if(Gate::authorize('member-group', $group)) {

            foreach($group->payments as $payment){
                $payment->contributors()->delete();
                $payment->participants()->delete();
                $payment->delete();
            }

            $group->members()->detach();
            $group->debts()->delete();
            $group->delete();

            return $this->ok('Group successfully deleted.');
        }
    }
}
