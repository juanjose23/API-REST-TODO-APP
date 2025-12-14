<?php

namespace Src\modules\auth\user\Domain\entities;

use DateTimeImmutable;
use Src\modules\auth\user\Domain\ValuesObjects\EmailVerifiedAt;
use Src\modules\auth\user\Domain\ValuesObjects\UserEmail;
use Src\modules\auth\user\Domain\ValuesObjects\UserName;
use Src\modules\auth\user\Domain\ValuesObjects\UserPassword;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User implements JWTSubject
{
    public ?int $id;
    private UserName $name;
    private UserEmail $email;
    private UserPassword $password;
    private ?EmailVerifiedAt $emailVerifiedAt;
    private bool $isActive;
    private ?string $avatar;
    private ?string $rememberToken;
    private ?string $verificationToken;
    private ?DateTimeImmutable $verificationTokenCreatedAt;

    public function __construct(
        ?int               $id,
        UserName           $name,
        ?UserEmail          $email,
        UserPassword       $password,
        ?EmailVerifiedAt   $emailVerifiedAt = null,
        bool               $isActive = true,
        ?string            $avatar = null,
        ?string            $rememberToken = null,
        ?string            $verificationToken = null,
        ?DateTimeImmutable $verificationTokenCreatedAt = null
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->emailVerifiedAt = $emailVerifiedAt;
        $this->isActive = $isActive;
        $this->avatar = $avatar;
        $this->rememberToken = $rememberToken;
        $this->verificationToken = $verificationToken;
        $this->verificationTokenCreatedAt = $verificationTokenCreatedAt;
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

    public function password(): UserPassword
    {
        return $this->password;
    }


    public function emailVerifiedAt(): ?EmailVerifiedAt
    {
        return $this->emailVerifiedAt;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function avatar(): ?string
    {
        return $this->avatar;
    }

    public function rememberToken(): ?string
    {
        return $this->rememberToken;
    }

    public function verificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function verificationTokenCreatedAt(): ?DateTimeImmutable
    {
        return $this->verificationTokenCreatedAt;
    }

    public function setEmailVerifiedAt(DateTimeImmutable $param): void
    {
        $this->emailVerifiedAt = new EmailVerifiedAt($param);
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setVerificationToken(string $token): void
    {
        $this->verificationToken = $token;
        $this->verificationTokenCreatedAt = new DateTimeImmutable();
    }

    public function isVerificationTokenExpired(int $minutes = 60): bool
    {
        if (!$this->verificationTokenCreatedAt) return true;
        $expireTime = $this->verificationTokenCreatedAt->modify("+$minutes minutes");
        return new DateTimeImmutable() > $expireTime;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getJWTIdentifier(): ?int
    {
        return $this->id;
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
