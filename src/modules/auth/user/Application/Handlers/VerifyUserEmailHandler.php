<?php

namespace Src\modules\auth\user\Application\Handlers;

use DateTimeImmutable;
use DomainException;
use Src\modules\auth\user\Application\Commands\VerifyUserEmailCommand;
use Src\modules\auth\user\Application\Dtos\VerifyUserEmailResponse;
use Src\modules\auth\user\Domain\contracts\UserRepositoryInterface;

readonly class VerifyUserEmailHandler
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function __invoke(VerifyUserEmailCommand $command): VerifyUserEmailResponse
    {
        $user = $this->repository->findByToken($command->token);

        if (!$user) {
            throw new DomainException("User not found.");
        }

        if ($user->emailVerifiedAt() !== null) {
            return new VerifyUserEmailResponse("Email already verified");
        }

        if ($user->isVerificationTokenExpired()) {
            throw new DomainException("Verification token expired. Please request a resend.");
        }

        $user->setEmailVerifiedAt(new DateTimeImmutable());
        $this->repository->update($user);

        return new VerifyUserEmailResponse("Email successfully verified");
    }
}
