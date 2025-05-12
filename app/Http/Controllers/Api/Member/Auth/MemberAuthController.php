<?php

namespace App\Http\Controllers\Api\Member\Auth;

use App\Http\Controllers\Controller;
use App\Interfaces\AuthInterface;
use App\Models\User;
use App\Traits\AuthTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Nette\Utils\Random;

class MemberAuthController extends Controller implements AuthInterface
{
    use AuthTrait;
    public function register(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'mobile' => 'required|unique:users',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => $e->errors()
            ], 422);
        }
        $referralCode = str()->random(6);
        $request->merge(['referral_code' => $referralCode, 'status' => 'active']);
        $user = User::create($request->all());
        if ($user) {
            $user->assignRole('member');
            return response()->json([
                'success' => true,
                'message' => 'Welcome to ZinZuu ' . $user->name,
                'data' => $user,
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'data' => []
        ]);
    }
}
