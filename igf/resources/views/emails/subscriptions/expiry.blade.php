<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscription Expired | IGF Link</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color:#dc3545; padding:20px; text-align:center;">
                            <h1 style="margin:0; color:#ffffff; font-size:22px;">
                                Subscription Expired
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; color:#374151; font-size:15px; line-height:1.6;">
                            <p style="margin-top:0;">
                                Hello <strong>{{ $subscription->district->name }}</strong>,
                            </p>

                            <p>
                               We wanted to give you a friendly reminder that your subscription for Pro Plan will expire in 30 days.
                            </p>

                            <p>
                                To continue enjoying uninterrupted access to our services, please renew your subscription as soon as possible.
                            </p>

                            <!-- Button -->
                            <p style="text-align:center; margin:30px 0;">
                                <a href="{{ url('/subscriptions') }}"
                                   style="display:inline-block; padding:12px 28px; background-color:#dc3545;
                                   color:#ffffff; text-decoration:none; font-weight:bold; border-radius:6px;">
                                    Renew Now
                                </a>
                            </p>

                            <p style="font-size:14px; color:#6b7280;">
                                If you have already renewed your subscription, please ignore this email.
                            </p>

                            <p style="margin-bottom:0;">
                                Regards,<br>
                                <strong>IGF Link Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f9fafb; padding:15px; text-align:center; font-size:12px; color:#9ca3af;">
                            © {{ date('Y') }} IGF Link. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
