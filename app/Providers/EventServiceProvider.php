<?php
namespace App\Providers;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Src\modules\auth\user\Infrastructure\events\UserRegistered::class => [
            \Src\modules\auth\user\Infrastructure\listeners\SendVerificationEmail::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
