<?php

namespace App\Permissions\V1;

use App\Models\User;

final class Abilities {

    public const ShowAll = 'group:all:show';
    public const ShowGroup = 'group:show';
    public const CreateGroup = 'group:create';
    public const UpdateGroup = 'group:update';
    public const DeleteGroup = 'group:delete';

    public const CreateUser = 'user:create';
    public const UpdateUser = 'user:update';
    public const DeleteUser = 'user:delete';

    public const ShowOwnGroup = 'group:own:show';
    public const CreateOwnGroup = 'group:own:create';
    public const UpdateOwnGroup = 'group:own:update';
    public const DeleteOwnGroup = 'group:own:delete';


    public static function getAbilities(User $user) {

        if($user->is_admin) {
            return [
                self::ShowAll,
                self::ShowGroup,
                self::CreateGroup,
                self::UpdateGroup,
                self::DeleteGroup,
                self::CreateUser,
                self::UpdateUser,
                self::DeleteUser,
            ];
        } else {
            return [
                self::ShowOwnGroup,
                self::CreateOwnGroup,
                self::UpdateOwnGroup,
                self::DeleteOwnGroup
            ];
        }
    }
}
