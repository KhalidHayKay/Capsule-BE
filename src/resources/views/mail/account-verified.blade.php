@extends('mail.layouts.auth')

@section('title')
    Email verified successfully 🎉
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
        <td style="padding: 20px 30px;">
            <p style="margin: 20px 0 0; font-size: 15px; line-height: 1.6;">
                Great news — your email has been verified!
                You can now enjoy the full features of <strong>{{ config('app.name') }}</strong>.
            </p>
        </td>
    </tr>

    <tr>
        <td style="padding: 0 30px 30px;">
            <p style="font-size: 14px; line-height: 1.5; color: #555;">
                If you didn’t perform this action, please secure your account immediately.</p>
            </p>
        </td>
    </tr>
@endsection
