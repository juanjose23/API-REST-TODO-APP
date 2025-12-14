<?php

namespace Src\modules\auth\oauth\Application\Handlers\Oauth;

use DateTimeImmutable;
use Illuminate\Support\Str;
use Src\modules\auth\oauth\Application\Commands\Auth\RegisterSocialUserCommand;
use Src\modules\auth\oauth\Domain\Contracts\OAuth\UserProviderRepositoryInterface;
use Src\modules\auth\oauth\Domain\Entities\UserProvider;

use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderId;
use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderName;
use Src\modules\auth\user\Domain\Contracts\UserRepositoryInterface;
use Src\modules\auth\user\Domain\entities\User;
use Src\modules\auth\user\Domain\ValuesObjects\EmailVerifiedAt;
use Src\modules\auth\user\Domain\ValuesObjects\UserEmail;
use Src\modules\auth\user\Domain\ValuesObjects\UserName;
use Src\modules\auth\user\Domain\ValuesObjects\UserPassword;

readonly class RegisterSocialUserHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private UserProviderRepositoryInterface $userProviders
    ) {}

    public function __invoke(RegisterSocialUserCommand $command): User
    {
        $user = new User(
            id: null,
            name: new UserName($command->name->value()),
            email: new UserEmail($command->email->value()),
            password: UserPassword::fromPlain(Str::random(24)),
            emailVerifiedAt: $command->emailVerified ? new EmailVerifiedAt(new DateTimeImmutable()) : null,
            isActive: true
        );

        $this->users->register($user);
        $userProvider = new UserProvider(
            userId: $user->id,
            providerName: new ProviderName($command->provider->value()) ,
            providerId: new ProviderId($command->providerId->value()),
            providerEmail: $command->email->value(),
            avatarUrl: $command->avatarUrl,
            nickname: $command->nickname,
            rawProfile: $command->rawProfile
        );

        $this->userProviders->createUserProvider($userProvider);

        return $user;
    }
}
