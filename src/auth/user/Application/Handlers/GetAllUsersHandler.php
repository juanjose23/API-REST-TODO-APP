<?php

namespace Src\auth\user\application\Handlers;

use Src\auth\user\application\Queries\getAllUsersQuery;
use Src\auth\user\domain\Contracts\UserRepositoryInterface;
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
