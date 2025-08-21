<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\FirebaseAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request as RequestFacade;

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
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'user'    => new UserResource($user),
            'token'   => $this->makeToken($user)->plainTextToken,
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
                'name'          => $userData['name'] ?? $userData['email'],
                'password'      => bcrypt(str()->random(24)),
                'avatar'        => $userData['avatar'] ?? null,

                'firebase_uid'  => $userData['uid'],
                'auth_provider' => $userData['provider'],
            ]
        );

        $newlyCreated = $user->wasRecentlyCreated;

        return response()->json([
            'message' => $newlyCreated ? 'User registered successfully' : 'Login successful',
            'user'    => new UserResource($user),
            'token'   => $this->makeToken($user)->plainTextToken,
        ], 201);
    }

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => new UserResource($user),
            'token'   => $this->makeToken($user)->plainTextToken,
        ], 201);
    }

    public function logout(Request $request, $all): JsonResponse
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

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => new UserResource($user),
        ], 200);
    }

    public function makeToken(User $user)
    {
        $requestAgent = RequestFacade::header('User-Agent') ?? 'auth-token';

        $user->tokens()->where('name', $requestAgent)->delete();

        return $user->createToken($requestAgent);
    }
}
