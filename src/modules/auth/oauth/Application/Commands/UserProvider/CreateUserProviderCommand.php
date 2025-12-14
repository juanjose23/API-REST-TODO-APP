<?php

namespace Src\modules\auth\oauth\Application\Commands\UserProvider;

use Src\modules\auth\oauth\Domain\Entities\UserProvider;

readonly class CreateUserProviderCommand
{
    public function __construct(
        public UserProvider $userProvider
    ) {}
}
