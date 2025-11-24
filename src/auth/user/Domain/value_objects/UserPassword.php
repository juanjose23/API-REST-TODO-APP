<?php


namespace Src\auth\user\domain\ValueObjects;

class UserPassword
{
    public function __construct(private  string $value)
    {
        if (strlen($value) < 8) {
            throw new \InvalidArgumentException("Password must have at least 8 characters.");
        }

        if (!preg_match('/[A-Z]/', $value)) {
            throw new \InvalidArgumentException("Password must contain at least one uppercase letter.");
        }

        if (!preg_match('/[0-9]/', $value)) {
            throw new \InvalidArgumentException("Password must contain at least one number.");
        }

        if (!preg_match('/[\W]/', $value)) {
            throw new \InvalidArgumentException("Password must contain at least one symbol.");
        }
        $this->value = trim($value);
    }

    public static function fromPlain(string $plain): self
    {
        return new self($plain);
    }


    public function value(): string
    {
        return $this->value;
    }
}
