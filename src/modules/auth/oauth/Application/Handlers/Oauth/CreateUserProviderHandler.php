<?php

namespace Src\modules\auth\oauth\Application\Handlers\Oauth;

use Src\modules\auth\oauth\Application\Commands\UserProvider\CreateUserProviderCommand;
use Src\modules\auth\oauth\Domain\Contracts\OAuth\UserProviderRepositoryInterface;

readonly class CreateUserProviderHandler
{
    public function __construct(private UserProviderRepositoryInterface $repository) {}

    public function __invoke(CreateUserProviderCommand $command): void
    {
        $this->repository->createUserProvider($command->userProvider);
    }

}
