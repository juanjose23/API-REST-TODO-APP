<?php

namespace Src\modules\auth\oauth\Infrastructure\services;
use Laravel\Socialite\Contracts\User as SocialUser;
use Src\modules\auth\oauth\Application\Commands\Auth\RegisterSocialUserCommand;
use Src\modules\auth\oauth\Application\Handlers\Oauth\RegisterSocialUserHandler;
use Src\modules\auth\oauth\Domain\Contracts\OAuth\OAuthUserProcessorInterface;
use Src\modules\auth\oauth\Domain\Contracts\OAuth\UserProviderRepositoryInterface;
use Src\modules\auth\oauth\Domain\Entities\UserProvider;
use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderId;
use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderName;
use Src\modules\auth\user\Domain\Contracts\UserRepositoryInterface;
use Src\modules\auth\user\Domain\entities\User;
use Src\modules\auth\user\Domain\ValuesObjects\UserEmail;
use Src\modules\auth\user\Domain\ValuesObjects\UserName;

readonly class OAuthUserProcessor implements OAuthUserProcessorInterface
{
    public function __construct(
        private UserProviderRepositoryInterface $userProviders,
        private RegisterSocialUserHandler $registerHandler,
        private UserRepositoryInterface $userRepository
    ) {}


    public function findOrCreateUser(ProviderName $providerName, SocialUser $socialUser): User
    {
        $user = $this->userRepository->findByEmail(new UserEmail($socialUser->getEmail()));

        if ($user) {

            $userProvider = $this->userProviders->findByUserAndProvider($user->id, $providerName);

            if (!$userProvider) {
                $this->userProviders->createUserProvider(
                    new UserProvider(
                        userId: $user->id,
                        providerName: new ProviderName($providerName->value()),
                        providerId: new ProviderId($socialUser->getId()),
                        providerEmail: $socialUser->getEmail(),
                        avatarUrl: $socialUser->getAvatar(),
                        nickname: $socialUser->getNickname(),
                        rawProfile: (array)$socialUser->user
                    )
                );
            }
        } else {

            $command = new RegisterSocialUserCommand(
                name: new UserName($socialUser->getName() ?? $socialUser->getNickname()),
                email: new UserEmail($socialUser->getEmail()),
                provider: $providerName,
                providerId: new ProviderId($socialUser->getId()),
                avatarUrl: $socialUser->getAvatar(),
                nickname: $socialUser->getNickname(),
                rawProfile: (array)$socialUser->user,
                emailVerified: true
            );

            $user = ($this->registerHandler)($command);
        }

        return $user;
    }
}
