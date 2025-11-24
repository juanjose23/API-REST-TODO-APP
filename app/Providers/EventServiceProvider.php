<?php
namespace App\Providers;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Src\Auth\User\Infrastructure\Events\UserRegistered::class => [
            \Src\Auth\User\Infrastructure\Listeners\SendVerificationEmail::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
