<?php

namespace App\Policies\V1;

use App\Models\Group;
use App\Models\User;
use App\Permissions\V1\Abilities;

class GroupPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function showAll(User $user) {
        if($user->tokenCan(Abilities::ShowAllGroups)){
            return true;
        }

        return false;
    }

    public function show(User $user, Group $group) {

        if($user->tokenCan(Abilities::ShowGroup)) {
            return true;
        } else if($user->tokenCan(Abilities::ShowOwnGroup)) {
            $member_ids = $group->members()->pluck('member_id')->toArray();

            return in_array($user->id, $member_ids);
        }

        return false;
    }

    public function delete(User $user, Group $group) {
        if($user->tokenCan(Abilities::DeleteGroup)) {
            return true;
        } else if($user->tokenCan(Abilities::DeleteOwnGroup)) {
            return $user->id === $group->owner_id;
        }

        return false;
    }

    public function store(User $user) {
        return $user->tokenCan(Abilities::CreateGroup) || $user->tokenCan(Abilities::CreateOwnGroup);
    }

    public function update(User $user, Group $group) {
        if($user->tokenCan(Abilities::UpdateGroup)) {
            return true;
        } else if($user->tokenCan(Abilities::UpdateOwnGroup)) {
            return $user->id === $group->owner_id;
        }

        return false;
    }
}
