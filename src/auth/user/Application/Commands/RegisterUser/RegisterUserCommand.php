<?php
namespace Src\auth\user\application\commands\RegisterUser;
use Src\auth\user\application\Dtos\RegisterUserRequest;

readonly class RegisterUserCommand
{
    public function __construct(
        public RegisterUserRequest $dto
    ) {}
}
