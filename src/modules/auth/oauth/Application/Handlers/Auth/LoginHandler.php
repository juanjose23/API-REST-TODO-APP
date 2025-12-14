<?php

namespace Src\modules\auth\oauth\Application\Handlers\Auth;

use DomainException;
use Src\modules\auth\oauth\Application\Commands\Auth\LoginCommand;
use Src\modules\auth\oauth\Application\Dtos\OAuthLoginDto;
use Src\modules\auth\oauth\Domain\Contracts\Auth\AuthTokenGeneratorInterface;
use Src\modules\auth\oauth\Domain\Contracts\Auth\CredentialsAuthenticatorInterface;
use Src\modules\auth\user\Domain\Mappers\UserMapper;

readonly  class LoginHandler
{
    public function __construct(
        private AuthTokenGeneratorInterface $tokenGenerator,
        private CredentialsAuthenticatorInterface $authenticator
    ) {}

    public function __invoke(LoginCommand $command): OAuthLoginDto
    {
        $user = $this->authenticator->attempt(
            $command->email,
            $command->password
        );
        if (!$user) {
            throw new DomainException('Invalid credentials.');
        }
        $tokens = $this->tokenGenerator->generateTokens($user, $command->fingerprint);
        return new OAuthLoginDto(
            accessToken: $tokens['access_token'],
            refreshToken: $tokens['refresh_token'],
            expiresIn: $tokens['expires_in'],
            user: UserMapper::toArray($user)
        );
    }
}
