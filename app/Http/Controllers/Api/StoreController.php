<?php

namespace App\Http\Controllers\Api;

use App\Models\Merchant;
use App\Models\Store;
use App\Enums\Store\VerificationStatus;
use App\Enums\Store\VisiblityStatus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\PermissionMiddleware;

class StoreController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(PermissionMiddleware::class . ':update store')->only(['update']);
    }

    private function validateMerchant($merchant): array
    {
        $success = true;
        $message = '';
        if (!$merchant) {
            $success = false;
            $message = 'Merchant not found.';
        }
        if ($merchant->verification_status != VerificationStatus::Approved->value) {
            $success = false;
            $message = 'Merchant not verified.';
        }
        return [
            'success' => $success,
            'message' => $message,
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $query = Store::query();
        $perPage = $request->input('per_page', 15); // Default to 15 items per page if not provided

        if ($request->filled('query')) {
            $searchQuery = $request->input('query');
            $query->where(function ($query) use ($searchQuery) {
                $query->where('name', 'like', '%' . $searchQuery . '%');
                $query->where('address', 'like', '%' . $searchQuery . '%');
                $query->where('city', 'like', '%' . $searchQuery . '%');
                $query->where('tax_number', 'like', '%' . $searchQuery . '%');
            });
        }

        if ($request->filled('sort') && $request->filled('order')) {
            $query->orderBy($request->input('sort'), $request->input('order'));
        }

        $stores = $query->with('merchant')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Stores retrieved successfully.',
            'data' => $stores
        ]);
    }

    public function show($id): JsonResponse
    {
        try {
            $store = Store::with('merchant')->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Store retrieved successfully.',
                'data' => $store
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Not Found. Store with id {$id} not found.",
                'data' => []
            ]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $merchant = $this->currentMerchant($request->user()->id);
        $res = $this->validateMerchant($merchant);
        if (!$res['success']) {
            return response()->json([
                'success' => false,
                'message' => $res['message'],
                'data' => []
            ]);
        }
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'landmark' => 'nullable|string|max:255',
                'state' => 'required|string|max:255',
                'zipcode' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'country_code' => 'required|string|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'contact_email' => 'required|string|email|max:50',
                'contact_number' => 'required|integer',
                'description' => 'nullable|string',
                'store_url' => 'nullable|string|max:255',
                'timing_varchar' => 'nullable|string|max:500',
                'address_proof' => 'required|string',
                'voided_check' => 'required|string',
                'tax_name' => 'required|string|max:255',
                'tax_number' => 'required|string|max:255',
                'bank_name' => 'required|string|max:255',
                'bank_branch_code' => 'required|string|max:255',
                'account_holder_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:255',
                'routing_number' => 'required|string|max:255',
                'bank_account_type' => 'required|in:checking,savings',
                'currency_code' => 'required|string|max:255',
                'permissions' => 'nullable|string',
                'pickup_from_store' => 'boolean',
                'home_delivery' => 'boolean',
                'shipping' => 'boolean',
                'in_store_purchase' => 'boolean',
                'time_slot_config' => 'nullable|string',
                'max_delivery_distance' => 'nullable|numeric',
                'shipping_min_free_delivery_amount' => 'nullable|numeric',
                'shipping_charge_priority' => 'nullable|string|max:255',
                'allowed_order_per_time_slot' => 'nullable|integer',
                'order_preparation_time' => 'nullable|integer',
                'pickup_time_schedule_config' => 'nullable|string',
                'carrier_partner' => 'nullable|string',
                'promotional_text' => 'nullable|string|max:1024',
                'restocking_percentage' => 'nullable|numeric',
                'shopify' => 'boolean',
                'shopify_settings' => 'nullable|string',
                'woocommerce' => 'boolean',
                'woocommerce_settings' => 'nullable|string',
                'etsy' => 'boolean',
                'etsy_settings' => 'nullable|string',
                'about_us' => 'nullable|string',
                'return_replacement_policy' => 'nullable|string',
                'refund_policy' => 'nullable|string',
                'terms_and_condition' => 'nullable|string',
                'delivery_policy' => 'nullable|string',
                'shipping_preference' => 'nullable|string',
                'domestic_shipping_charges' => 'nullable|numeric',
                'international_shipping_charges' => 'nullable|numeric',
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
            ]);
            $validatedData['merchant_id'] = $merchant->id;
            $validatedData['slug'] = createUniqueSlug($validatedData['name'], 'Store');
            $store = Store::create($validatedData);
            return response()->json([
                'success' => true,
                'message' => 'Store Added successfully. now Wait for admin approval.',
                'data' => $store
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $store = Store::with('merchant')->find($id);
        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found.',
                'data' => []
            ]);
        }
        try {
            $this->authorize('update', $store);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to Update this store.',
                'data' => []
            ]);
        }

        try {
            $validatedData = $request->validate([
                'name' => 'string|max:255',
                'address' => 'string|max:255',
                'city' => 'string|max:255',
                'landmark' => 'nullable|string|max:255',
                'state' => 'string|max:255',
                'zipcode' => 'string|max:255',
                'country' => 'string|max:255',
                'country_code' => 'string|max:255',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'contact_email' => 'string|email|max:50',
                'contact_number' => 'integer',
                'description' => 'nullable|string',
                'store_url' => 'nullable|string|max:255',
                'timing_varchar' => 'nullable|string|max:500',
                'address_proof' => 'string',
                'voided_check' => 'string',
                'tax_name' => 'string|max:255',
                'tax_number' => 'string|max:255',
                'bank_name' => 'string|max:255',
                'bank_branch_code' => 'string|max:255',
                'account_holder_name' => 'string|max:255',
                'account_number' => 'string|max:255',
                'routing_number' => 'string|max:255',
                'bank_account_type' => 'in:checking,savings',
                'currency_code' => 'string|max:255',
                'permissions' => 'nullable|string',
                'pickup_from_store' => 'boolean',
                'home_delivery' => 'boolean',
                'shipping' => 'boolean',
                'in_store_purchase' => 'boolean',
                'time_slot_config' => 'nullable|string',
                'max_delivery_distance' => 'nullable|numeric',
                'shipping_min_free_delivery_amount' => 'nullable|numeric',
                'shipping_charge_priority' => 'nullable|string|max:255',
                'allowed_order_per_time_slot' => 'nullable|integer',
                'order_preparation_time' => 'nullable|integer',
                'pickup_time_schedule_config' => 'nullable|string',
                'carrier_partner' => 'nullable|string',
                'promotional_text' => 'nullable|string|max:1024',
                'restocking_percentage' => 'nullable|numeric',
                'shopify' => 'boolean',
                'shopify_settings' => 'nullable|string',
                'woocommerce' => 'boolean',
                'woocommerce_settings' => 'nullable|string',
                'etsy' => 'boolean',
                'etsy_settings' => 'nullable|string',
                'about_us' => 'nullable|string',
                'return_replacement_policy' => 'nullable|string',
                'refund_policy' => 'nullable|string',
                'terms_and_condition' => 'nullable|string',
                'delivery_policy' => 'nullable|string',
                'shipping_preference' => 'nullable|string',
                'domestic_shipping_charges' => 'nullable|numeric',
                'international_shipping_charges' => 'nullable|numeric',
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
            ]);
            $validatedData['slug'] = createUniqueSlug($validatedData['name'], 'Store', $store->id);
            $store->update($validatedData);
            return response()->json([
                'success' => true,
                'message' => 'Store Updated successfully.',
                'data' => $store
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $store = Store::find($id);
            if (!$store) {
                return response()->json([
                    'success' => false,
                    'message' => 'Store not found.',
                    'data' => []
                ], 404);
            }
            try {
                $this->authorize('delete', $store);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to Delete this store.',
                    'data' => []
                ]);
            }
            $user_id = Auth::user()->id ?? 0;
            $merchant = $this->currentMerchant($user_id);
            if ($store->merchant_id != $merchant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to Delete this store.',
                    'data' => []
                ]);
            }
            $store->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Merchant deleted successfully.',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function availableStores(Request $request): JsonResponse
    {
        $limit = $request->query('limit', 10);
        $offset = $request->query('offset', 0);

        $stores = Store::where('verification_status', VerificationStatus::Approved->value)
            ->where('visiblity_status', VisiblityStatus::Visible->value)
            ->with('merchant')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Available stores retrieved successfully.',
            'data' => $stores
        ]);
    }

    public function statuses(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Statuses retrieved successfully.',
            'data' => [
                'verification_statuses' => VerificationStatus::cases(),
                'visibility_statuses' => VisiblityStatus::cases()
            ]
        ]);
    }

    public function changeVerificationStatus(Request $request, $id): JsonResponse
    {
        $store = Store::findOrFail($id);

        try {
            $validatedData = $request->validate([
                'verification_status' => ['required', new Enum(VerificationStatus::class)],
            ]);

            $store->verification_status = $validatedData['verification_status'];
            $store->save();

            return response()->json([
                'success' => true,
                'message' => 'Store verification status updated successfully.',
                'data' => $store
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function currentMerchant($user_id)
    {
        return Merchant::where('user_id', $user_id)->first();
    }
}
