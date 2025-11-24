<?php
namespace Src\auth\user\application\Dtos;
use OpenApi\Attributes as OAT;
#[OAT\Schema(
    schema: 'RegisterUserSchema',
    required: ['email', 'name', 'password'],
    properties: [
        new OAT\Property(property: 'email', type: 'string', format: 'email'),
        new OAT\Property(property: 'name', type: 'string'),
        new OAT\Property(property: 'password', type: 'string', format: 'password'),
    ]
)]
readonly class RegisterUserRequest
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}
}
