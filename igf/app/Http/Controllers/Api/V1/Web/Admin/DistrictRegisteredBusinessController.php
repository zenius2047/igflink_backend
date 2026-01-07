<?php

namespace App\Http\Controllers\Api\v1\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegisteredBusiness;
use Illuminate\Http\Request;

class DistrictRegisteredBusinessController extends Controller
{
    public function index(Request $request)
    {
        $districtId = auth()->user()->districts()->first()->id;

        $businesses = RegisteredBusiness::where('district_id', $districtId)
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%")
                ->orWhere('owner_name', 'ilike', "%{$request->search}%")
                ->orWhere('registration_number', 'ilike', "%{$request->search}%");
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
            'data' => $businesses
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
            'name'    => 'required|string|max:150',
            'owner_name'       => 'required|string|max:150',
            'phone'            => 'required|string|max:32',
            'email'            => 'nullable|email|max:150',
            'location'         => 'nullable|string',
            'community'        => 'nullable|string|max:100',
            'business_type'    => 'nullable|string|max:100',
            'annual_fee'       => 'nullable|numeric',
        ]);
        // Create business and assign district automatically
        $business = RegisteredBusiness::create([
            'name'     => $validated['name'],
            'owner_name'        => $validated['owner_name'],
            'phone'             => $validated['phone'],
            'email'             => $validated['email'] ?? null,
            'location'          => $validated['location'] ?? null,
            'community'         => $validated['community'] ?? null,
            'business_type'     => $validated['business_type'] ?? null,
            'annual_fee'        => $validated['annual_fee'] ?? null,
            'status'            => $validated['status'] ?? 'active',
            'compliance_status' => $validated['compliance_status'] ?? 'pending',
            'district_id'       => $district->id
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => $business
        ], 201);
    }
    public function show($id)
    {
        
        $business = RegisteredBusiness::where('district_id', auth()->user()->districts()->first()->id)
            ->with('payments')
            ->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $business
        ]);
    }

    public function update(Request $request, $id)
    {
        $business = RegisteredBusiness::where('district_id', auth()->user()->districts()->first()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name'    => 'sometimes|required|string|max:150',
            'owner_name'       => 'sometimes|required|string|max:150',
            'phone'            => 'sometimes|required|string|max:32',
            'email'            => 'sometimes|nullable|email|max:150',
            'location'         => 'sometimes|nullable|string',
            'community'        => 'sometimes|nullable|string|max:100',
            'business_type'    => 'sometimes|nullable|string|max:100',
            'annual_fee'       => 'sometimes|nullable|numeric',
            'status'           => 'sometimes|nullable|string|in:active,inactive',
            'compliance_status'=> 'sometimes|nullable|string|in:pending,compliant,non-compliant'
        ]);

        $business->update($validated);

        return response()->json([
            'status' => 'success',
            'data'   => $business
        ]);
    }

    public function stats()
    {
        $districtId = auth()->user()->districts()->first()->id;

        $totalBusinesses = RegisteredBusiness::where('district_id', $districtId)->count();
        $activeBusinesses = RegisteredBusiness::where('district_id', $districtId)
            ->where('status', 'active')->count();
        $inactiveBusinesses = RegisteredBusiness::where('district_id', $districtId)
            ->where('status', 'inactive')->count();
        $compliantBusinesses = RegisteredBusiness::where('district_id', $districtId)
            ->where('compliance_status', 'compliant')->count();
        $nonCompliantBusinesses = RegisteredBusiness::where('district_id', $districtId)
            ->where('compliance_status', 'non-compliant')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_businesses' => $totalBusinesses,
                'active_businesses' => $activeBusinesses,
                'inactive_businesses' => $inactiveBusinesses,
                'compliant_businesses' => $compliantBusinesses,
                'non_compliant_businesses' => $nonCompliantBusinesses,
            ]
        ]);
    }


} 