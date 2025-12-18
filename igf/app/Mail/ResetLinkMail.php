<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetLinkMail extends Mailable
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
     * Get the message envelope.
     */


    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->subject('IGF Link – forget Password')
                    ->view('emails.resetlink');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
