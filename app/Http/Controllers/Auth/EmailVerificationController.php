<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\EmailVerification;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Mail\AccountVerified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|min:6|max:6',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        $record = DB::table('email_verification_tokens')
            ->where('user_id', $user->id)->firstOrFail();

        if (! $record || ! Hash::check($request->code, $record->token)) {
            return response()->json(['message' => 'Invalid code'], 400);
        }

        if (now()->greaterThan($record->expires_at)) {
            return response()->json(['message' => 'Code expired'], 400);
        }

        $user->update(['email_verified_at' => now()]);

        Mail::to($user->email)->send(new AccountVerified($user->name));

        DB::table('email_verification_tokens')->where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Email verified successfully',
            'user'    => new UserResource($user),
            'token'   => $user->makeToken()->plainTextToken,
        ]);
    }

    public function code(Request $request)
    {
        $data = $request->validate(['email' => 'required|email',]);

        $user = User::where('email', $data['email'])->firstOrFail();

        $code = rand(111111, 999999);

        DB::table('email_verification_tokens')
            ->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'token'      => Hash::make($code),
                    'expires_at' => now()->addMinutes(20),
                    'updated_at' => now(),
                ]
            );

        Mail::to($user->email)->send(new EmailVerification(
            $user->name,
            $code
        ));

        return response()->json(['message' => 'Verification code sent succesfully']);
    }
}
