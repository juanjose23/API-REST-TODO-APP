<?php

namespace Src\modules\auth\oauth\Application\Handlers\Oauth;

use Src\modules\auth\oauth\Application\Commands\UserProvider\DeleteUserProviderCommand;
use Src\modules\auth\oauth\Domain\Contracts\OAuth\UserProviderRepositoryInterface;

readonly class DeleteUserProviderHandler
{
    public function __construct(private UserProviderRepositoryInterface $repository) {}

    public function __invoke(DeleteUserProviderCommand $command): void
    {
        $this->repository->deleteByUserAndProvider(
            $command->userId,
            $command->providerName
        );
    }
}
