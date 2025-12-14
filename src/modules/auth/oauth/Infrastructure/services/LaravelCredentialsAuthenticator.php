<?php

namespace Src\modules\auth\oauth\Infrastructure\services;

use DomainException;
use Illuminate\Support\Facades\Auth;
use Src\modules\auth\oauth\Domain\Contracts\Auth\CredentialsAuthenticatorInterface;
use Src\modules\auth\user\Domain\Contracts\UserRepositoryInterface;
use Src\modules\auth\user\Domain\Entities\User;

readonly class LaravelCredentialsAuthenticator implements CredentialsAuthenticatorInterface
{
    public function __construct(private UserRepositoryInterface $users)
    {

    }

    public function attempt(string $email, string $password): ?User
    {
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return null;
        }

        $userId = auth()->id()
            ?? throw new DomainException('No authenticated user or ID found.');

        $user = $this->users->findById($userId)
            ?? throw new DomainException('User entity not found after successful authentication.');

        if ($user->emailVerifiedAt() === null) {
            Auth::logout();
            throw new DomainException('Email not verified.');
        }

        return $user;
    }
}
