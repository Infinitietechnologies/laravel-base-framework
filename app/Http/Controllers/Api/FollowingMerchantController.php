<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FollowingMerchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowingMerchantController extends Controller
{
    public function index()
    {
        $followingMerchants = FollowingMerchant::all();

        return response()->json([
            'success' => true,
            'message' => 'Following Merchants retrieved successfully',
            'data' => $followingMerchants
        ]);
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
        ]);

        $existingFollowing = FollowingMerchant::where('user_id', $userId)
            ->where('merchant_id', $request->merchant_id)
            ->first();

        if ($existingFollowing) {
            return response()->json([
                'success' => false,
                'message' => 'You are already following this merchant',
                'errors' => []
            ], 400);
        }

        try {
            $followingMerchant = FollowingMerchant::create([
                'user_id' => $userId,
                'merchant_id' => $request->merchant_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Following Merchant created successfully',
                'data' => $followingMerchant
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'There was an error creating the Following Merchant',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $followingMerchant = FollowingMerchant::find($id);

        if (!$followingMerchant) {
            return response()->json([
                'success' => false,
                'message' => 'Following Merchant not found',
                'errors' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Following Merchant retrieved successfully',
            'data' => $followingMerchant
        ]);
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();

        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
        ]);

        $followingMerchant = FollowingMerchant::where('id', $id)->where('user_id', $userId)->first();

        if (!$followingMerchant) {
            return response()->json([
                'success' => false,
                'message' => 'Following Merchant not found or you do not have permission to update it',
                'errors' => []
            ], 404);
        }

        try {
            $followingMerchant->update([
                'merchant_id' => $request->merchant_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Following Merchant updated successfully',
                'data' => $followingMerchant
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'There was an error updating the Following Merchant',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $followingMerchant = FollowingMerchant::where('id', $id)->where('user_id', $userId)->first();

        if (!$followingMerchant) {
            return response()->json([
                'success' => false,
                'message' => 'Following Merchant not found or you do not have permission to delete it',
                'errors' => []
            ], 404);
        }

        try {
            $followingMerchant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Following Merchant deleted successfully',
                'data' => []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'There was an error deleting the Following Merchant',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function getByUserId()
    {
        $userId = Auth::id();
        $followingMerchants = FollowingMerchant::where('user_id', $userId)->get();

        if ($followingMerchants->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Following Merchants found for this user',
                'errors' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Following Merchants retrieved successfully',
            'data' => $followingMerchants
        ]);
    }
}
