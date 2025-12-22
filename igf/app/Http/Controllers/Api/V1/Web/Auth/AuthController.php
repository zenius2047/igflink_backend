<?php

namespace App\Http\Controllers\Api\V1\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Mail\AuthMail;
use App\Mail\ResetLinkMail;
use GuzzleHttp\Client as GuzzleClient;
use Twilio\Http\CurlClient;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
public function register(Request $request)
{
    $validated = $request->validate([
        'full_name'  => 'required|string|max:150',
        'email'      => 'required|string|email|max:255|unique:users',
        'password'   => 'required|string|min:8',
        'phone'      => 'required|string|max:32',
        'staff_id'   => 'required|string|max:80|unique:users',
        'department' => 'nullable|string|max:100',
        'role'       => 'required|string|exists:roles,name',
        'district_id'=> 'nullable|exists:districts,id',
    ]);

    // Create the user
    $user = User::create([
        'full_name'  => $validated['full_name'],
        'email'      => $validated['email'],
        'password'   => Hash::make($validated['password']),
        'phone'      => $validated['phone'],
        'staff_id'   => $validated['staff_id'],
        'department' => $validated['department'] ?? null,
        'created_by' => auth()->id(), 
    ]);

    // Attach role
    $role = Role::where('name', $validated['role'])->first();
    $user->roles()->attach($role->id);

    // Attach district if applicable
    if (!empty($validated['district_id'])) {
        $user->districts()->attach($validated['district_id']);
    }

    // Generate password reset token
    $token = Password::createToken($user);
    $resetUrl = config('app.frontend_url') . "/reset-password?token={$token}&email={$user->email}";
    $loginUrl = config('app.frontend_url') . "/login";

    // Send SMS or email based on role
    if ($role->name === 'collector') {
        $smsMessage = "Welcome to IGF Link, {$user->full_name}! "
                    . "Your account has been created. Use your phone number to log in. Click here: {$loginUrl}";
        $this->sendSMS($user->phone, $smsMessage);
    } else {
        Mail::to($user->email)->send(new AuthMail($user->full_name, $resetUrl));
    }

    // Optional: create auth token immediately
    $authToken = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User registered successfully. An email has been sent to set your password.',
        'user'    => $user,
        'token'   => $authToken,
    ], 201);
}


    /**
     * Login user
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get authenticated user details
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('roles');
        return response()->json([
            'status' => 'success',
            'user' =>[
                'id'        => $user->id,
                'full_name' => $user->full_name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'staff_id'  => $user->staff_id,
                'department'=> $user->department,
                'avatar'    => $user->avatar,
                'roles'     => $user->roles->pluck('name'),
            ]
        ]);
    }

    public function forgotPassword(Request $request)
    {
    $request->validate([
        'email' => 'required|string|email',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    // Generate reset token
    $token = Password::createToken($user);

    $resetUrl = config('app.frontend_url')
        . "/reset-password?token={$token}&email={$user->email}";

    // Send reset email
    Mail::to($user->email)->send(
        new ResetLinkMail($user->full_name, $resetUrl)
    );

    return response()->json([
        'message' => 'Password reset link sent to your email'
    ]);
    }

    
    public function resetPassword(Request $request)
    {
    $request->validate([
        'email'                 => 'required|string|email',
        'token'                 => 'required|string',
        'password'              => 'required|string|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        }
    );

    if ($status !== Password::PASSWORD_RESET) {
        return response()->json([
            'message' => 'Invalid or expired reset token'
        ], 400);
    }

    return response()->json([
        'message' => 'Password reset successful. You can now log in.'
    ]);
    }


  

    public function changePassword(Request $request)
{
    $request->validate([
        'current_password'      => 'required|string',
        'new_password'          => 'required|string|min:8|confirmed',
    ]);

    $user = $request->user();

    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'message' => 'Current password is incorrect'
        ], 400);
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json([
        'message' => 'Password changed successfully'
    ]);

}

public function updateProfile(Request $request){
    $user = $request->user();

    $validated = $request->validate([
        'full_name'  => 'sometimes|required|string|max:150',
        'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if (isset($validated['full_name'])) {
        $user->full_name = $validated['full_name'];
    }


    $imageUrl =null;
    // laravel file upload handling
    if ($request->hasFile('avatar')) {
        $file = $request->file('avatar');
        $path = $file->store('avatars', 'public');
        $imageUrl = asset('storage/' . $path);
        $user->avatar = $imageUrl;
    }

    $user->save();

    return response()->json([
        'message' => 'Profile updated successfully',
        'user'    => $user,
    ]);
}

 private function sendSMS($phoneNumber, $message)
{
    $apiKey   = env('MNOTIFY_SERVICE_API_KEY');
    $senderId = "IGF LINK";

    $apiEndpoint = env('MNOTIFY_URL');

   $postData = [
    'key'       => $apiKey,
    'recipient' => [$phoneNumber],
    'message'   => $message,
    'sender' => $senderId,
    ];


    $ch = curl_init($apiEndpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 // DISABLE SSL verification (for local testing ONLY)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        \Log::error('mNotify cURL error', [
            'error' => curl_error($ch),
        ]);
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    // Log raw response
    \Log::info('mNotify raw response', [
        'response' => $response,
        'phone' => $phoneNumber,
    ]);

    // Try to decode JSON response
    $decoded = json_decode($response, true);

    // SUCCESS CONDITIONS
    if (
        $response === '2000' ||
        (is_array($decoded) && isset($decoded['code']) && $decoded['code'] == '2000')
    ) {
        return true;
    }

    // FAILURE
    \Log::warning('mNotify SMS failed', [
        'response' => $response,
    ]);

    return false;
}
}
