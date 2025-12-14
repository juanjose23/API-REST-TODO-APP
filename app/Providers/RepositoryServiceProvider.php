<?php

namespace App\Providers;

use App\Interfaces\AuthInterface;
use App\Interfaces\TeamInterface;
use App\Repository\AuthRepository;
use App\Repository\TeamRepository;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\ServiceProvider;
use Src\modules\auth\oauth\Domain\Contracts\Auth\AuthTokenGeneratorInterface;
use Src\modules\auth\oauth\Domain\Contracts\Auth\CredentialsAuthenticatorInterface;
use Src\modules\auth\oauth\Domain\Contracts\Auth\JwtManagerInterface;
use Src\modules\auth\oauth\Domain\Contracts\Jwt\JwtTokenRepositoryInterface as DddJwtTokenRepositoryInterface;
use Src\modules\auth\oauth\Domain\Contracts\OAuth\OAuthUserProcessorInterface;
use Src\modules\auth\oauth\Domain\Contracts\OAuth\UserProviderRepositoryInterface;
use Src\modules\auth\oauth\Infrastructure\Clients\GitHubOAuthClient;
use Src\modules\auth\oauth\Infrastructure\Clients\TwitterOAuthClient;
use Src\modules\auth\oauth\Infrastructure\Repositories\EloquentJwtTokenRepository;
use Src\modules\auth\oauth\Infrastructure\Repositories\UserProviderRepository;
use Src\modules\auth\oauth\Infrastructure\services\JWTAuthAdapter;
use Src\modules\auth\oauth\Infrastructure\services\JwtAuthTokenGenerator;
use Src\modules\auth\oauth\Infrastructure\services\LaravelCredentialsAuthenticator;
use Src\modules\auth\oauth\Infrastructure\services\OAuthUserProcessor;
use Src\modules\auth\user\Domain\Contracts\UserRepositoryInterface;
use Src\modules\auth\user\Infrastructure\Repositories\EloquentUserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthInterface::class, AuthRepository::class);
        $this->app->bind(TeamInterface::class, TeamRepository::class);

        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(AuthTokenGeneratorInterface::class, JwtAuthTokenGenerator::class);

        $this->app->bind(CredentialsAuthenticatorInterface::class, LaravelCredentialsAuthenticator::class);
        $this->app->bind(DddJwtTokenRepositoryInterface::class, EloquentJwtTokenRepository::class);
        $this->app->bind(JwtManagerInterface::class, JWTAuthAdapter::class);
        $this->app->bind(OAuthUserProcessorInterface::class, OAuthUserProcessor::class);
        $this->app->bind(UserProviderRepositoryInterface::class, UserProviderRepository::class);
        $this->app->bind('oauth.github', fn() => new GitHubOAuthClient());
        $this->app->bind('oauth.twitter', fn() => new TwitterOAuthClient());
    }


    /**
     * Bootstrap srvices.
     */
    public function boot(): void
    {
        //
    }
}
