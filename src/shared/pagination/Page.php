<?php
namespace Src\shared\pagination;
class Page
{
    public function __construct(
        public int $number = 1,
        public int $size = 10
    ) {
        if ($number < 1) {
            throw new \InvalidArgumentException("Page number must be >= 1");
        }

        if ($size < 1) {
            throw new \InvalidArgumentException("Page size must be >= 1");
        }
    }
}
