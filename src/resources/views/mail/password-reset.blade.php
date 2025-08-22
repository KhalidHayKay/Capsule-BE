@extends('mail.layouts.auth')

@section('title')
    problem signing in?
@endsection

@section('content')
    <tr>
        <td style="padding: 5px 30px;">
            <p style="margin: 0; font-size: 16px; line-height: 1.5;">
                Hey {{ $name ?? 'there' }},
            </p>
        </td>
    </tr>
    <tr>
        <td style="padding: 10px 30px;">
            <p style="margin: 20px 0 0; font-size: 15px; line-height: 1.6;">
                We received a request to reset your password for your {{ config('app.name') }} account.
            </p>
        </td>
    </tr>

    <tr>
        <td align="center" style="padding: 5px 30px;">
            <p style="font-size: 18px; font-weight: bold; letter-spacing: 2px;">
                Use the code below to reset your password.
            </p>
            <p style="font-size: 24px; font-weight: bold; color: #4C83EE;">
                {{ $code }}
            </p>
            <p style="font-size: 14px; font-weight: bold;">
                Code valid for the next {{ $expiresIn ?? 20 }} minutes.
            </p>
        </td>
    </tr>

    <tr>
        <td style="padding: 0 30px 30px;">
            <p style="font-size: 14px; line-height: 1.5; color: #555;">
                If you didn’t request this, you can safely ignore this email. Your password won’t change unless you use the
                code above.
            </p>
        </td>
    </tr>
@endsection
