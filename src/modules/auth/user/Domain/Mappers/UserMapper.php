<?php

namespace Src\modules\auth\user\Domain\Mappers;

use Src\modules\auth\user\Domain\entities\User;

class UserMapper
{

    public static function toArray(User $user): array
    {
        return [
            'id' => $user->id(),
            'name' => $user->name()->value(),
            'email' => $user->email()->value(),
            'avatar' => $user->avatar(),
        ];
    }
}
