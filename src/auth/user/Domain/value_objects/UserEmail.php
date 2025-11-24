<?php

namespace Src\auth\user\domain\ValueObjects;

class UserEmail
{
    public function __construct(private readonly string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format.");
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
