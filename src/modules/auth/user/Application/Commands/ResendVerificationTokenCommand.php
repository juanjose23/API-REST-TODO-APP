<?php

namespace Src\modules\auth\user\Application\Commands;

class ResendVerificationTokenCommand
{
    public function __construct(public string $email) {}
}
