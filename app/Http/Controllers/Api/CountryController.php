<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $query = Country::query();
        $perPage = $request->input('per_page', 15);

        if ($request->filled('query')) {
            $searchQuery = $request->input('query');
            $query->where('name', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('iso3', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('iso2', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('phonecode', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('currency', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('currency_name', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('currency_symbol', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('region', 'LIKE', '%' . $searchQuery . '%');
        }
        if ($request->filled('sort') && $request->filled('order')) {
            $query->orderBy($request->input('sort'), $request->input('order'));
        }
        $countries = $query->paginate($perPage);
        return response()->json([
            'success' => true,
            'message' => 'Countries retrieved successfully.',
            'data' => $countries
        ]);
    }
}
