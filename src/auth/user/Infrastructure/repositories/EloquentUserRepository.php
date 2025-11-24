<?php

namespace  Src\auth\user\infrastructure\repositories;
use Illuminate\Support\Facades\Hash;
use Src\auth\user\domain\Contracts\UserRepositoryInterface;
use App\Models\User as UserModel;
use Src\auth\user\domain\Entities\User;
use Src\auth\user\domain\ValueObjects\UserEmail;
use Src\shared\pagination\Page;
use Src\shared\pagination\PaginatedResult;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function register(User $user): void
    {
        $eloquentUser = UserModel::create([
            'name' => $user->name()->value(),
            'email' => $user->email()->value(),
            'password' => Hash::make($user->password()->value())
        ]);

        $user->setId($eloquentUser->id);
    }

    public function existsByEmail(UserEmail $email): bool
    {
        return UserModel::where('email', $email->value())->exists();
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
