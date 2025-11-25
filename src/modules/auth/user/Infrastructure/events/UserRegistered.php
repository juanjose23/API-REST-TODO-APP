<?php
namespace Src\modules\auth\user\Infrastructure\events;

use Src\modules\auth\user\Domain\entities\User;

readonly class UserRegistered
{
    public function __construct(public User $user) {}
}
