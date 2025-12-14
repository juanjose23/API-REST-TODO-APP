<?php

namespace Src\modules\auth\oauth\Application\Dtos;


use Src\modules\auth\user\Domain\entities\User;

class OAuthLoginDto
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
        public int $expiresIn,
        public array $user
    ) {}
}
