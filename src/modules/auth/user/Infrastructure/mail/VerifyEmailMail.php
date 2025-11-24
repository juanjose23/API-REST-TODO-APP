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
     * Tiempo máximo para reintentos si falla.
     * Opcional: 3 intentos, por defecto
     */
    public int $tries = 3;

    /**
     * Retraso entre intentos en segundos
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
        // Token del usuario
        $verificationUrl = url("/auth/verify-email/{$this->user->verificationToken()}");
        $expiresAt = now()->addMinutes(60);

        return $this->subject('Verifica tu correo')
            ->view('emails.verify-email')
            ->with([
                'name' => $this->user->name()->value(),
                'url'  => $verificationUrl,
                'expiresAt' => $expiresAt,
            ]);
    }
}
