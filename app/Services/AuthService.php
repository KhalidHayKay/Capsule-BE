<?php

namespace App\Services;

use App\Models\User;
use App\Mail\AccountCreated;
use App\Models\SocialAccount;
use App\Mail\EmailVerification;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Builder;

class AuthService
{
    public function __construct(readonly protected FirebaseAuthService $firebase) {}

    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials',], 401);
        }

        if (! $user->email_verified_at) {
            $this->sendEmailVerificationCode($user);

            return ['message' => 'Email not verified. Check your email for verification code'];
        }

        return [
            'message' => 'Login successful',
            'user'    => new UserResource($user),
            'token'   => $user->makeToken()->plainTextToken,
        ];
    }

    public function sLogin(string $authToken)
    {
        $userData = $this->firebase->getUserData($authToken);

        if (! $userData) {
            return response()->json(['message' => 'Invalid Firebase token'], 401);
        }

        // Step 1: Check if social account exists
        $account = SocialAccount::firstOrcreate(
            [
                'provider_id'   => $userData['uid'],
                'provider_name' => $userData['provider_name'],
            ],
            [
                'updated_at' => now(),
            ]
        );

        if ($account->wasRecentlyCreated) {
            $user = $account->user->firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'              => $userData['name'] ?? $userData['email'],
                    'password'          => bcrypt(Str::random(24)), // random password for fallback
                    'avatar'            => $userData['avatar'] ?? null,
                    'firebase_uid'      => $userData['uid'],
                    'auth_provider'     => $userData['provider'],
                    'email_verified_at' => now(),
                ]
            );

        } else {
            $user         = $account->user;
            $newlyCreated = false;

            $newlyCreated = $user->wasRecentlyCreated;

            // Step 3: Link the social account
            $user->socialAccounts()->updateOrCreate(
                [
                    'provider_name' => $userData['provider_name'],
                    'provider_id'   => $userData['uid'],
                ],
                ['updated_at' => now()]
            );

            // Optional: send account created email
            if ($newlyCreated) {
                Mail::to($user->email)->send(new AccountCreated($user->name));
            }
        }

        // Step 4: Return token
        return [
            'message' => $newlyCreated ? 'User registered successfully' : 'Login successful',
            'user'    => new UserResource($user),
            'token'   => $user->createToken()->plainTextToken->plainTextToken,
        ];

    }

    public function register(array $data)
    {
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        $this->sendEmailVerificationCode($user);

        return [
            'message' => 'User registered successfully. Check email for verification code',
            'user'    => new UserResource($user),
            'token'   => $user->makeToken()->plainTextToken,
        ];
    }

    public function logout(User $user, string $all)
    {
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($all === 'all') {
            $user->tokens()->delete();

            return ['message' => 'Logged out from all devices'];
        }

        $user->currentAccessToken->delete();

        return ['message' => 'Logged out successfully'];
    }

    private function sendEmailVerificationCode(User $user)
    {
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

        if ($user->wasRecentlyCreated) {
            Mail::to($user->email)->send(new AccountCreated(
                $user->name,
                $code
            ));
        } else {
            Mail::to($user->email)->send(new EmailVerification(
                $user->name,
                $code
            ));
        }

        return $code;
    }
}
