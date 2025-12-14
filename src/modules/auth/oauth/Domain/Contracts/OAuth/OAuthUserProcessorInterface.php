<?php

namespace Src\modules\auth\oauth\Domain\Contracts\OAuth;

use Laravel\Socialite\Contracts\User as SocialUser;
use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderName;
use Src\modules\auth\user\Domain\entities\User;

interface OAuthUserProcessorInterface
{
    public function findOrCreateUser(ProviderName $providerName, SocialUser $socialUser): User;
}
