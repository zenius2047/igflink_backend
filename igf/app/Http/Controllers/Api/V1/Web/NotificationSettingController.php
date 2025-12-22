<?php

namespace App\Http\Controllers\Api\V1\Web;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    /**
     * Get logged-in user's notification settings
     */
    public function show()
    {
        $user = auth()->user();
        $settings = $user->notificationSetting;
        return response()->json([
            'data' => $settings
        ]);
    }

    /**
     * Update logged-in user's notification settings
     */
  public function storeOrUpdate(Request $request)
{
    $request->validate([
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'payment_alerts' => 'boolean',
        'daily_reports' => 'boolean',
        'weekly_digests' => 'boolean',
        'compliance_reminders' => 'boolean',
        'system_alerts' => 'boolean',
        'push_notifications' => 'boolean',
    ]);

    $user = auth()->user();

    $settings = NotificationSetting::updateOrCreate(
        ['user_id' => $user->id], // condition
        $request->only([
            'email_notifications',
            'sms_notifications',
            'payment_alerts',
            'daily_reports',
            'weekly_digests',
            'compliance_reminders',
            'system_alerts',
            'push_notifications',
        ])
    );

    return response()->json([
        'message' => 'Notification settings updated successfully',
        'data' => $settings
    ]);
}
}