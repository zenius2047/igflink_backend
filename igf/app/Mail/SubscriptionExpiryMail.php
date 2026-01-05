<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $daysLeft;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscription $subscription, int $daysLeft)
    {
        $this->subscription = $subscription;
        $this->daysLeft = $daysLeft;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Subscription Expiry Notification')
                    ->markdown('emails.subscriptions.expiry');
    }
}
