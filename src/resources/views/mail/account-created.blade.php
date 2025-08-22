@extends('mail.layouts.auth')

@section('title')
    Welcome to {{ config('app.name') }} 🎉
@endsection

@section('content')
    <tr>
        <td style="padding: 5px 30px;">
            <p style="margin: 0; font-size: 16px; line-height: 1.5;">
                Hey {{ $name ?? 'there' }},
            </p>
        </td>
    </tr>

    @if ($code ?? false)
        {{-- If email verification is required --}}
        <tr>
            <td style="padding: 10px 30px;">
                <p style="margin: 20px 0 0; font-size: 15px; line-height: 1.6;">
                    Thanks for signing up! Please verify your email address to activate your account.
                </p>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding: 5px 30px;">
                <p style="font-size: 18px; font-weight: bold; letter-spacing: 2px;">
                    Your verification code is:
                </p>
                <p style="font-size: 24px; font-weight: bold; color: #4C83EE;">
                    {{ $code }}
                </p>
                <p style="font-size: 14px; font-weight: bold;">
                    Code valid for the next {{ $expiresIn ?? 20 }} minutes.
                </p>
            </td>
        </tr>
    @else
        {{-- If account created via social login (already verified) --}}
        <tr>
            <td style="padding: 20px 30px;">
                <p style="margin: 20px 0 0; font-size: 15px; line-height: 1.6;">
                    Your account has been successfully created.
                    You can now enjoy the full features of <strong>{{ config('app.name') }}</strong>.
                </p>
            </td>
        </tr>
    @endif

    <tr>
        <td style="padding: 0 30px 30px;">
            <p style="font-size: 14px; line-height: 1.5; color: #555;">
                If you didn’t create an account with us, you can safely ignore this email.
            </p>
        </td>
    </tr>
@endsection
