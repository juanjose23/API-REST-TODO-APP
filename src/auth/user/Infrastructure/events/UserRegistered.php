<?php
namespace Src\auth\user\infrastructure\Events;

use Src\Auth\User\Domain\Entities\User;

readonly class UserRegistered
{
    public function __construct(public User $user) {}
}
