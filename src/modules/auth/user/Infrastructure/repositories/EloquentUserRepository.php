<?php

namespace  Src\modules\auth\user\Infrastructure\repositories;
use App\Models\User as UserModel;
use DomainException;
use Src\modules\auth\user\Domain\contracts\UserRepositoryInterface;
use Src\modules\auth\user\Domain\entities\User;
use Src\modules\auth\user\Domain\ValuesObjects\EmailVerifiedAt;
use Src\modules\auth\user\Domain\ValuesObjects\ProviderId;
use Src\modules\auth\user\Domain\ValuesObjects\ProviderName;
use Src\modules\auth\user\Domain\ValuesObjects\UserEmail;
use Src\modules\auth\user\Domain\ValuesObjects\UserName;
use Src\modules\auth\user\Domain\ValuesObjects\UserPassword;
use Src\shared\pagination\Page;
use Src\shared\pagination\PaginatedResult;

class EloquentUserRepository implements UserRepositoryInterface
{

    public function register(User $user): void
    {
        $eloquent = UserModel::create([
            'name' => $user->name()->value(),
            'email' => $user->email()->value(),
            'password' => $user->password()->value(),
            'provider' => $user->provider()?->value(),
            'provider_id' => $user->providerId()?->value(),
            'email_verified_at' => $user->emailVerifiedAt()?->value(),
            'avatar' => $user->avatar(),
            'is_active' => $user->isActive(),
            'remember_token' => $user->rememberToken(),
            'verification_token' => $user->verificationToken(),
        ]);

        $user->setId($eloquent->id);
    }

    public function update(User $user): void
    {
        $eloquent = UserModel::find($user->id());

        if (!$eloquent) {
            throw new DomainException("User not found.");
        }

        $eloquent->name = $user->name()->value();
        $eloquent->email = $user->email()->value();
        $eloquent->password = $user->password()->value();
        $eloquent->provider = $user->provider()?->value();
        $eloquent->provider_id = $user->providerId()?->value();
        $eloquent->email_verified_at = $user->emailVerifiedAt()?->value();
        $eloquent->avatar = $user->avatar();
        $eloquent->is_active = $user->isActive();
        $eloquent->remember_token = $user->rememberToken();
        $eloquent->verification_token = $user->verificationToken();
        $eloquent->verification_token_created_at = $user->verificationTokenCreatedAt();
        $eloquent->save();
    }

    public function findById(int $id): ?User
    {
        $eloquent = UserModel::find($id);

        if (!$eloquent) return null;

        return $this->mapToEntity($eloquent);
    }

    public function findByToken(string $token): ?User
    {
        $eloquent = UserModel::where('verification_token', $token)->first();

        if (!$eloquent) return null;

        return $this->mapToEntity($eloquent);
    }

    public function existsByEmail(UserEmail $email): bool
    {
        return UserModel::where('email', $email->value())->exists();
    }

    public function findByEmail(UserEmail $email): ?User
    {
        $eloquent = UserModel::where('email', $email->value())->first();
        if (!$eloquent) return null;
        return $this->mapToEntity($eloquent);
    }

    private function mapToEntity(UserModel $eloquent): User
    {
        return new User(
            id: $eloquent->id,
            name: new UserName($eloquent->name),
            email: new UserEmail($eloquent->email),
            password: UserPassword::fromHashed($eloquent->password),
            provider: $eloquent->provider ? new ProviderName($eloquent->provider) : null,
            providerId: $eloquent->provider_id ? new ProviderId($eloquent->provider_id) : null,
            emailVerifiedAt: $eloquent->email_verified_at
                ? new EmailVerifiedAt($eloquent->email_verified_at->toDateTimeImmutable())
                : null,
            isActive: $eloquent->is_active,
            avatar: $eloquent->avatar,
            rememberToken: $eloquent->remember_token
        );
    }




    public function getAllUsers(Page $page): PaginatedResult
    {
        $query = UserModel::query()
            ->select(['id', 'name', 'email', 'created_at'])
            ->orderBy('id', 'desc');


        $paginator = $query->paginate(
            perPage: $page->size,
            page: $page->number
        );

        return new PaginatedResult(
            items: $paginator->items(),
            total: $paginator->total(),
            page: $page
        );
    }
}
