<?php

namespace App\Http\Controllers\Api\V1\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegisteredVehicles;
use Illuminate\Http\Request;

class DistrictRegisteredVehicles extends Controller
{
    public function index(Request $request)
    {
        $districtId = auth()->user()->districts()->first()->id;

        $vehicles = RegisteredVehicles::where('district_id', $districtId)
            ->when($request->search, function ($q) use ($request) {
                $q->where('owner_name', 'ilike', "%{$request->search}%")
                ->orWhere('registration_number', 'ilike', "%{$request->search}%")
                ->orWhere('vehicle_type', 'ilike', "%{$request->search}%");
            })
            ->when($request->status, fn ($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->compliance, fn ($q) =>
                $q->where('compliance_status', $request->compliance)
            )
           
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $vehicles
        ]);
    }

    public function store(Request $request)
    {
        

        // Get the authenticated user's district
        $district = auth()->user()->districts()->first();
        if (!$district) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authenticated user does not belong to a district.'
            ], 400);
        }

        // Validate request
        $validated = $request->validate([
            'owner_name'       => 'required|string|max:150',
            'registration_number' => 'required|string|max:32',
            'vehicle_type'     => 'required|string|max:100',
            'phone'            => 'nullable|string|max:32',
            'email'            => 'nullable|email|max:150',
            'make'              => 'nullable|string|max:100',
            'model'            => 'nullable|string|max:100', 
            'monthly_levy'     => 'nullable|numeric',
            
        ]);
        // Create vehicle and assign district automatically
        $vehicle = RegisteredVehicles::create([
            'district_id' => $district->id,
            'owner_name' => $validated['owner_name'],
            'registration_number' => $validated['registration_number'],
            'vehicle_type' => $validated['vehicle_type'],
            'model' => $validated['model'] ?? null,
            'make' => $validated['make'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'monthly_levy' => $validated['monthly_levy'] ?? null,
            
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $vehicle
        ], 201);
    }

    public function show($id)
    {
        $districtId = auth()->user()->districts()->first()->id;

        $vehicle = RegisteredVehicles::where('district_id', $districtId)
            ->where('id', $id)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vehicle not found in your district.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $vehicle
        ]);
    }

    public function update(Request $request, $id)
    {
        $districtId = auth()->user()->districts()->first()->id;

        $vehicle = RegisteredVehicles::where('district_id', $districtId)
            ->where('id', $id)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vehicle not found in your district.'
            ], 404);
        }

        $validated = $request->validate([
            'owner_name'       => 'sometimes|required|string|max:150',
            'registration_number' => 'sometimes|required|string|max:32',
            'vehicle_type'     => 'sometimes|required|string|max:100',
            'phone'            => 'nullable|string|max:32',
            'email'            => 'nullable|email|max:150',
            'make'              => 'nullable|string|max:100',
            'model'            => 'nullable|string|max:100', 
            'monthly_levy'     => 'nullable|numeric',
        ]);

        $vehicle->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $vehicle
        ]);
    }

    //vehicle levies statistics
    public function stats()
    {
        $totalVehicles = RegisteredVehicles::count();

        $totalMonthlyLevy = RegisteredVehicles::sum('monthly_levy');

        $totalcomplete = RegisteredVehicles::where('compliance_status', 'compliant')->count();

        return response()->json([
            'total_vehicles' => $totalVehicles,
            'total_monthly_levy' => $totalMonthlyLevy,
            'total_compliant_vehicles' => $totalcomplete,
        ]);


        
    }

}

