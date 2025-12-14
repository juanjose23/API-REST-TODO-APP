<?php
declare(strict_types=1);

namespace Src\modules\auth\oauth\Domain\ValuesObjects;

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
