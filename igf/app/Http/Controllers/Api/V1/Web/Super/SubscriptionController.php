<?php

namespace App\Http\Controllers\Api\V1\Web\Super;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Packages;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * List all subscriptions with district & package info
     */
    public function index()
    {
        $subscriptions = Subscription::with(['district', 'package'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $subscriptions
        ]);
    }

    /**
     * Create a new subscription
     */
  public function store(Request $request)
{
    $validated = $request->validate([
        'district_id'      => 'required|exists:districts,id',
        'package_id'       => 'required|exists:packages,id',
        'payment_method'   => 'required|string|max:50',
        'reference_number' => 'required|string|unique:subscriptions,reference_number',
        'is_active'        => 'sometimes|boolean',
    ]);

    // Package must be active
    $package = Packages::where('id', $validated['package_id'])
        ->where('is_active', true)
        ->first();

    if (! $package) {
        return response()->json([
            'status' => 'error',
            'message' => 'Selected package is not active'
        ], 422);
    }

    // Default is_active to true
    $validated['is_active'] = $validated['is_active'] ?? true;

    // Prevent duplicate active subscription
    $existing = Subscription::where('district_id', $validated['district_id'])
        ->where('is_active', true)
        ->get()
        ->first(fn ($sub) => $sub->isActive());

    if ($existing) {
        return response()->json([
            'status' => 'error',
            'message' => 'District already has an active subscription'
        ], 409);
    }

    try {
        $subscription = Subscription::create($validated)->load(['district', 'package']);

        return response()->json([
            'status' => 'success',
            'data' => [
                ...$subscription->toArray(),
                'expires_at' => $subscription->expiresAt(),
            ],
        ], 201);

    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Subscription creation failed',
        ], 500);
    }
}


    // update 

public function update(Request $request, $id)
{
    $subscription = Subscription::with('package')->findOrFail($id);

    $validated = $request->validate([
        'package_id'       => 'sometimes|nullable|exists:packages,id',
        'payment_method'   => 'sometimes|nullable|string|max:50',
        'reference_number' => 'sometimes|nullable|string|unique:subscriptions,reference_number,' . $subscription->id,
        'is_active'        => 'sometimes|boolean',
    ]);

    // Validate package status if package_id is present
    if (
        array_key_exists('package_id', $validated) &&
        $validated['package_id'] !== $subscription->package_id
    ) {
        $package = Package::where('id', $validated['package_id'])
            ->where('is_active', true)
            ->first();

        if (! $package) {
            return response()->json([
                'status' => 'error',
                'message' => 'Selected package is not active',
            ], 422);
        }
    }

    // Prevent reactivation of expired subscriptions
    if (
        array_key_exists('is_active', $validated) &&
        $validated['is_active'] === true &&
        ! $subscription->isActive()
    ) {
        return response()->json([
            'status' => 'error',
            'message' => 'Expired subscriptions cannot be reactivated. Create a new subscription.',
        ], 422);
    }

    $subscription->update(array_filter($validated, fn ($v) => !is_null($v)));

    return response()->json([
        'status' => 'success',
        'data' => $subscription->fresh()->load(['district', 'package']),
    ]);
}


    /**
     * Show a single subscription
     */
    public function show($id)
    {
        $subscription = Subscription::with(['district', 'package'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $subscription,
        ]);
    }

    /**
     * Activate or deactivate a package
     */
    public function togglePackageStatus($id)
    {
        $package = Package::findOrFail($id);
        $package->is_active = !$package->is_active;
        $package->save();
        return response()->json([
            'status' => 'success',
            'data' => $package,
        ]);
    }


    // stats
    public function statistics()
    {
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::get()->filter->isActive()->count();
        $inactiveSubscriptions = $totalSubscriptions - $activeSubscriptions;

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_subscriptions' => $totalSubscriptions,
                'active_subscriptions' => $activeSubscriptions,
                'inactive_subscriptions' => $inactiveSubscriptions,
            ],
        ]);
    }   
}
