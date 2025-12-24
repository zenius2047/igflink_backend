<?php

namespace App\Http\Controllers\Api\V1\Web\Super;

use App\Http\Controllers\Controller;
use App\Models\Packages;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    //
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10); // default: 10

        $packages = Packages::paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $packages->items(),
            'meta' => [
                'current_page' => $packages->currentPage(),
                'last_page' => $packages->lastPage(),
                'per_page' => $packages->perPage(),
                'total' => $packages->total(),
            ]
        ], 200);
    }


    // Store a newly created package
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'price' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:3',
            'is_active' => 'required|boolean',
        ]);

        $package = Packages::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $package,
        ], 201);
    }

    // update package
    public function update(Request $request, $id)
    {
        $package = Packages::findOrFail($id);   
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'description' => 'sometimes|nullable|string',
            'features' => 'sometimes|nullable|array',
            'price' => 'sometimes|required|numeric|min:0',
            'duration_months' => 'sometimes|required|integer|min:3',
            'is_active' => 'sometimes|required|boolean',
        ]);
        $package->update($validated);
        return response()->json([
            'status' => 'success',
            'data' => $package,
        ], 200);
    }

    // delete package
    public function destroy($id)
    {
        $package = Packages::findOrFail($id);
        $package->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Package deleted successfully',
        ], 200);

    }
}