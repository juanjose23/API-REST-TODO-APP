<?php

namespace Src\auth\user\domain\Contracts;

use Src\auth\user\domain\Entities\User;
use Src\auth\user\domain\ValueObjects\UserEmail;
use Src\shared\pagination\Page;
use Src\shared\pagination\PaginatedResult;

interface UserRepositoryInterface
{
    public function getAllUsers(Page $page): PaginatedResult;
    //public function availableUsers($teamId);
    public function register(User $user);
    public function existsByEmail(UserEmail $email): bool;

}
