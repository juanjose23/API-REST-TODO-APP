<?php

namespace Src\modules\auth\oauth\Application\Handlers\Oauth;
use Src\modules\auth\oauth\Application\Commands\Auth\ProviderLoginCommand;
use Src\modules\auth\oauth\Application\Dtos\OAuthLoginDto;
use Src\modules\auth\oauth\Domain\Contracts\Auth\AuthTokenGeneratorInterface;
use Src\modules\auth\oauth\Domain\Contracts\OAuth\OAuthUserProcessorInterface;
use Src\modules\auth\user\Domain\Mappers\UserMapper;

readonly class OAuthLoginHandler
{
    public function __construct(
        private OAuthUserProcessorInterface $oauthUserProcessor,
        private AuthTokenGeneratorInterface $tokenGenerator
    ) {}

    public function __invoke(ProviderLoginCommand $command): OAuthLoginDto
    {
        $user = $this->oauthUserProcessor->findOrCreateUser(
            $command->providerName,
            $command->socialUser
        );

        $tokens = $this->tokenGenerator->generateTokens($user, $command->fingerprint);
        return new OAuthLoginDto(
            accessToken: $tokens['access_token'],
            refreshToken: $tokens['refresh_token'],
            expiresIn: $tokens['expires_in'],
            user: UserMapper::toArray($user)
        );
    }
}
