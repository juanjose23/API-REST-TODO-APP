<?php

namespace Src\modules\auth\user\Application\Dtos;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: "RegisterUserResponse",
    properties: [
        new OAT\Property(property: 'id', type: 'integer'),
        new OAT\Property(property: 'email', type: 'string'),
        new OAT\Property(property: 'name', type: 'string'),
    ]
)]
readonly class RegisterUserResponse
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email
    ) {}
}
