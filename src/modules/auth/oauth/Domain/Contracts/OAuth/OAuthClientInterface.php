<?php

namespace Src\modules\auth\oauth\Domain\Contracts\OAuth;

use Src\modules\auth\oauth\Domain\Entities\OAuthUserData;

interface OAuthClientInterface
{
    public function fetchUser(string $code): OAuthUserData;

}
