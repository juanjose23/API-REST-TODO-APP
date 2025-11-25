<?php

namespace Src\modules\auth\user\Application\Handlers;

use DomainException;
use Illuminate\Support\Facades\Event;
use Random\RandomException;
use Src\modules\auth\user\Application\Commands\ResendVerificationTokenCommand;
use Src\modules\auth\user\Domain\contracts\UserRepositoryInterface;
use Src\modules\auth\user\Domain\ValuesObjects\UserEmail;
use Src\modules\auth\user\Infrastructure\events\UserRegistered;

readonly class ResendVerificationTokenHandler
{
    public function __construct(private UserRepositoryInterface $repository) {}

    /**
     * @throws RandomException
     */
    public function __invoke(ResendVerificationTokenCommand $command): void
    {
        $user = $this->repository->findByEmail(new UserEmail($command->email));

        if (!$user) {
            throw new DomainException("User not found.");
        }

        if ($user->emailVerifiedAt() !== null) {
            throw new DomainException("User already verified.");
        }

        if ($user->isVerificationTokenExpired()) {
            $user->setVerificationToken(bin2hex(random_bytes(16)));
            $this->repository->update($user);
        }

        Event::dispatch(new UserRegistered($user));
    }
}
