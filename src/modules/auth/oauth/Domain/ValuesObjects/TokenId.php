<?php

namespace Src\modules\auth\oauth\Domain\ValuesObjects;

final class TokenId
{
    private string $value;

    public function __construct(string $value)
    {
        if (!preg_match('/^[0-9a-fA-F-]{16,}$/', $value)) {
            throw new \InvalidArgumentException('TokenId invalid');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
