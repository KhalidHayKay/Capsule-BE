<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>You're on the Waitlist!</title>
</head>

<body style="margin:0; padding:0; background-color:#f6f6f6; font-family:Arial, sans-serif; color:#333333;">

    <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f6f6f6">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                    style="margin: 40px auto; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 40px 30px 20px;">
                            <h2 style="margin: 0 0 10px; font-size: 24px; color: #4C83EE;">You're on the Waitlist! 🎉
                            </h2>
                            <p style="margin: 0; font-size: 16px; line-height: 1.5;">
                                Hey {{ $name ?? 'there' }},
                            </p>
                            <p style="margin: 20px 0 0; font-size: 15px; line-height: 1.6;">
                                Thanks for joining the waitlist for <strong>{{ config('app.name') }}</strong>. We’re
                                excited to have you on board.
                                You'll be among the first to know when we go live!
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 20px 30px;">
                            <a href="{{ config('app.url') }}"
                                style="display: inline-block; background-color: #4C83EE; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold;">
                                Visit Our Website
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <p style="font-size: 14px; line-height: 1.5; color: #555;">
                                Want to help us grow? Share our website with your friends or invite others to join —
                                every referral helps!
                            </p>

                            @isset($referralLink)
                                <p style="margin-top: 10px; font-size: 14px;">
                                    Your unique referral link:<br>
                                    <a href="{{ $referralLink }}"
                                        style="color: #4C83EE; word-break: break-all;">{{ $referralLink }}</a>
                                </p>

                                <p style="margin-top: 10px; font-size: 14px; color: #555;">
                                    Share via:
                                    <br>
                                    <a href="https://wa.me/?text={{ urlencode('Join me on this amazing platform! ' . $referralLink) }}"
                                        style="text-decoration: none; margin-right: 15px;">
                                        <img src="https://cdn-icons-png.flaticon.com/16/733/733585.png" alt="WhatsApp"
                                            style="vertical-align: middle; margin-right: 5px;">
                                        WhatsApp
                                    </a>

                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($referralLink) }}"
                                        style="text-decoration: none; margin-right: 15px;">
                                        <img src="https://cdn-icons-png.flaticon.com/16/733/733547.png" alt="Facebook"
                                            style="vertical-align: middle; margin-right: 5px;">
                                        Facebook
                                    </a>

                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($referralLink) }}&text={{ urlencode('Join me on this amazing platform!') }}"
                                        style="text-decoration: none;">
                                        <img src="https://cdn-icons-png.flaticon.com/16/733/733579.png" alt="Twitter"
                                            style="vertical-align: middle; margin-right: 5px;">
                                        Twitter
                                    </a>
                                </p>
                            @endisset
                        </td>
                    </tr>


                    <tr>
                        <td style="padding: 0 30px 40px; font-size: 13px; color: #999999;">
                            <hr style="border: none; border-top: 1px solid #eeeeee; margin: 30px 0;">
                            <p style="margin: 0;">Thanks again, <br>The {{ config('app.name') }} Team</p>
                        </td>
                    </tr>
                </table>

                <p style="font-size: 11px; color: #aaa; margin-top: 20px;">
                    This message was sent from {{ config('app.name') }} • {{ config('app.url') }}
                </p>
            </td>
        </tr>
    </table>

</body>

</html>
