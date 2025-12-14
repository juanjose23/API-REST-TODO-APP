<?php

namespace Src\modules\auth\oauth\Application\Commands\Auth;

use Laravel\Socialite\Contracts\User as SocialUser;
use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderName;

readonly class ProviderLoginCommand
{


    public function __construct(
        public SocialUser $socialUser,
        public ProviderName $providerName,
        public ?string $fingerprint = null
    ) {}
}
