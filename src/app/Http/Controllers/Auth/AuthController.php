<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
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

    public function socialLogin(Request $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::firstOrCreate(
            [
                'provider'    => $validated['provider'],
                'provider_id' => $validated['provider_id'],
            ],
            [
                'email'  => $validated['email'] ?? null,
                'name'   => $validated['name'] ?? 'Unknown',
                'avatar' => $validated['avatar'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Login successful',
            'user'    => new UserResource($user),
            'token'   => $this->makeToken($user)->plainTextToken,
        ], 201);
    }

    public function logout(Request $request, $all = null): JsonResponse
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

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

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
