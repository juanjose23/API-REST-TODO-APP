<?php

namespace Src\modules\auth\oauth\Domain\Contracts\OAuth;

use Src\modules\auth\oauth\Domain\Entities\UserProvider;
use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderName;

interface UserProviderRepositoryInterface
{

    public function createUserProvider(UserProvider $provider): void;

    public function deleteByUserAndProvider(int $userId, ProviderName $providerName): void;


    public function findByUserAndProvider(int $userId, ProviderName $providerName): ?UserProvider;
}
