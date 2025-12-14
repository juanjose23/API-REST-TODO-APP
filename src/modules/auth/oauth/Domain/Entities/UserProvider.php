<?php

namespace Src\modules\auth\oauth\Domain\Entities;

use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderId;
use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderName;

readonly class UserProvider
{
    public function __construct(
        public int $userId,
        public ProviderName $providerName,
        public ProviderId $providerId,
        public ?string $providerEmail,
        public ?string $avatarUrl,
        public ?string $nickname,
        public ?array $rawProfile
    ) {}
}
