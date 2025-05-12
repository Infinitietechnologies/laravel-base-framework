<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Enums\Category\CategoryStatusEnum;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\RoleMiddleware;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', RoleMiddleware::class . ':super_admin'])->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource with pagination, search, and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::query();
        $perPage = $request->input('per_page', 15); // Default to 15 items per page if not provided

        if ($request->filled('query')) {
            $searchQuery = $request->input('query');
            $query->where('title', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('description', 'LIKE', '%' . $searchQuery . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('sort') && $request->filled('order')) {
            $query->orderBy($request->input('sort'), $request->input('order'));
        }

        $categories = $query->with('parent')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully.',
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'parent_id' => 'nullable|integer|exists:categories,id',
                'title' => 'required|string|max:255|unique:categories,title',
                'description' => 'nullable|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
                'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'status' => ['required', new Enum(CategoryStatusEnum::class)],
                'requires_approval' => 'boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
            ], $this->messages());
            $fileData = $this->manageUpload($request);
            if (!empty($fileData['image'])) {
                $validated['image'] = $fileData['image'];
            }
            if (!empty($fileData['banner'])) {
                $validated['banner'] = $fileData['banner'];
            }
            $validated['uuid'] = (string)Str::uuid();
            $category = Category::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'data' => $category
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'data' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully.',
            'data' => $category->load('parent')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $validated = $request->validate([
                'parent_id' => 'nullable|integer|exists:categories,id',
                'title' => 'nullable|string|max:255|unique:categories,title,' . $category->id,
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'status' => ['nullable', new Enum(CategoryStatusEnum::class)],
                'requires_approval' => 'boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
            ], $this->messages());
            $fileData = $this->manageUpload($request);
            if (!empty($fileData['image'])) {
                $validated['image'] = $fileData['image'];
            }
            if (!empty($fileData['banner'])) {
                $validated['banner'] = $fileData['banner'];
            }
            $category->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'data' => $category
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'data' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.',
                'data' => null
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }
    }

    /**
     * Display a listing of available categories with pagination, search, and sorting.
     */
    public function availableCategories(Request $request): JsonResponse
    {
        $query = Category::where('status', CategoryStatusEnum::ACTIVE);
        $perPage = $request->input('per_page', 15); // Default to 15 items per page if not provided

        if ($request->filled('query')) {
            $searchQuery = $request->input('query');
            $query->where('title', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('description', 'LIKE', '%' . $searchQuery . '%');
        }

        if ($request->filled('sort') && $request->filled('order')) {
            $query->orderBy($request->input('sort'), $request->input('order'));
        }

        $categories = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Available categories retrieved successfully.',
            'data' => $categories
        ]);
    }

    /**
     * Display a listing of all available category statuses.
     */
    public function availableCategoryStatuses(): JsonResponse
    {
        $statuses = CategoryStatusEnum::values();
        return response()->json([
            'success' => true,
            'message' => 'Available category statuses retrieved successfully.',
            'data' => $statuses
        ]);
    }

    /**
     * Custom messages for validation errors.
     */
    protected function messages(): array
    {
        return [
            'title.unique' => 'The category title must be unique.'
        ];
    }

    private function manageUpload($request): array
    {
        $validated = [];
        if ($request->file('image')) {
            $imageRes = uploadFile($request->file('image'), 'categories/images');
            if ($imageRes['success']) {
                $validated['image'] = $imageRes['file_url'];
            }
        }
        if ($request->file('banner')) {
            $bannerRes = uploadFile($request->file('banner'), 'categories/banners');
            if ($bannerRes['success']) {
                $validated['banner'] = $bannerRes['file_url'];
            }
        }
        return $validated;
    }
}
