<?php

namespace Src\modules\auth\oauth\Infrastructure\Repositories;

use Src\modules\auth\oauth\Domain\Contracts\OAuth\UserProviderRepositoryInterface;
use Src\modules\auth\oauth\Domain\Entities\UserProvider;
use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderName;
use App\Models\UserProvider as UserProviderModel;
class UserProviderRepository implements UserProviderRepositoryInterface
{

    public function createUserProvider(UserProvider $provider): void
    {
        UserProviderModel::create([
            'user_id' => $provider->userId,
            'provider_name' => $provider->providerName->value(),
            'provider_id' => $provider->providerId->value(),
            'provider_email' => $provider->providerEmail,
            'avatar_url' => $provider->avatarUrl,
            'nickname' => $provider->nickname,
            'raw_profile' => $provider->rawProfile,
        ]);
    }

    public function deleteByUserAndProvider(int $userId, ProviderName $providerName): void
    {
        UserProviderModel::where('user_id', $userId)
            ->where('provider_name', $providerName->value())
            ->delete();
    }

    public function findByUserAndProvider(int $userId, ProviderName $providerName): ?UserProvider
    {
        $model = UserProviderModel::where('user_id', $userId)
            ->where('provider_name', $providerName->value())
            ->first();

        if (!$model) {
            return null;
        }

        return new UserProvider(
            userId: $model->user_id,
            providerName: $model->provider_name,
            providerId: $model->provider_id,
            providerEmail: $model->provider_email,
            avatarUrl: $model->avatar_url,
            nickname: $model->nickname,
            rawProfile: $model->raw_profile,
        );
    }
}
