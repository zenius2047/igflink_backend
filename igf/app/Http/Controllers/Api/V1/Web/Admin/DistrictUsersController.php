<?php

namespace App\Http\Controllers\Api\V1\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\DistrictUser;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Validation\Rule;

class DistrictUsersController extends Controller
{
    //count users in a district
    public function stats($districtId)
    {
    //    count users in a district
        $userCount = DistrictUser::where('district_id', $districtId)->count();
        // count active users in a district
        $activeUserCount = User::whereHas('districts', function ($query) use ($districtId) {
            $query->where('district_id', $districtId);
        })->where('is_active', true)->count();
        // count district admins in a district
        $districtAdminCount = User::whereHas('districts', function ($query) use ($districtId) {
            $query->where('district_id', $districtId);
        })->whereHas('roles', function ($query) {
            $query->where('name', 'district_admin');
        })->count();

        return response()->json([
            'district_id' => $districtId,
            'user_count' => [
                'total' => $userCount,
                'active' => $activeUserCount,
                'district_admins' => $districtAdminCount
            ]
        ]);
    }

        //list users in a district 
    public function usersByDistrict($districtId)
{

    $users = User::with('roles')
        ->whereHas('districts', function($query) use ($districtId) {
            $query->where('district_id', $districtId);
        })
        ->paginate(10);  // paginate 10 per page

    return response()->json([
        'total_users'  => $users->total(),
        'users'        => UserResource::collection($users),
    ]);
}

    //activate or deactivate a user
    public function activateOrDeactivate($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'message' => 'User ' . ($user->is_active ? 'activated' : 'deactivated') . ' successfully.',
            'user' => new UserResource($user)
        ]);
    }

    //change user role
    public function changeUserRole(Request $request, $id)
    {
        //check authorization
        if (!auth()->user()->hasRole('district_admin')) {
            return response()->json([
                'message' => 'Unauthorized. Only district admins can change roles.'
            ], 403);
        }

        //Validate the request
        $request->validate([
            'role' => [
                'required',
                'string',
                'exists:roles,name',
                Rule::in(['district_admin', 'finance_officer', 'auditor','collector','revenue_superintendent']) // Allowed roles
            ],
        ]);

        //Find the user
        $user = User::findOrFail($id);
 
        //Prevent changing own role
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'You cannot change your own role.'
            ], 403);
        }

        // // Check if the user belongs to the same district(s) as the district admin
        // $adminDistrictIds = auth()->user()->districts->pluck('id');  // Assumes User has 'districts' relationship via DistrictUser pivot
        // if (!$user->districts()->whereIn('district_id', $adminDistrictIds)->exists()) {
        //     return response()->json([
        //         'message' => 'Unauthorized. User not in your district.'
        //     ], 403);
        // }

        //Find and assign the role
        $role = Role::where('name', $request->role)->firstOrFail();
        $user->roles()->sync([$role->id]);

        //Return success response
        return response()->json([
            'message' => 'User role changed successfully.',
            'user' => new UserResource($user)
        ]);
    }

// remove user role
    public function removeUserRole($id)
    {
        //check authorization
        if (!auth()->user()->hasRole('district_admin')) {
            return response()->json([
                'message' => 'Unauthorized. Only district admins can remove roles.'
            ], 403);
        }

        //Find the user
        $user = User::findOrFail($id);

        //Prevent removing own role
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'You cannot remove your own role.'
            ], 403);
        }

        //Remove all roles from the user
        $user->roles()->detach();

        //Return success response
        return response()->json([
            'message' => 'User roles removed successfully.',
            'user' => new UserResource($user)
        ]);
    }
}
