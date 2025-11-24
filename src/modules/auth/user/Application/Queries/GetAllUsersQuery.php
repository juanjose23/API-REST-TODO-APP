<?php

namespace Src\modules\auth\user\Application\Queries;

readonly class GetAllUsersQuery
{
    public function __construct(
        public int $perPage = 10,
        public int $page = 1
    ) {}
}
