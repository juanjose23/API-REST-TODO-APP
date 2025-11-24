<?php
namespace Src\auth\user\infrastructure\mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $userData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $userData)
    {
        $this->userData = $userData;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $verificationUrl = url("/verify/{$this->userData['id']}");

        return $this->subject('Verifica tu correo')
            ->view('emails.verify-email')
            ->with([
                'name' => $this->userData['name'],
                'url'  => $verificationUrl,
            ]);
    }
}
