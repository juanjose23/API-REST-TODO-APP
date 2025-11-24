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
            throw new DomainException("Usuario no encontrado.");
        }

        if ($user->emailVerifiedAt() !== null) {
            return new VerifyUserEmailResponse("Correo ya verificado");
        }

        if ($user->isVerificationTokenExpired()) {
            throw new DomainException("El token de verificación ha expirado. Solicita un reenvío.");
        }

        $user->setEmailVerifiedAt(new DateTimeImmutable());
        $this->repository->update($user);

        return new VerifyUserEmailResponse("Correo verificado correctamente");
    }
}
