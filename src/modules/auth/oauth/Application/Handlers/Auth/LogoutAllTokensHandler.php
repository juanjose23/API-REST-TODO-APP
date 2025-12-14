<?php

namespace Src\modules\auth\oauth\Application\Handlers\Auth;

use Src\modules\auth\oauth\Domain\Contracts\Auth\AuthTokenGeneratorInterface;

readonly class LogoutAllTokensHandler
{
    public function __construct(private AuthTokenGeneratorInterface $tokenGenerator)
    {
    }

    public function __invoke(int $userId): int
    {
        return $this->tokenGenerator->revokeAllUserTokens($userId);
    }
}
