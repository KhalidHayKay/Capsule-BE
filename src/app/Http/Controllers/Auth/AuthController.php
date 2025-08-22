<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\EmailVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Services\FirebaseAuthService;
use App\Http\Requests\RegisterUserRequest;
use App\Mail\AccountCreated;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials',], 401);
        }

        if (! $user->email_verified_at) {
            $this->sendEmailVerificationCode($user);

            return response()->json([
                'message' => 'Email not verified. Check your email for verification code',
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'user'    => new UserResource($user),
            'token'   => $user->makeToken()->plainTextToken,
        ]);
    }

    public function socialLogin(Request $request, FirebaseAuthService $firebase)
    {
        $request->validate([
            'firebase_token' => 'required|string',
        ]);

        $userData = $firebase->getUserData($request->firebase_token);

        if (! $userData) {
            return response()->json(['message' => 'Invalid Firebase token'], 401);
        }

        $user = User::firstOrCreate(
            ['email' => $userData['email']],
            [
                'name'              => $userData['name'] ?? $userData['email'],
                'password'          => bcrypt(str()->random(24)),
                'avatar'            => $userData['avatar'] ?? null,
                'firebase_uid'      => $userData['uid'],
                'auth_provider'     => $userData['provider'],
                'email_verified_at' => now(),
            ]
        );

        $newlyCreated = $user->wasRecentlyCreated;

        if ($newlyCreated) {
            Mail::to($user->email)->send(new AccountCreated($user->name));
        }

        return response()->json([
            'message' => $newlyCreated ? 'User registered successfully' : 'Login successful',
            'user'    => new UserResource($user),
            'token'   => $user->makeToken()->plainTextToken,
        ], 201);
    }

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        $this->sendEmailVerificationCode($user);

        return response()->json([
            'message' => 'User registered successfully. Check email for verification code',
            'user'    => new UserResource($user),
            'token'   => $user->makeToken()->plainTextToken,
        ], 201);
    }

    public function logout(Request $request, string|null $all = null): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($all === 'all') {
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Logged out from all devices',
            ], 200);
        }

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
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
