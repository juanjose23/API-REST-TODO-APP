<?php

namespace App\Providers;

use App\Interfaces\AuthInterface;
use App\Interfaces\TeamInterface;
use App\Models\Team;
use App\Repository\AuthRepository;
use App\Repository\TeamRepository;
use Illuminate\Support\ServiceProvider;
use Src\auth\user\domain\Contracts\UserRepositoryInterface;
use Src\auth\user\infrastructure\repositories\EloquentUserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(AuthInterface::class, AuthRepository::class);
        $this->app->bind(TeamInterface::class, TeamRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
    }

    /**
     * Bootstrap srvices.
     */
    public function boot(): void
    {
        //
    }
}
