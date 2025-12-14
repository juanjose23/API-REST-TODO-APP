<?php

namespace Src\modules\auth\oauth\Domain\ValuesObjects;

use InvalidArgumentException;
use Src\shared\enums\OauthProviders;

readonly class ProviderName
{
    public function __construct(private ?string $value)
    {
        if ($value !== null && !OauthProviders::tryFrom($value)) {
            throw new InvalidArgumentException("Invalid provider name.");
        }
    }

    public function value(): ?string
    {
        return $this->value;
    }
}
