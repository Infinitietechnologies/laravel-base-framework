<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductConditionEnum;
use App\Http\Controllers\Controller;
use App\Models\ProductCondition;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ProductConditionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $page = $request->query('page', 1);
        $search = $request->query('query', '');
        $sortBy = $request->query('sort', 'id'); // Default sort by 'id'
        $sortOrder = $request->query('order', 'asc'); // Default sort order 'asc'

        $query = ProductCondition::with('category');

        if (!empty($search)) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        $productConditions = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Product conditions retrieved successfully',
            'data' => $productConditions,
            'errors' => []
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'category_id' => 'required|integer|exists:categories,id',
                'title' => 'required|string|max:255',
                'alignment' => ['required', new Enum(ProductConditionEnum::class)],
            ]);

            $productCondition = ProductCondition::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Product condition created successfully',
                'data' => $productCondition,
                'errors' => []
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->getMessage()
            ], 400);
        }
    }

    public function show($id): JsonResponse
    {
        $productCondition = ProductCondition::with('category')->find($id);

        if ($productCondition) {
            return response()->json([
                'success' => true,
                'message' => 'Product condition retrieved successfully',
                'data' => $productCondition,
                'errors' => []
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product condition not found',
                'errors' => []
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'category_id' => 'integer|exists:categories,id',
                'title' => 'string|max:255',
                'alignment' => [new Enum(ProductConditionEnum::class)],
            ]);

            $productCondition = ProductCondition::find($id);

            if ($productCondition) {
                $productCondition->update($validatedData);

                return response()->json([
                    'success' => true,
                    'message' => 'Product condition updated successfully',
                    'data' => $productCondition,
                    'errors' => []
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Product condition not found',
                    'errors' => []
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id): JsonResponse
    {
        $productCondition = ProductCondition::find($id);

        if ($productCondition) {
            $productCondition->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product condition deleted successfully',
                'errors' => []
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product condition not found',
                'errors' => []
            ], 404);
        }
    }

    public function alignments(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Product condition statuses retrieved successfully',
            'data' => ProductConditionEnum::cases(),
            'errors' => []
        ]);
    }

    public function getByCategory($categoryId): JsonResponse
    {
        $category = Category::with('productConditions')->find($categoryId);
        if ($category) {
            return response()->json([
                'success' => true,
                'message' => 'Product conditions retrieved successfully',
                'data' => $category->productConditions,
                'errors' => []
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
                'errors' => []
            ], 404);
        }
    }
}
