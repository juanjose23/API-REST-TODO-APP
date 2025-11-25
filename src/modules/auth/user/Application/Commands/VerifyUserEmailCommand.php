<?php

namespace Src\modules\auth\user\Application\Commands;

readonly class VerifyUserEmailCommand
{
    public function __construct(public string $token) {}
}
