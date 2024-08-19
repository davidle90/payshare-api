<?php

namespace App\Policies\V1;

use App\Models\Group;
use App\Models\Payment;
use App\Models\User;
use App\Permissions\V1\Abilities;

class PaymentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct(){}

    public function showAll(User $user)
    {
        if($user->tokenCan(Abilities::ShowAllPayments)){
            return true;
        }

        return false;
    }

    public function show(User $user, Group $group)
    {
        if($user->tokenCan(Abilities::ShowPayment)){
            return true;
        } else if ($user->tokenCan(Abilities::ShowOwnPayment)){
            return $group->members()->where('member_id', $user->id)->exists();
        }

        return false;
    }

    public function store(User $user, Group $group)
    {
        if($user->tokenCan(Abilities::CreatePayment)) {
            return true;
        } else if($user->tokenCan(Abilities::CreateOwnPayment)) {
            return $group->members()->where('member_id', $user->id)->exists();
        }

        return false;
    }

    public function update(User $user, Group $group)
    {
        if($user->tokenCan(Abilities::UpdatePayment)) {
            return true;
        } else if($user->tokenCan(Abilities::UpdateOwnPayment)) {
            return $group->members()->where('member_id', $user->id)->exists();
        }

        return false;
    }

    public function delete(User $user, Group $group)
    {
        if($user->tokenCan(Abilities::DeletePayment)) {
            return true;
        } else if($user->tokenCan(Abilities::DeleteOwnPayment)) {
            return $group->members()->where('member_id', $user->id)->exists();
        }

        return false;
    }

    public function is_payment_group(User $user, Group $group, Payment $payment)
    {
        if($user->is_admin) {
            return true;
        } else if($user->tokenCan(Abilities::UpdateOwnPayment)) {
            if($group->members()->where('member_id', $user->id)->exists() && ($payment->group_id == $group->id))

            return true;
        }

        return false;
    }
}
