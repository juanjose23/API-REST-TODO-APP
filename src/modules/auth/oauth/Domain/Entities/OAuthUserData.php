<?php

namespace Src\modules\auth\oauth\Domain\Entities;


use Src\modules\auth\user\Domain\ValuesObjects\UserEmail;
use Src\modules\auth\user\Domain\ValuesObjects\UserName;

final readonly class OAuthUserData
{
    public function __construct(
        public string  $id,
        public UserEmail  $email,
        public ?UserName $name,
        public ?string $avatar,
    ) {}

    public static function fromArray(array $data): self
    {
        $rawName = $data['name'] ?? null;

        return new self(
            id: (string) $data['id'],
            email: new UserEmail((string) $data['email']),
            name: $rawName ? new UserName($rawName) : null,
            avatar: $data['avatar'] ?? null
        );
    }

    public static function fromSocialite(object $social): self
    {
        $rawId = (string) ($social->getId() ?? $social->id ?? '');
        $rawEmail = (string) ($social->getEmail() ?? $social->email ?? '');
        $rawName = ($social->getName() ?? $social->name ?? $social->nickname ?? null);
        $rawAvatar = ($social->getAvatar() ?? $social->avatar ?? null);

        return new self(
            id: $rawId,
            email: new UserEmail($rawEmail),
            name: $rawName ? new UserName($rawName) : null,
            avatar: $rawAvatar
        );
    }
}
