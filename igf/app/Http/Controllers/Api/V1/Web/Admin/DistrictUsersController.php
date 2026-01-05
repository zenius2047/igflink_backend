<?php

namespace App\Http\Controllers\Api\V1\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\DistrictUser;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

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
}
