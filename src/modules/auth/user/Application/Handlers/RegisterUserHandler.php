<?php

namespace Src\modules\auth\user\Application\Handlers;
use DomainException;
use Illuminate\Support\Facades\Event;
use Random\RandomException;
use Src\modules\auth\user\Application\Commands\RegisterUser\RegisterUserCommand;
use Src\modules\auth\user\Application\Dtos\RegisterUserResponse;
use Src\modules\auth\user\Domain\Contracts\UserRepositoryInterface;
use Src\modules\auth\user\Domain\entities\User;
use Src\modules\auth\user\Domain\ValuesObjects\UserEmail;
use Src\modules\auth\user\Domain\ValuesObjects\UserName;
use Src\modules\auth\user\Domain\ValuesObjects\UserPassword;
use Src\modules\auth\user\Infrastructure\events\UserRegistered;

readonly class RegisterUserHandler
{
    public function __construct(private UserRepositoryInterface $repository) {}

    /**
     * @throws RandomException
     */
    public function __invoke(RegisterUserCommand $command): RegisterUserResponse
    {
        $name = new UserName($command->dto->name);
        $email = new UserEmail($command->dto->email);
        $password = UserPassword::fromPlain($command->dto->password);

        if ($this->repository->existsByEmail($email)) {
            throw new DomainException("Email already exists.");
        }

        $user = new User(
            id: null,
            name: $name,
            email: $email,
            password: $password
        );
        $user->setVerificationToken(bin2hex(random_bytes(16)));
        $this->repository->register($user);
        Event::dispatch(new UserRegistered($user));
        return new RegisterUserResponse(
            $user->id(),
            $user->name()->value(),
            $user->email()->value()
        );
    }
}
