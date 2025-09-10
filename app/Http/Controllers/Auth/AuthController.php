<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;

class AuthController extends Controller
{
    public function __construct(readonly protected AuthService $service) {}

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $response = $this->service->login($data);

        return response()->json($response->data, $response->code);
    }

    public function socialLogin(Request $request)
    {
        $request->validate(['firebase_token' => 'required|string']);

        $response = $this->service->sLogin($request->firebase_token);

        return response()->json($response->data, $response->code);
    }

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $response = $this->service->register($data);

        return response()->json($response->data, $response->code);
    }

    public function logout(Request $request, string|null $all = null): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $response = $this->service->logout($user, $all);

        return response()->json($response->data, $response->code);
    }
}
