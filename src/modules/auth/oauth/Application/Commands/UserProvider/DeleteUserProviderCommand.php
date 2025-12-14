<?php

namespace Src\modules\auth\oauth\Application\Commands\UserProvider;

use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderName;

readonly class DeleteUserProviderCommand
{
    public function __construct(
        public int $userId,
        public ProviderName $providerName
    ) {}
}
