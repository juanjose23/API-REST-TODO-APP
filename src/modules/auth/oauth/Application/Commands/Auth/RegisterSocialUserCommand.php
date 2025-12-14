<?php

namespace Src\modules\auth\oauth\Application\Commands\Auth;

use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderId;
use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderName;
use Src\modules\auth\user\Domain\ValuesObjects\UserEmail;
use Src\modules\auth\user\Domain\ValuesObjects\UserName;

class RegisterSocialUserCommand
{
    public function __construct(
        public UserName $name,
        public UserEmail $email,
        public ProviderName $provider,
        public ProviderId $providerId,
        public ?string $avatarUrl = null,
        public ?string $nickname = null,
        public ?array $rawProfile = null,
        public bool $emailVerified = true
    ) {}
}
