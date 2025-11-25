<?php

namespace Src\modules\auth\user\Infrastructure\mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Src\modules\auth\user\Domain\Entities\User;

class VerifyEmailMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;

    /**
     * Max retry attempts if it fails.
     * Optional: 3 attempts by default.
     */
    public int $tries = 3;

    /**
     * Delay between attempts in seconds.
     */
    public array $backoff = [10, 30, 60]; // 10s, 30s, 60s

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        // User token verification URL
        $verificationUrl = url("/auth/verify-email/{$this->user->verificationToken()}");
        $expiresAt = now()->addMinutes(60);

        return $this->subject('Verify your email')
            ->view('emails.verify-email')
            ->with([
                'name' => $this->user->name()->value(),
                'url'  => $verificationUrl,
                'expiresAt' => $expiresAt,
            ]);
    }
}
