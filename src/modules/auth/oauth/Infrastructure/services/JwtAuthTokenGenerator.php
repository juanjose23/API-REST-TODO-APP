<?php

namespace Src\modules\auth\oauth\Infrastructure\services;

use DateTimeImmutable;
use DomainException;
use Random\RandomException;
use Src\modules\auth\oauth\Domain\Contracts\Auth\AuthTokenGeneratorInterface;
use Src\modules\auth\oauth\Domain\Contracts\Jwt\JwtTokenRepositoryInterface;
use Src\modules\auth\oauth\Domain\Entities\JwtToken;
use Src\modules\auth\user\Domain\Contracts\UserRepositoryInterface;
use Src\modules\auth\user\Domain\Entities\User;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

readonly class JwtAuthTokenGenerator implements AuthTokenGeneratorInterface
{
    public function __construct(private JwtTokenRepositoryInterface $repo)
    {
    }

    /**
     * @throws RandomException
     */
    public function generateTokens(User $user, ?string $fingerprint = null): array
    {
        $issuedAt = new DateTimeImmutable();
        $expiresAt = $issuedAt->modify('+1 hour');
        $jti = bin2hex(random_bytes(16));

        $accessToken = JWTAuth::claims([
            'jti' => $jti,
            'fp' => $fingerprint
        ])->fromUser($user);

        $refreshJti = bin2hex(random_bytes(16));
        $refreshToken = JWTAuth::claims([
            'jti' => $refreshJti,
            'fp' => $fingerprint
        ])->fromUser($user);

        $this->repo->store(new JwtToken($jti, $user->id, $issuedAt, $expiresAt));
        $this->repo->store(new JwtToken($refreshJti, $user->id, $issuedAt, $issuedAt->modify('+1 days')));

        return [
            'access_token' => (string) $accessToken,
            'refresh_token' => (string) $refreshToken,
            'expires_in' => $expiresAt->getTimestamp() - $issuedAt->getTimestamp(),
        ];
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        try {
            $payload = JWTAuth::setToken($refreshToken)->getPayload();
            $jti = $payload->get('jti');
            $userId = $payload->get('sub');

            $tokenEntity = $this->repo->findByJti($jti);
            if (!$tokenEntity || $tokenEntity->revokedAt !== null) {
                throw new DomainException('Refresh token revoked or invalid.');
            }

            $this->repo->revokeByJti($jti);

            $user = app(UserRepositoryInterface::class)->findById($userId);
            if (!$user) {
                throw new DomainException('User not found for refresh token.');
            }

            return $this->generateTokens($user);
        } catch (Throwable $e) {
            throw new DomainException('Could not refresh token: ' . $e->getMessage(), 0, $e);
        }
    }

    public function revokeTokenByJti(string $jti): bool
    {
        return $this->repo->revokeByJti($jti);
    }

    public function revokeAllUserTokens(int $userId): int
    {
        return $this->repo->revokeAllForUser($userId);
    }
}
