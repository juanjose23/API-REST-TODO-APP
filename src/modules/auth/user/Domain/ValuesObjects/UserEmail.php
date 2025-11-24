<?php

namespace Src\modules\auth\user\Domain\ValuesObjects;

use InvalidArgumentException;

readonly class UserEmail
{
    public function __construct(private string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format.");
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
