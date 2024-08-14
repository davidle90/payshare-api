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

    public function show(User $user) {
        return $user->tokenCan(Abilities::ShowGroup) || $user->tokenCan(Abilities::ShowOwnGroup);
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
