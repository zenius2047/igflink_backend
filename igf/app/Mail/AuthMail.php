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

    /**
     * Create a new message instance.
     */
    public function __construct(string $fullName, string $resetUrl)
    {
        $this->fullName = $fullName;
        $this->resetUrl = $resetUrl;
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
