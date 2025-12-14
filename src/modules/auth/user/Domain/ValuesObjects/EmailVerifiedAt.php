<?php


namespace Src\modules\auth\user\Domain\ValuesObjects;

use DateTimeImmutable;

readonly class EmailVerifiedAt
{
        public function __construct(private ?DateTimeImmutable $value)
    {
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    public function isVerified(): bool
    {
        return $this->value !== null;
    }
}
