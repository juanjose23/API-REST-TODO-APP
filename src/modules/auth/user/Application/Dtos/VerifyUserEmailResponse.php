<?php

namespace Src\modules\auth\user\Application\Dtos;

class VerifyUserEmailResponse
{
    public function __construct(public string $message) {}
}
