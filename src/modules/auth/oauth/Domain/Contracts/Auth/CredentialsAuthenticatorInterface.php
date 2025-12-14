<?php

namespace Src\modules\auth\oauth\Domain\Contracts\Auth;

use Src\modules\auth\user\Domain\entities\User;

interface CredentialsAuthenticatorInterface
{
    public function attempt(string $email, string $password): ?User;
}
