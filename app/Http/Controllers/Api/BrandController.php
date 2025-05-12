<?php

namespace App\Http\Controllers\Api;

use App\Enums\BrandStatusEnum;
use App\Models\Brand;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\PermissionMiddleware;

class BrandController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->middleware(PermissionMiddleware::class . ':create brand')->only(['store']);
        $this->middleware(PermissionMiddleware::class . ':view brand')->only(['statuses']);
        $this->middleware(PermissionMiddleware::class . ':update brand')->only(['update']);
        $this->middleware(PermissionMiddleware::class . ':delete brand')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Brand::query();
        $perPage = $request->input('per_page', 15);
        if ($request->filled('query')) {
            $searchQuery = $request->input('query');
            $query->where(function ($query) use ($searchQuery) {
                $query->where('title', 'like', '%' . $searchQuery . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('sort') && $request->filled('order')) {
            $query->orderBy($request->input('sort'), $request->input('order'));
        }
        $brands = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Brands retrieved successfully',
            'data' => $brands
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255|unique:brands,title',
                'logo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
                'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'status' => ['required', new Enum(BrandStatusEnum::class)],
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
            ]);

            $mergeData = [];
            if ($request->file('logo')) {
                $imageRes = uploadFile($request->file('logo'), 'brands');
                if ($imageRes['success']) {
                    $mergeData['logo'] = $imageRes['file_url'];
                }
            }
            if ($request->file('banner')) {
                $bannerRes = uploadFile($request->file('banner'), 'brands');
                if ($bannerRes['success']) {
                    $mergeData['banner'] = $bannerRes['file_url'];
                }
            }
            $mergeData['uuid'] = (string)Str::uuid();
            $validated = array_merge($validated, $mergeData);

            $brand = Brand::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully',
                'data' => $brand
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => $e->errors()
            ], 422);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $brand = Brand::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Brand retrieved successfully',
                'data' => $brand
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found.',
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'nullable|string|max:255|unique:brands,title,' . $id,
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'status' => ['nullable', new Enum(BrandStatusEnum::class)],
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
            ]);

            $mergeData = [];
            if ($request->file('logo')) {
                $imageRes = uploadFile($request->file('logo'), 'brands/logo');
                if ($imageRes['success']) {
                    $mergeData['logo'] = $imageRes['file_url'];
                }
            }
            if ($request->file('banner')) {
                $bannerRes = uploadFile($request->file('banner'), 'brands/banners');
                if ($bannerRes['success']) {
                    $mergeData['banner'] = $bannerRes['file_url'];
                }
            }
            $validated = array_merge($validated, $mergeData);

            $brand = Brand::findOrFail($id);
            $brand->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully',
                'data' => $brand
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found',
                'data' => []
            ], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found',
                'data' => []
            ]);
        }
        $brand->delete();
        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully',
            'data' => []
        ], 200);
    }

    public function statuses(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Statuses retrieved successfully',
            'data' => BrandStatusEnum::cases()
        ]);
    }
}
