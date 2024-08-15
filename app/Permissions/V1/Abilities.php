<?php

namespace App\Permissions\V1;

use App\Models\User;

final class Abilities {

    // GROUP
    public const ShowAllGroups = 'group:all:show';
    public const ShowGroup = 'group:show';
    public const CreateGroup = 'group:create';
    public const UpdateGroup = 'group:update';
    public const DeleteGroup = 'group:delete';

    public const ShowOwnGroup = 'group:own:show';
    public const CreateOwnGroup = 'group:own:create';
    public const UpdateOwnGroup = 'group:own:update';
    public const DeleteOwnGroup = 'group:own:delete';

    // USER
    public const CreateUser = 'user:create';
    public const UpdateUser = 'user:update';
    public const DeleteUser = 'user:delete';

    // PAYMENT
    public const ShowAllPayments = 'payments:all:show';
    public const ShowPayment = 'payment:show';
    public const CreatePayment = 'payment:create';
    public const UpdatePayment = 'payment:update';
    public const DeletePayment = 'payment:delete';

    public const ShowOwnPayment = 'payments:own:show';
    public const CreateOwnPayment = 'payments:own:create';
    public const UpdateOwnPayment = 'payments:own:update';
    public const DeleteOwnPayment = 'payments:own:delete';

    public static function getAbilities(User $user) {

        if($user->is_admin) {
            return [
                self::ShowAllGroups,
                self::ShowGroup,
                self::CreateGroup,
                self::UpdateGroup,
                self::DeleteGroup,
                self::CreateUser,
                self::UpdateUser,
                self::DeleteUser,
                self::ShowAllPayments,
                self::ShowPayment,
                self::CreatePayment,
                self::UpdatePayment,
                self::DeletePayment
            ];
        } else {
            return [
                self::ShowOwnGroup,
                self::CreateOwnGroup,
                self::UpdateOwnGroup,
                self::DeleteOwnGroup,
                self::ShowOwnPayment,
                self::CreateOwnPayment,
                self::UpdateOwnPayment,
                self::DeleteOwnPayment
            ];
        }
    }
}
