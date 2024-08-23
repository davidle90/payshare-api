<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class GroupMemberController extends ApiController
{
    public function add_members(Request $request, $group_reference_id)
    {
        $group = Group::where('reference_id', $group_reference_id)->first();

        if(!$group){
            return $this->error('Group not found', 404);
        }

        if(Gate::authorize('member-group', $group)){
            $member_ids = $request->input('data.attributes.member_ids');
            $group->members()->syncWithoutDetaching($member_ids);

            return $this->ok('Members added', $member_ids);
        }
    }

    public function remove_members(Request $request, $group_reference_id)
    {
        $group = Group::where('reference_id', $group_reference_id)->first();

        if(!$group){
            return $this->error('Group not found', 404);
        }

        if(Gate::authorize('member-group', $group)){
            $member_ids = $request->input('data.attributes.member_ids');
            $group->members()->detach($member_ids);

            return $this->ok('Members removed', $member_ids);
        }
    }

    public function join_group(Request $request, $user_reference_id)
    {
        $group_reference_id = $request->get('group_reference_id');
        $group = Group::where('reference_id', $group_reference_id)->first();

        if(!$group){
            return $this->error('Group not found.', 404);
        }

        $user = User::where('reference_id', $user_reference_id)->first();

        if(!$user){
            return $this->error('User not found.', 404);
        }

        // add password?

        $group_member_ids = $group->members->pluck('id')->toArray();

        if(in_array($user->id, $group_member_ids)){
            return $this->ok('User is already a member of the group.');
        }

        $group->members()->syncWithoutDetaching($user->id);

        return $this->ok('Group joined.', ['group' => $group->name]);
    }
}
