<?php

namespace Src\modules\auth\oauth\Application\Commands\Auth;

class LoginCommand
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $fingerprint = null
    ) {}
}
