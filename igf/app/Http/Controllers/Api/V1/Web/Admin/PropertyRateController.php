<?php

namespace App\Http\Controllers\Api\V1\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;

class PropertyRateController extends Controller
{
   //get the statistics of property
   public function stats()
   {
        //total properties
        $totalProperties = Property::count();

        //quartely collecions
        $quarterCollection = Property::sum('amount_paid');

        //compliant properties
        $compliantProperties = Property::where('status', 'compliant')->count();

        $totalExpectedRate = Property::sum('rate_amount');

        if ($totalExpectedRate > 0) {
            $collectionRate = ($quarterCollection / $totalExpectedRate) * 100;
        } else {
            $collectionRate = 0; // Avoid division by zero error
        }

        return response()->json([
            'total_properties' => $totalProperties,
            'quarter_collection' => $quarterCollection,
            'compliant_properties' => $compliantProperties,
            'collection_rate_percentage' => round($collectionRate, 2)
        ]);
    }



    //add a new property rate 
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'address' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'rate_amount' => 'required|numeric',
            'owner_phone' => 'nullable|string|max:20',
            'owner_email' => 'nullable|email|max:255',
            'zone' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'community' => 'required|string|max:100',
            'registration_number' => 'required|string|unique:properties,registration_number',
            'due_date' => 'nullable|date',
        ]);

        $validated['registration_number'] = 'PROP-' . strtoupper(uniqid());

        // // Set a due date (e.g., 3 months from today for "Quarterly")
        // $validated['due_date'] = now()->addMonths(3);


        $property = Property::create($validatedData);

        return response()->json([
            'message' => 'Property registered successfully',
            'property' => $property
        ], 201);
    }

    //update a property rate details
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id); 

        $validatedData = $request->validate([
            'address' => 'sometimes|required|string|max:255',
            'owner_name' => 'sometimes|required|string|max:255',
            'rate_amount' => 'sometimes|required|numeric',
            'owner_phone' => 'sometimes|nullable|string|max:20',
            'owner_email' => 'sometimes|nullable|email|max:255',
            'zone' => 'sometimes|required|string|max:100',
            'district' => 'sometimes|required|string|max:100',
            'community' => 'sometimes|required|string|max:100',
            'due_date' => 'sometimes|nullable|date',
        ]);
        
        $property->update($validatedData);
        return response()->json([
            'message' => 'Property updated successfully',
            'property' => $property
        ]);
    }


    // //delete a property rate
    // public function destroy($id)
    // {
    //     $property = Property::findOrFail($id);
    //     $property->delete();
    //     return response()->json([
    //         'message' => 'Property deleted successfully'
    //     ]);;
    // }

    //get property list details
    public function index()
    {
        $properties = Property::paginate(10); // Paginate 10 per page
        return response()->json([
            'total_properties' => $properties->total(),
            'properties' => $properties->items(),
        ]);
        }

        //record a payment against a property
        public function recordPayment(Request $request, $id)
        {
            $property = Property::findOrFail($id);

            $validatedData = $request->validate([
                'amount_paid' => 'required|numeric',
                'payment_method' => 'required|string|max:100',
                'transaction_id' => 'required|string|unique:payments,transaction_id',
                'description' => 'nullable|string|max:255',
            ]);

            // Update the amount paid
            $property->amount_paid += $validatedData['amount_paid'];

            if ($property->amount_paid >= $property->rate_amount) {
                $property->status = 'compliant';
            } else {
                $property->status = 'partially_paid';
            }

            $property->save();

            return response()->json([
                'message' => 'Payment recorded successfully',
                'property' => $property
            ]);
        }

    }

  