<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Check if super admin already exists
        $email = 'superadmin@example.com';
        if (User::where('email', $email)->exists()) {
            $this->command->info('Super admin already exists!');
            return;
        }

        // Create the super admin user
        $user = User::create([
            'full_name'  => 'Super Admin',
            'email'      => $email,
            'password'   => Hash::make('Password123!'), // change after first login
            'phone'      => '0000000000',
            'staff_id'   => 'SUPERADMIN001',
            'department' => 'Administration',
            'created_by' => null, // first account
            'is_active'  => true,
        ]);

        // Assign super_admin role
        $role = Role::where('name', 'super_admin')->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        $this->command->info("Super admin created: {$email} / Password123!");
    }
}
