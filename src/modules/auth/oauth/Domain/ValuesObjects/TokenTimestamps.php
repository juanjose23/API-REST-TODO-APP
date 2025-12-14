<?php

namespace Src\modules\auth\oauth\Domain\ValuesObjects;

use InvalidArgumentException;

final readonly class TokenTimestamps
{
    public function __construct(
        private \DateTimeImmutable  $issuedAt,
        private \DateTimeImmutable  $expiresAt,
        private ?\DateTimeImmutable $revokedAt = null
    ) {
        if ($expiresAt <= $issuedAt) {
            throw new InvalidArgumentException('    ');
        }
    }

    public function issuedAt(): \DateTimeImmutable
    {
        return $this->issuedAt;
    }
    public function expiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }
    public function revokedAt(): ?\DateTimeImmutable
    {
        return $this->revokedAt;
    }
    public function isExpired(\DateTimeInterface $now): bool
    {
        return $now > $this->expiresAt;
    }
    public function isRevoked(): bool
    {
        return $this->revokedAt !== null;
    }
}
