<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * */
    public function index(Request $request): JsonResponse
    {
        $query = Faq::query();
        $perPage = $request->input('per_page', 15);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('query')) {
            $searchQuery = $request->input('query');
            $query->where('question', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('answer', 'LIKE', '%' . $searchQuery . '%');
        }
        if ($request->filled('sort') && $request->filled('order')) {
            $query->orderBy($request->input('sort'), $request->input('order'));
        }
        $faqs = $query->paginate($perPage);
        return response()->json([
            'success' => true,
            'message' => 'FAQs retrieved successfully.',
            'data' => $faqs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {

        try {
            $validatedData = $request->validate([
                'question' => 'required|string|max:255',
                'answer' => 'required|string',
                'status' => 'required|in:active,inactive'
            ]);

            $faq = Faq::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'FAQ created successfully.',
                'data' => $faq
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'data' => $e->errors()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Faq $faq
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $faq = Faq::find($id);
        if (!$faq) {
            return response()->json([
                'success' => false,
                'message' => 'Faq not found.',
                'data' => []
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'FAQ retrieved successfully.',
            'data' => $faq
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param FAQ $faq
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $faq = Faq::find($id);
            if (!$faq) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faq not found.',
                    'data' => []
                ], 404);
            }
            $validatedData = $request->validate([
                'question' => 'sometimes|required|string|max:255',
                'answer' => 'sometimes|required|string',
                'status' => 'sometimes|required|in:active,inactive'
            ]);

            $faq->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'FAQ updated successfully.',
                'data' => $faq
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'data' => $e->errors()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Faq $faq
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $faq = Faq::find($id);
            if (!$faq) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faq not found.',
                    'data' => []
                ], 404);
            }
            $faq->delete();

            return response()->json([
                'success' => true,
                'message' => 'FAQ deleted successfully.',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.',
                'data' => $e->getMessage()
            ], 500);
        }
    }
}
