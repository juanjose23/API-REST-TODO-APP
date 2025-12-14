<?php

namespace Src\modules\auth\oauth\Application\Handlers\Auth;

use DomainException;
use RuntimeException;
use Src\modules\auth\oauth\Application\Dtos\OAuthLoginDto;
use Src\modules\auth\oauth\Domain\Contracts\Auth\AuthTokenGeneratorInterface;
use Throwable;

readonly class RefreshAccessTokenHandler
{
    public function __construct(
        private AuthTokenGeneratorInterface $tokenGenerator
    ) {}

    public function __invoke(string $refreshToken): OAuthLoginDto
    {
        try {
            $tokens = $this->tokenGenerator->refreshAccessToken($refreshToken);
            return new OAuthLoginDto(
                accessToken: $tokens['access_token'],
                refreshToken: $tokens['refresh_token'],
                expiresIn: $tokens['expires_in'],
                user: $tokens['user']
            );
        } catch (DomainException $e) {
            throw new DomainException('Invalid or expired refresh token.', 0, $e);
        } catch (Throwable $e) {
            throw new RuntimeException('Could not refresh token.', 0, $e);
        }
    }
}
