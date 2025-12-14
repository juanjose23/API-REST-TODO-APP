<?php

namespace Src\modules\auth\oauth\Domain\Contracts\Auth;

interface JwtManagerInterface
{
    public function getCurrentTokenJti(): ?string;
    public function invalidateCurrentToken(): bool;
}
