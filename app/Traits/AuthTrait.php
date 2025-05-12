<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait AuthTrait
{

    /**
     * Register a user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => $e->errors()
            ], 422);
        }
        if (auth()->attempt($request->only('email', 'password'))) {
            $user = auth()->user();
            $roles = $user->getRoleNames();
            $user["role"] = $roles->first() ?? "";
            return response()->json([
                'success' => true,
                'message' => 'Welcome Back ' . $user->name,
                'access_token' => $user->createToken($request->user()->email)->plainTextToken,
                'token_type' => 'Bearer',
                'data' => $user,
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
            'data' => []
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout success',
            'data' => []
        ]);
    }

    public function currentUser(Request $request): JsonResponse
    {
        $user = $request->user();
        $roles = $user->getRoleNames();
        $user["two_steps_auth"] = FALSE;
        $user["role"] = $roles;

        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully.',
            'data' => $user,
        ]);
    }
}
