<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Mail\SubscriptionExpiryMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifySubscriptionExpiry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Get all active subscriptions with related district and package
        $subscriptions = Subscription::with('district', 'package')
            ->where('is_active', true)
            ->get();

        foreach ($subscriptions as $sub) {
            $daysLeft = $sub->expires_at; // computed days remaining

            // Notify only if exactly 30 days left
            if ($daysLeft === 30 && $sub->district && $sub->district->email) {

                // Log the notification
                Log::info("Subscription expiry notification: 
                    Subscription ID {$sub->id}, 
                    District {$sub->district->name}, 
                    Email {$sub->district->email}, 
                    Days left: {$daysLeft}");

                // Send email
                Mail::to($sub->district->email)
                    ->send(new SubscriptionExpiryMail($sub, $daysLeft));
            }
        }
    }
}
