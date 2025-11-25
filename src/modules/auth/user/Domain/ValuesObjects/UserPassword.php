<?php
namespace Src\modules\auth\user\Domain\ValuesObjects;

class UserPassword
{
    private string $hashed;

    private function __construct(string $hashed)
    {
        $this->hashed = $hashed;
    }

    public static function fromPlain(string $plain): self
    {
        if (strlen($plain) < 8) {
            throw new \InvalidArgumentException("Password must have at least 8 characters.");
        }

        if (!preg_match('/[A-Z]/', $plain)) {
            throw new \InvalidArgumentException("Password must contain at least one uppercase letter.");
        }

        if (!preg_match('/[0-9]/', $plain)) {
            throw new \InvalidArgumentException("Password must contain at least one number.");
        }

        if (!preg_match('/[\W]/', $plain)) {
            throw new \InvalidArgumentException("Password must contain at least one symbol.");
        }

        $hashed = password_hash(trim($plain), PASSWORD_BCRYPT);

        return new self($hashed);
    }
    public static function fromHashed(string $hashed): self
    {
        return new self($hashed);
    }
    public function check(string $plain): bool
    {
        return password_verify($plain, $this->hashed);
    }

    public function value(): string
    {
        return $this->hashed;
    }
}
