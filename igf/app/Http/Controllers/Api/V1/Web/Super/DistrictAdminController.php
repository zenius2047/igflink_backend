<?php

namespace App\Http\Controllers\Api\V1\Web\Super;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\User;
use App\Models\Role;
use App\Mail\DistrictAdminCreate;
use App\Http\Resources\DistrictAdminResource;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

class DistrictAdminController extends Controller
{
    //
    public function index()
    {
        $districtAdmins = User::whereHas('roles', function ($query) {
            $query->where('name', 'district_admin');
        })->with('districts')->get();

        return response()->json([
            'status' => 'success',
            'data' => DistrictAdminResource::collection($districtAdmins)
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'  => 'required|string|max:150',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8',
            'phone'      => 'required|string|max:32',
            'staff_id'   => 'required|string|max:80|unique:users',
            'department' => 'nullable|string|max:100',
            'district_id'=> 'required|exists:districts,id',
            'role'       => 'required|string|exists:roles,name',
        ]);

        // Create the district admin user
        $user = User::create([
            'full_name'  => $validated['full_name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'phone'      => $validated['phone'],
            'staff_id'   => $validated['staff_id'],
            'department' => $validated['department'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // Attach district_admin role
        $role = Role::where('name',$validated['role'])->first();
        $user->roles()->attach($role->id);

        // Attach district
        $user->districts()->attach($validated['district_id']);

        $token = Password::createToken($user);

        $resetUrl = config('app.frontend_url') . "/reset-password?token={$token}&email={$user->email}";
        // send email notification to the district admin with login details (optional)
        Mail::to($user->email)->send(new DistrictAdminCreate($user->districts()->first()->name, $resetUrl, $validated['password'])); 
        
        return response()->json([
            'status' => 'success',
            'data'   => DistrictAdminResource::make($user)
        ], 201);
    }


    // create update
public function update(Request $request, $id)
{
    $districtAdmin = User::findOrFail($id);

    $validated = $request->validate([
        'full_name'  => 'sometimes|required|string|max:150',
        'email'      => 'sometimes|required|string|email|max:255|unique:users,email,' . $districtAdmin->id,
        'password'   => 'sometimes|required|string|min:8',
        'phone'      => 'sometimes|required|string|max:32',
        'staff_id'   => 'sometimes|required|string|max:80|unique:users,staff_id,' . $districtAdmin->id,
        'department' => 'sometimes|nullable|string|max:100',
        'district_id'=> 'sometimes|required|exists:districts,id',
        'role'       => 'sometimes|required|string|exists:roles,name',
    ]);

    // Hash password if provided
    if (isset($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    }

    // Only update columns that exist in the users table
    $fillableFields = ['full_name', 'email', 'password', 'phone', 'staff_id', 'department'];
    $districtAdmin->update(array_intersect_key($validated, array_flip($fillableFields)));

    // Sync district if provided
    if (isset($validated['district_id'])) {
        $districtAdmin->districts()->sync([$validated['district_id']]);
    }

    // Sync role if provided
    if (isset($validated['role'])) {
        $role = Role::where('name', $validated['role'])->first();
        if ($role) {
            $districtAdmin->roles()->sync([$role->id]);
        }
    }

    return response()->json([
        'status' => 'success',
        'data'   => DistrictAdminResource::make($districtAdmin)
    ], 200);
}


    //activeate or deactivate
    public function activateOrDeactivate($id)
    {
        $districtAdmin = User::findOrFail($id);
        $districtAdmin->is_active = !$districtAdmin->is_active;
        $districtAdmin->save(); 
        return response()->json([
            'status' => 'success',
            'data'   => DistrictAdminResource::make($districtAdmin)
        ], 200);
        }
}
