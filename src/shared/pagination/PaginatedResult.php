<?php
namespace Src\shared\pagination;

class PaginatedResult
{
    public function __construct(
        public array $items,
        public int $total,
        public Page $page
    ) {}

    public function lastPage(): int
    {
        return (int) ceil($this->total / $this->page->size);
    }

    public function currentPage(): int
    {
        return $this->page->number;
    }

    public function perPage(): bool
    {
        return $this->page->size;
    }
}
