<?php

namespace Src\auth\user\application\Handlers;
use DomainException;
use Illuminate\Support\Facades\Event;
use Src\auth\user\application\Commands\RegisterUser\RegisterUserCommand;
use Src\auth\user\application\Dtos\RegisterUserResponse;
use Src\auth\user\domain\Contracts\UserRepositoryInterface;
use Src\auth\user\domain\Entities\User;
use Src\auth\user\domain\ValueObjects\UserEmail;
use Src\auth\user\domain\ValueObjects\UserName;
use Src\auth\user\domain\ValueObjects\UserPassword;
use Src\Auth\User\Infrastructure\Events\UserRegistered;

readonly class RegisterUserHandler
{
    public function __construct(private UserRepositoryInterface $repository) {}

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
        $this->repository->register($user);
        Event::dispatch(new UserRegistered($user));
        return new RegisterUserResponse(
            $user->id(),
            $user->name()->value(),
            $user->email()->value()
        );
    }
}
