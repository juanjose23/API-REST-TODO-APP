<?php
declare(strict_types=1);

namespace Src\auth\user\domain\ValueObjects;

class ProviderId
{
    private ?string $value;

    public function __construct(?string $value)
    {
        $this->value = $value;
    }

    public function value(): ?string
    {
        return $this->value;
    }
}
