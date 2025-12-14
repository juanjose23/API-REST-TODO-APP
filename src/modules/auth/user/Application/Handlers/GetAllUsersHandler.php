<?php

namespace Src\modules\auth\user\Application\Handlers;

use Src\modules\auth\user\Application\Queries\getAllUsersQuery;
use Src\modules\auth\user\Domain\Contracts\UserRepositoryInterface;
use Src\shared\pagination\Page;
use Src\shared\pagination\PaginatedResult;

readonly class GetAllUsersHandler
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}


    public function __invoke(GetAllUsersQuery $query): PaginatedResult
    {
        $page = new Page(number: $query->page, size: $query->perPage);
        return $this->repository->getAllUsers($page);
    }
}
