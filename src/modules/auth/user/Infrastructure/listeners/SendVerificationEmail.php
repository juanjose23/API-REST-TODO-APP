<?php
namespace Src\modules\auth\user\Infrastructure\listeners;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Src\modules\auth\user\Infrastructure\events\UserRegistered;
use Src\modules\auth\user\Infrastructure\mail\VerifyEmailMail;

class SendVerificationEmail
{
    use Queueable, SerializesModels;

    /**
     * Handle the event.
     *
     * @param UserRegistered $event
     * @return void
     */
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        Mail::to($user->email()->value())->send(
            new VerifyEmailMail($user)
        );

        Log::info("UserRegistered recibido", [
            'email' => $event->user->email()->value()
        ]);
    }
}
