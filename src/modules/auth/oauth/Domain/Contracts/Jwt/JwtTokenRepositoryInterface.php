<?php

namespace Src\modules\auth\oauth\Domain\Contracts\Jwt;

use Src\modules\auth\oauth\Domain\Entities\JwtToken;

interface   JwtTokenRepositoryInterface
{
    public function store(JwtToken $token): JwtToken;
    public function findByJti(string $jti): ?JwtToken;
    public function revokeByJti(string $jti): bool;
    public function revokeAllForUser(int $userId): int;
}
