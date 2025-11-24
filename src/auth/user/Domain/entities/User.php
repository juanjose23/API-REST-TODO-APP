<?php

namespace Src\auth\user\domain\Entities;

use Illuminate\Support\Facades\Hash;
use Src\auth\user\domain\ValueObjects\ProviderId;
use Src\auth\user\domain\ValueObjects\ProviderName;
use Src\auth\user\domain\ValueObjects\UserEmail;
use Src\auth\user\domain\ValueObjects\UserName;
use Src\auth\user\domain\ValueObjects\UserPassword;
use Src\auth\user\domain\ValueObjects\EmailVerifiedAt;

class User
{

    private ?int $id;
    private UserName $name;
    private UserEmail $email;
    private UserPassword $password;
    private ?ProviderName $provider;
    private ?ProviderId $providerId;
    private ?EmailVerifiedAt $emailVerifiedAt;

    public function __construct(
        ?int $id,
        UserName $name,
        UserEmail $email,
        UserPassword $password,
        ?ProviderName $provider = null,
        ?ProviderId $providerId = null,
        ?EmailVerifiedAt $emailVerifiedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->provider = $provider;
        $this->providerId = $providerId;
        $this->emailVerifiedAt = $emailVerifiedAt;
    }
    public function id(): ?int
    {
        return $this->id;
    }
    public function name(): UserName
    {
        return $this->name;
    }
    public function email(): UserEmail
    {
        return $this->email;
    }

    public function password():UserPassword
    {
        return $this->password;
    }

    public function setId(mixed $id): void
    {
        $this->id = $id;
    }

}
