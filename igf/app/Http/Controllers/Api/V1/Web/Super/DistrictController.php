<?php

namespace App\Http\Controllers\Api\V1\Web\Super;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\User;
use App\Http\Resources\DistrictResource;    
use Illuminate\Http\Request;

class DistrictController extends Controller
{
   public function index(Request $request)
{
    $perPage = $request->get('per_page', 10); // default: 10

    $districts = District::paginate($perPage);

    return response()->json([
        'status' => 'success',
        'data' => DistrictResource::collection($districts),
        'meta' => [
            'current_page' => $districts->currentPage(),
            'last_page' => $districts->lastPage(),
            'per_page' => $districts->perPage(),
            'total' => $districts->total(),
        ]
    ], 200);
}

  public function store(Request $request)
{
  
    // Validate only the fields the user will actually send
    $validated = $request->validate([
        'name'    => 'required|string|max:100',
        'email'   => 'nullable|email|max:255',
        'phone'   => 'nullable|string|max:32',
        'address' => 'nullable|string',
        'region'  => 'nullable|string|max:100',
    ]);

    // Automatically generate a code from the name
    $code = strtoupper(str_pad(substr($validated['name'], 0, 3), 3, 'X')) . rand(100, 999);

   // Create the district
    $district = District::create([
        'name'    => $validated['name'],
        'code'    => $code,
        'email'   => $validated['email'] ?? null,
        'phone'   => $validated['phone'] ?? null,
        'address' => $validated['address'] ?? null,
        'region'  => $validated['region'] ?? null,
        'is_active' => true,
    ]);

    // Return success response
    return response()->json([
        'status' => 'success',
        'data'   =>  new DistrictResource($district)
    ], 201);
}


    public function update(Request $request, $id)
    {
        $district = District::findOrFail($id);

        $validated = $request->validate([
            'name'    => 'sometimes|required|string|max:100',
            'email'   => 'sometimes|email|max:255',
            'phone'   => 'sometimes|string|max:32',
            'address' => 'sometimes|string',
            'region'  => 'sometimes|string|max:100',
            'is_active' => 'boolean',
        ]);

        $district->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => new DistrictResource($district)
        ], 200);
    }

    public function activateOrDeactivate($id)
    {
        $district = District::findOrFail($id);
        $district->is_active = !$district->is_active;
        $district->save();

        return response()->json([
            'status' => 'success',
            'data' => new DistrictResource($district)
        ], 200);
    }

    public function statistics()
    {
        $totalDistricts = District::count();
        $activeDistricts = District::where('is_active', true)->count();
        $inactiveDistricts = District::where('is_active', false)->count();
        $regionWiseCounts = District::select('region', \DB::raw('count(*) as total'))
            ->groupBy('region')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_districts' => $totalDistricts,
                'active_districts' => $activeDistricts,
                'inactive_districts' => $inactiveDistricts,
                'region_counts' => $regionWiseCounts,
            ]
        ], 200);
    }   

    
}
