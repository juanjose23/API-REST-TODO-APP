<?php
namespace Src\auth\user\infrastructure\listeners;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Src\Auth\User\Infrastructure\Events\UserRegistered;
use Src\auth\user\infrastructure\mail\VerifyEmailMail;

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
            new VerifyEmailMail([
                'id' => $user->id(),
                'name' => $user->name()->value()
            ])
        );

    }
}
