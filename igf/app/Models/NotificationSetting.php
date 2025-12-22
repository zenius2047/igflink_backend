<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'email_notifications',
        'sms_notifications',
        'payment_alerts',
        'daily_reports',
        'weekly_digests',
        'compliance_reminders',
        'system_alerts',
        'push_notifications',
    ];

    /**
     * Notification setting belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
