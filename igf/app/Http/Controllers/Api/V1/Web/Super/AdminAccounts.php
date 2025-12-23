<?php

namespace App\Http\Controllers\Api\V1\Web\Super;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Mail\AuthMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

class AdminAccounts extends Controller
{
    //
 public function index(Request $request)
{
    $perPage = $request->get('per_page', 10); // default 10

    $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'super_admin');
        })
        ->paginate($perPage);

    return response()->json([
        'status' => 'success',
        'data' => $admins->items(),
        'meta' => [
            'current_page' => $admins->currentPage(),
            'last_page' => $admins->lastPage(),
            'per_page' => $admins->perPage(),
            'total' => $admins->total(),
        ]
    ], 200);
}

    public function register(Request $request)
{
    $validated = $request->validate([
        'full_name'  => 'required|string|max:150',
        'email'      => 'required|string|email|max:255|unique:users',
        'password'   => 'required|string|min:8',
        'phone'      => 'required|string|max:32',
    ]);


    $staffId = 'IGF' . strtoupper(substr($validated['full_name'], 0, 3)) . rand(1000, 9999);

    // Create the user
    $user = User::create([
        'full_name'  => $validated['full_name'],
        'email'      => $validated['email'],
        'password'   => Hash::make($validated['password']),
        'phone'      => $validated['phone'],
        'staff_id'   => $staffId,
        'created_by' => auth()->id(), 
    ]);

    // Attach role
    $role = Role::where('name', 'super_admin')->firstOrFail();
    $user->roles()->attach($role->id);



    // Generate password reset token
    $token = Password::createToken($user);
    $temproaryPassword = $validated['password'];
    $resetUrl = config('app.frontend_url') . "/reset-password?token={$token}&email={$user->email}";
    $loginUrl = config('app.frontend_url') . "/login";


    // Send SMS or email based on role

    Mail::to($user->email)->send(new AuthMail($user->full_name, $resetUrl, $temproaryPassword, $loginUrl));


    // Optional: create auth token immediately
    $authToken = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User registered successfully. An email has been sent to set your password.',
        'user'    => $user,
        'token'   => $authToken,
    ], 201);
}

    public function activateOrDeactivate($id)
    {
        $user = User::findOrFail($id);

        // Toggle active status
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'status' => 'success',
            'data'   => $user,
            'message'=> $user->is_active ? 'User activated successfully.' : 'User deactivated successfully.'
        ], 200);        

    }
}
