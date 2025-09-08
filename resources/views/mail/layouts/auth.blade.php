<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'Notification' }}</title>
</head>

<body style="margin:0; padding:0; background-color:#f6f6f6; font-family:Arial, sans-serif; color:#333333;">

    <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f6f6f6">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                    style="margin: 40px auto; border-radius: 8px; overflow: hidden;">

                    <!-- Header -->
                    <tr>
                        <td align="center" bgcolor="#4C83EE" style="padding: 20px 30px;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 30px;">
                            <h2>@yield('title')</h2>
                        </td>
                    </tr>

                    <!-- Content (customizable per email type) -->
                    <tr>
                        @yield('content')
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; font-size: 13px; color: #999999; text-align: center;">
                            <hr style="border: none; border-top: 1px solid #eeeeee; margin: 20px 0;">
                            <p style="margin: 0;">Thanks, <br>The {{ config('app.name') }} Team</p>
                        </td>
                    </tr>
                </table>

                <!-- Bottom note -->
                <p style="font-size: 11px; color: #aaa; margin-top: 20px;">
                    This message was sent by {{ config('app.name') }} • {{ config('app.url') }}
                </p>
            </td>
        </tr>
    </table>

</body>

</html>
