<?php

namespace Src\modules\auth\user\Domain\Contracts;

use Src\modules\auth\user\Domain\entities\User;
use Src\modules\auth\user\Domain\ValuesObjects\UserEmail;
use Src\shared\pagination\Page;
use Src\shared\pagination\PaginatedResult;

interface UserRepositoryInterface
{
    public function getAllUsers(Page $page): PaginatedResult;
    public function register(User $user);
    public function existsByEmail(UserEmail $email): bool;
    public function update(User $user): void;
    public function findById(int $id): ?User;
    public function findByToken(string $token): ?User;
    //public function findByProvider(string $provider, string $providerId): ?User;
    public function findByEmail(UserEmail $email): ?User;
}
