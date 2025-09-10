<?php

namespace App\Services;

use App\Models\User;
use App\Services\Support\ServiceResponse;
use Illuminate\Support\Str;
use App\Mail\AccountCreated;
use App\Models\SocialAccount;
use App\Mail\EmailVerification;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    public function __construct(readonly protected FirebaseAuthService $firebase) {}

    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return new ServiceResponse(['message' => 'Invalid credentials'], 401);
        }

        if (! $user->email_verified_at) {
            $this->sendEmailVerificationCode($user);
            return new ServiceResponse(
                ['message' => 'Email not verified. A new verification code has been sent to your email.'],
                403
            );
        }

        return $this->respondWithToken($user, 'Login successful');
    }

    public function sLogin(string $authToken)
    {
        $userData = $this->firebase->getUserData($authToken);

        if (! $userData) {
            return new ServiceResponse(['message' => 'Invalid Firebase token'], 401);
        }

        $account = SocialAccount::where($userData['social'])->first();

        if ($account) {
            return $this->respondWithToken($account->user, 'Login successful');
        }

        // Find or create user (since social account does not exist)
        $user = User::firstOrCreate(
            ['email' => $userData['email']],
            [
                'name'              => $userData['name'] ?? $userData['email'],
                'password'          => bcrypt(Str::random(24)),
                'avatar'            => $userData['avatar'] ?? null,
                'firebase_uid'      => $userData['fb_uid'],
                'email_verified_at' => now(),
            ]
        );

        // Linking of social account to newly created user
        $user->socialAccounts()->create($userData['social']);

        if ($user->wasRecentlyCreated) {
            Mail::to($user->email)->send(new AccountCreated($user->name));
            return $this->respondWithToken($user, 'Registration successful');
        }

        if (! $user->email_verified_at && $userData['email_verified']) {
            $user->email_verified_at = now();
        }

        if (! $user->avatar && $userData['avatar']) {
            $user->avatar = $userData['avatar'];
        }

        $user->save();

        return $this->respondWithToken($user, 'Login successful');
    }

    public function register(array $data)
    {
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        $this->sendEmailVerificationCode($user);

        return $this->respondWithToken($user, 'Registration successful. Check your email for verification code');
    }

    public function logout(User $user, string $all)
    {
        if ($all === 'all') {
            $user->tokens()->delete();
            return new ServiceResponse(['message' => 'Logged out from all devices']);
        }

        $user->currentAccessToken->delete();

        return new ServiceResponse(['message' => 'Logged out successfully']);
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

    protected function respondWithToken(User $user, string $message)
    {
        return new ServiceResponse([
            'message' => $message,
            'user'    => new UserResource($user),
            'token'   => [
                'access' => $user->createToken('auth_token')->plainTextToken,
                'type'   => 'Bearer',
            ],

        ], 201);
    }
}
