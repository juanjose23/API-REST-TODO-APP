<?php

namespace Src\modules\auth\oauth\Domain\Entities;

use DateTimeInterface;

class JwtToken
{
    public function __construct(
        public readonly string $jti,
        public readonly int $userId,
        public readonly DateTimeInterface $issuedAt,
        public readonly DateTimeInterface $expiresAt,
        public ?DateTimeInterface $revokedAt = null
    ) {
    }

    public function isRevokedOrExpired(DateTimeInterface $now): bool
    {
        return $this->revokedAt !== null || $now > $this->expiresAt;
    }
}
