<?php

namespace Src\modules\auth\oauth\Application\Handlers\Auth;

use DomainException;
use Src\modules\auth\oauth\Domain\Contracts\Auth\AuthTokenGeneratorInterface;
use Src\modules\auth\oauth\Domain\Contracts\Auth\JwtManagerInterface;
use Throwable;

readonly class LogoutUserHandler
{
    public function __construct(
        private AuthTokenGeneratorInterface $tokenGenerator,
        private JwtManagerInterface $jwtManager
    )
    {
    }

    public function __invoke(): void
    {
        try {
            $jti = $this->jwtManager->getCurrentTokenJti();
            if (!$jti) {
                throw new DomainException("Token JTI is missing.");
            }
            $revoked = $this->tokenGenerator->revokeTokenByJti($jti);
            if (!$revoked) {
                throw new DomainException("Token could not be revoked from repository.");
            }
            $this->jwtManager->invalidateCurrentToken();

        } catch (Throwable $e) {
            throw new DomainException('Logout failed: ' . $e->getMessage());
        }
    }
}
