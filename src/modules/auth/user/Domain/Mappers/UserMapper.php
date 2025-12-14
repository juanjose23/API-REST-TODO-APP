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
            'email_verified_at' => $user->emailVerifiedAt()?->value()?->format('Y-m-d H:i:s'),
            'is_active' => $user->isActive(),
        ];
    }
}
