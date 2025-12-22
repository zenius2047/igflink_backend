<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DistrictAdminCreate extends Mailable
{
    use Queueable, SerializesModels;

    public $districtName;
    public $resetUrl;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(string $districtName, string $resetUrl, $password)
    {
        //
        $this->districtName = $districtName;
        $this->resetUrl = $resetUrl;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     */
      public function build()
    {
        return $this->subject('IGF Link – forget Password')
                    ->view('emails.districtaccount');
    }


    /**
     * Get the message content definition.
     */

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
