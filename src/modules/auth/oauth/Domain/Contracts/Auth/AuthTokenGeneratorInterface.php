<?php

namespace Src\modules\auth\oauth\Domain\Contracts\Auth;

use Src\modules\auth\user\Domain\Entities\User;

interface AuthTokenGeneratorInterface
{
    public function generateTokens(User $user, ?string $fingerprint = null): array;
    public function refreshAccessToken(string $refreshToken): array;
    public function revokeTokenByJti(string $jti): bool;
    public function revokeAllUserTokens(int $userId): int;
}
