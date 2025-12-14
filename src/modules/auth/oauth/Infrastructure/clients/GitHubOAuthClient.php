<?php

namespace Src\modules\auth\oauth\Infrastructure\clients;

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Src\modules\auth\oauth\Domain\Contracts\OAuth\OAuthClientInterface;
use Src\modules\auth\oauth\Domain\Entities\OAuthUserData;

class GitHubOAuthClient implements OAuthClientInterface
{
    public function fetchUser(string $code): OAuthUserData
    {
        /** @var GithubProvider $provider */
        $provider = Socialite::driver('github');
        $socialUser = $provider->userFromToken($code);
        return OAuthUserData::fromSocialite($socialUser);
    }
}
