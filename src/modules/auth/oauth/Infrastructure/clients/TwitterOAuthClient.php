<?php

namespace Src\modules\auth\oauth\Infrastructure\clients;

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\TwitterProvider;
use Src\modules\auth\oauth\Domain\Contracts\OAuth\OAuthClientInterface;
use Src\modules\auth\oauth\Domain\Entities\OAuthUserData;

class TwitterOAuthClient implements OAuthClientInterface
{
    public function fetchUser(string $code): OAuthUserData
    {
        /** @var TwitterProvider $provider */
        $provider = Socialite::driver('twitter');
        $socialUser = $provider->userFromToken($code);

        return OAuthUserData::fromSocialite($socialUser);
    }
}
