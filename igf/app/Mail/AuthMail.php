<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuthMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $resetUrl;
    public string $fullName;
    public string $temproaryPassword;
    public string $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $fullName, string $resetUrl, string $temproaryPassword, string $loginUrl)
    {
        $this->fullName = $fullName;
        $this->resetUrl = $resetUrl;
        $this->temproaryPassword = $temproaryPassword;
        $this->loginUrl = $loginUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('IGF Link – Set Your Password')
                    ->view('emails.auth_mail');
    }
}
