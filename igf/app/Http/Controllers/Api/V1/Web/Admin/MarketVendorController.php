<?php

namespace App\Http\Controllers\Api\v1\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketVendor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MarketVendorController extends Controller
{
    // List all vendors
    public function index()
    {
        return response()->json(MarketVendor::all());
    }

    // Add a new vendor
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stall_name'      => 'required|string|max:255',
            'vendor_name'     => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'market_location' => 'required|string|max:255',
            'community'       => 'nullable|string|max:100',
        ]);

        $vendor = MarketVendor::create($validated);
        return response()->json($vendor, 201);
    }

    // Show a single vendor
    public function show($id)
    {
        $vendor = MarketVendor::findOrFail($id);
        return response()->json($vendor);
    }

    // Update a vendor
    public function update(Request $request, $id)
    {
        $vendor = MarketVendor::findOrFail($id);

        $validated = $request->validate([
            'stall_name'      => 'sometimes|required|string|max:255',
            'vendor_name'     => 'sometimes|required|string|max:255',
            'phone'           => 'sometimes|required|string|max:20',
            'market_location' => 'sometimes|required|string|max:255',
            'community'       => 'sometimes|nullable|string|max:100',
        ]);

        $vendor->update($validated);
        return response()->json($vendor);
    }

    public function stats()
    {
        $totalVendors = MarketVendor::count();

        // Sum of daily fees per vendor (today's total)
        $dailySum = MarketVendor::sum('daily_fees_collected');

        // Days elapsed in the current ISO week (Monday=1 ... Sunday=7)
        $daysElapsed = Carbon::now()->isoWeekday();

        //calculate today's transactions
        $todayTransactions = MarketVendor::whereDate('created_at', Carbon::today())->count();


        // Weekly total accumulates each day and resets at week start
        $weeklyTotal = $dailySum * $daysElapsed;    
        return response()->json([
            'total_vendors'       => $totalVendors,
            'today_total_fees'    => $dailySum,
            'weekly_total_fees'   => $weeklyTotal,
            'today_transactions'  => $todayTransactions,
        ]);
    }

    
    // Search vendors by stall or vendor name
    public function search(Request $request)
    {
        $query = $request->query('q');

       
        // If no query, return empty array
        if (!$query) {
            return response()->json([]);
        }

        $vendors = MarketVendor::where("stall_name", 'LIKE', "%{$query}%")
            ->orWhere("vendor_name", 'LIKE', "%{$query}%")
            ->get();

        return response()->json($vendors);
    }

    }
