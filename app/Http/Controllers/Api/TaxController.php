<?php

namespace App\Http\Controllers\Api;

use App\Models\TaxClass;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(PermissionMiddleware::class . ':create tax')->only(['store']);
        $this->middleware(PermissionMiddleware::class . ':view tax')->only(['index','show']);
        $this->middleware(PermissionMiddleware::class . ':update tax')->only(['update']);
        $this->middleware(PermissionMiddleware::class . ':delete tax')->only(['destroy']);
    }

    public function index(): JsonResponse
    {
        $taxClasses = TaxClass::with('taxRates')->get();
        return response()->json([
            'success' => true,
            'message' => 'Tax classes retrieved successfully.',
            'data' => $taxClasses
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'title' => 'required|string|max:255|unique:tax_classes',
                'country' => 'nullable|string|max:255',
                'country_code' => 'nullable|string|max:255',
                'tax_rates' => 'required|array',
                'tax_rates.*.rate' => 'required|numeric|between:0,99.99',
            ]);

            $taxClass = TaxClass::create($request->only(['title', 'country', 'country_code']));

            foreach ($request->tax_rates as $rate) {
                $taxClass->taxRates()->create($rate);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tax class created successfully.',
                'data' => $taxClass->load('taxRates')
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'data' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the tax class.',
                'data' => []
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $taxClass = TaxClass::with('taxRates')->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Tax class retrieved successfully.',
                'data' => $taxClass
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tax not found.',
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $taxClass = TaxClass::findOrFail($id);

        try {
            DB::beginTransaction();

            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'country' => 'nullable|string|max:255',
                'country_code' => 'nullable|string|max:255',
                'tax_rates' => 'sometimes|required|array',
                'tax_rates.*.rate' => 'required|numeric|between:0,99.99',
            ]);

            $taxClass->update($request->only(['title', 'country', 'country_code']));

            if ($request->has('tax_rates')) {
                $taxClass->taxRates()->delete();
                foreach ($request->tax_rates as $rate) {
                    $taxClass->taxRates()->create($rate);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tax class updated successfully.',
                'data' => $taxClass->load('taxRates')
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'data' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the tax class.',
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $taxClass = TaxClass::findOrFail($id);
            DB::beginTransaction();

            $taxClass->taxRates()->delete();
            $taxClass->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tax class deleted successfully.',
                'data' => []
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tax not found.',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the tax class.',
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
