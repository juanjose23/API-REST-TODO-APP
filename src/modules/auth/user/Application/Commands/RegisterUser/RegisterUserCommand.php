<?php
namespace Src\modules\auth\user\Application\Commands\RegisterUser;
use Src\modules\auth\user\Application\Dtos\RegisterUserRequest;

readonly class RegisterUserCommand
{
    public function __construct(
        public RegisterUserRequest $dto
    ) {}
}
