<?php

namespace Src\auth\user\application\Queries;

class GetAllUsersQuery
{
    public function __construct(
        public readonly int $perPage = 10,
        public readonly int $page = 1
    ) {}
}
