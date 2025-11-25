<?php

namespace Src\modules\auth\user\Domain\ValuesObjects;

class UserName
{
    public function __construct(private  string $value)
    {
        $name = trim($value);

        if (strlen($name) < 3) {
            throw new \InvalidArgumentException("Name must be at least 3 characters.");
        }

        if (!preg_match('/^[\pL\s]+$/u', $name)) {
            throw new \InvalidArgumentException("Name can only contain letters and spaces.");
        }

        $this->value = $name;
    }

    public function value(): string
    {
        return $this->value;
    }
}
