@extends('mail.layouts.auth')

@section('title')
    One more step 🚀
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
                please verify your email address to get started with {{ config('app.name') }}.
            </p>
        </td>
    </tr>
    {{-- Uncomment below for a clickable link instead of code --}}
    {{--
        <tr>
            <td align="center" style="padding: 20px 30px;">
                <a href="{{ $verificationUrl }}"
                    style="display: inline-block; background-color: #4C83EE; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold;">
                    Verify Email
                </a>
            </td>
        </tr>
        --}}
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

    <tr>
        <td style="padding: 0 30px 30px;">
            <p style="font-size: 14px; line-height: 1.5; color: #555;">
                If you didn’t create an account with us, you can safely ignore this email.
            </p>
        </td>
    </tr>
@endsection
