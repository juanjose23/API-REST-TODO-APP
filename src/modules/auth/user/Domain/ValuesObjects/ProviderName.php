<?php

namespace Src\modules\auth\user\Domain\ValuesObjects;

class ProviderName
{
    private const VALID = ['google', 'facebook', 'github', 'apple'];

    public function __construct(private readonly ?string $value)
    {
        if ($value !== null && !in_array($value, self::VALID)) {
            throw new \InvalidArgumentException("Invalid provider name.");
        }
    }

    public function value(): ?string
    {
        return $this->value;
    }
}
