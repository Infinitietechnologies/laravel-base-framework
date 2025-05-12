<?php

namespace App\Http\Controllers\Api;

use App\Enums\Merchant\MerchantVerificationStatusEnum;
use App\Enums\Merchant\MerchantVisiblityStatusEnum;
use App\Models\Merchant;
use App\Models\User;
use Dedoc\Scramble\Attributes\BodyParameter;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\PermissionMiddleware;

class MerchantController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'getAvailableMerchants']);
        $this->middleware(PermissionMiddleware::class . ':update merchant')->only(['update']);
        $this->middleware(PermissionMiddleware::class . ':delete merchant')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Merchant::query();
        $perPage = $request->input('per_page', 15); // Default to 15 items per page if not provided

        if ($request->filled('query')) {
            $searchQuery = $request->input('query');
            $query->where('name', 'LIKE', '%' . $searchQuery . '%')
                ->orWhereHas('user', function ($q) use ($searchQuery) {
                    $q->where('mobile', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('email', 'LIKE', '%' . $searchQuery . '%');
                });
        }

        if ($request->filled('status')) {
            $query->where('verification_status', $request->input('status'));
        }

        if ($request->filled('sort') && $request->filled('order')) {
            $query->orderBy($request->input('sort'), $request->input('order'));
        }

        $merchants = $query->with(['user','countryDetails'])->paginate($perPage);
        return response()->json([
            'success' => true,
            'message' => 'Merchants retrieved successfully.',
            'data' => $merchants
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @requestMediaType multipart/form-data
     */
    #[BodyParameter('user_id', description: 'Add user id. if user already exist.', required: false, type: 'number', example: '')]
    #[BodyParameter('address', description: 'Address of the location.', required: true, type: 'string', example: '123 Main St')]
    #[BodyParameter('city', description: 'City of the location.', required: true, type: 'string', example: 'New York')]
    #[BodyParameter('state', description: 'State of the location.', required: true, type: 'string', example: 'NY')]
    #[BodyParameter('zipcode', description: 'Zipcode of the location.', required: true, type: 'string', example: '10001')]
    #[BodyParameter('country', description: 'Country of the location.', required: true, type: 'string', example: 'USA')]
    #[BodyParameter('country_code', description: 'Country code of the location.', required: true, type: 'string', example: 'US')]
    #[BodyParameter('latitude', description: 'Latitude of the location.', required: false, type: 'string', example: '40.7128')]
    #[BodyParameter('longitude', description: 'Longitude of the location.', required: false, type: 'string', example: '-74.0060')]
    #[BodyParameter('pricing_template_id', description: 'Pricing template ID.', required: false, type: 'string', example: 'template_123')]
    #[BodyParameter('business_license', description: 'Business license of the location.', required: true, type: 'file', example: 'signature_123')]
    #[BodyParameter('articles_of_incorporation', description: 'Articles of incorporation.', required: true, type: 'file', example: 'articles_123')]
    #[BodyParameter('national_identity_card', description: 'National identity card.', required: true, type: 'file', example: 'id_123')]
    #[BodyParameter('authorized_signature', description: 'Authorized signature.', required: true, type: 'file', example: 'signature_123')]
    #[BodyParameter('meta_title', description: 'Meta title for SEO.', required: false, type: 'string', example: 'Meta Title')]
    #[BodyParameter('meta_keywords', description: 'Meta keywords for SEO.', required: false, type: 'string', example: 'keyword1, keyword2')]
    #[BodyParameter('meta_description', description: 'Meta description for SEO.', required: false, type: 'string', example: 'Meta Description')]
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = null;
            if ($request->has('user_id')) {
                $user = User::find($request->user_id);
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found.',
                        'data' => []
                    ]);
                }
                $is_merchant_exist = Merchant::where('user_id', $user->id)->exists();
                if ($is_merchant_exist) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Merchant already exists.',
                        'data' => []
                    ]);
                }
            }
            if (!$user) {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'mobile' => 'required|string|unique:users',
                    'password' => 'required',
                ]);
                $validated['status'] = "active";
                $user = User::create($validated);
            }
            $validated = $request->validate([
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'zipcode' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'country_code' => 'required|string|max:255',
                'latitude' => 'nullable|string|max:255',
                'longitude' => 'nullable|string|max:255',
                'pricing_template_id' => 'nullable|string|max:255',
                'business_license' => 'required|string|max:255',
                'articles_of_Incorporation' => 'required|string|max:255',
                'national_identity_card' => 'required|string|max:255',
                'authorized_signature' => 'required|string|max:255',
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:255',
            ]);

            if ($user) {
                $user->assignRole('merchant');
                $validated += [
                    'slug' => createUniqueSlug($user->name, 'Merchant', $user->id),
                    'user_id' => $user->id,
                    'uuid' => (string)Str::uuid(),
                    'name' => $user->name,
                    'verification_status' => "not_approved",
                    'visibility_status' => "draft",
                ];
                $merchant = Merchant::create($validated);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Merchant created successfully.',
                    'data' => $merchant
                ]);
            }

            throw new Exception('User creation failed');

        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'data' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $merchant = Merchant::with('user')->find($id);
        if (!$merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant not found.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Merchant retrieved successfully.',
            'data' => $merchant
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    #[BodyParameter('name', description: 'user name', required: false, type: 'string', example: 'John')]
    #[BodyParameter('mobile', description: 'mobile number', required: false, type: 'number', example: '8787877878')]
    public function update(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $merchant = Merchant::find($id);
            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Merchant not found.',
                    'data' => []
                ], 404);
            }
            try {
                // Authorize
                $this->authorize('update', $merchant);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to Update this merchant.',
                    'data' => []
                ]);
            }
            $user_id = $merchant->user_id;
            $user = User::find($user_id);
            $validated = $request->validate([
                'address' => 'sometimes|required|string|max:255',
                'city' => 'sometimes|required|string|max:255',
                'state' => 'sometimes|required|string|max:255',
                'zipcode' => 'sometimes|required|string|max:255',
                'country' => 'sometimes|required|string|max:255',
                'country_code' => 'sometimes|required|string|max:255',
                'latitude' => 'nullable|string|max:255',
                'longitude' => 'nullable|string|max:255',
                'pricing_template_id' => 'nullable|string|max:255',
                'business_license' => 'sometimes|required|string|max:255',
                'articles_of_Incorporation' => 'sometimes|required|string|max:255',
                'national_identity_card' => 'sometimes|required|string|max:255',
                'authorized_signature' => 'sometimes|required|string|max:255',
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:255',
            ]);
            $user_validate = $request->validate([
                'name' => 'nullable|string|max:255',
                'mobile' => 'nullable|string|unique:users,mobile,' . $user_id,
            ]);
            $user->update($user_validate);
            $merchant->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Merchant updated successfully.',
                'data' => $merchant
            ]);
        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'data' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $merchant = Merchant::find($id);
            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Merchant not found.',
                    'data' => []
                ], 404);
            }
            try {
                $this->authorize('delete', $merchant);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to Delete this merchant.',
                    'data' => []
                ]);
            }
            $merchant->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Merchant deleted successfully.',
                'data' => []
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all statuses.
     */
    public function getStatuses(): JsonResponse
    {
        $verificationStatuses = MerchantVerificationStatusEnum::cases();
        $visibilityStatuses = MerchantVisiblityStatusEnum::cases();

        return response()->json([
            'success' => true,
            'message' => 'Statuses retrieved successfully.',
            'data' => [
                'verificationStatuses' => $verificationStatuses,
                'visibilityStatuses' => $visibilityStatuses,
            ]
        ]);
    }

    /**
     * Get all available merchants.
     */
    public function getAvailableMerchants(): JsonResponse
    {
        $availableMerchants = Merchant::where('verification_status', MerchantVerificationStatusEnum::Approved->value)->where('visibility_status', MerchantVisiblityStatusEnum::Visible->value)->with('user')->get();

        $availableMerchantsData = $availableMerchants->map(function ($merchant) {
            return [
                'id' => $merchant->id,
                'name' => $merchant->name,
                'mobile' => $merchant->user->mobile,
                'email' => $merchant->user->email,
                'address' => $merchant->address,
                'city' => $merchant->city,
                'state' => $merchant->state,
                'zipcode' => $merchant->zipcode,
                'country' => $merchant->country,
                'country_code' => $merchant->country_code,
                'latitude' => $merchant->latitude,
                'longitude' => $merchant->longitude,
                'pricing_template_id' => $merchant->pricing_template_id,
                'business_license' => $merchant->business_license,
                'articles_of_Incorporation' => $merchant->articles_of_Incorporation,
                'national_identity_card' => $merchant->national_identity_card,
                'authorized_signature' => $merchant->authorized_signature,
                'verification_status' => $merchant->verification_status,
                'meta_title' => $merchant->meta_title,
                'meta_keywords' => $merchant->meta_keywords,
                'meta_description' => $merchant->meta_description,
                'visibility_status' => $merchant->visibility_status,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Available merchants retrieved successfully.',
            'data' => $availableMerchantsData
        ]);
    }

    public function updateVerificationStatus(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $merchant = Merchant::find($id);
            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Merchant not found.',
                    'data' => []
                ], 404);
            }

            $validated = $request->validate([
                'verification_status' => ['required', new Enum(MerchantVerificationStatusEnum::class)],
            ]);

            $merchant->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Merchant verification status updated successfully.',
                'data' => $merchant
            ]);
        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'data' => $e->errors()
            ], 422);
        }
    }

    /**
     * Update the visibility status of the specified merchant.
     */
    public function updateVisibilityStatus(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $merchant = Merchant::find($id);
            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Merchant not found.',
                    'data' => []
                ], 404);
            }

            $validated = $request->validate([
                'visibility_status' => ['required', new Enum(MerchantVisiblityStatusEnum::class)],
            ]);

            $merchant->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Merchant visibility status updated successfully.',
                'data' => $merchant
            ]);
        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'data' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'data' => $e->getMessage()
            ], 500);
        }
    }
}
