<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing roles (should be created by RolePermissionSeeder)
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        
        // If roles don't exist, create them (fallback)
        if (!$superAdminRole) {
            $superAdminRole = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
            $superAdminRole->syncPermissions(Permission::all());
        }
        
        if (!$adminRole) {
            $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
            // Assign admin permissions (excluding critical system permissions)
            $adminPermissions = Permission::whereNotIn('name', [
                'backup system',
                'system settings',
                'delete users',
            ])->get();
            $adminRole->syncPermissions($adminPermissions);
        }

        // Create Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@lmsebook.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('SuperAdmin123!'),
                'email_verified_at' => now(),
                'whatsapp_number' => '+6281234567800',
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@lmsebook.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('Admin123!'),
                'email_verified_at' => now(),
                'whatsapp_number' => '+6281234567801',
            ]
        );
        $admin->assignRole($adminRole);

        // Create Demo Admin User (for testing)
        $demoAdmin = User::firstOrCreate(
            ['email' => 'demo@lmsebook.com'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('Demo123!'),
                'email_verified_at' => now(),
                'whatsapp_number' => '+6281234567802',
            ]
        );
        $demoAdmin->assignRole($adminRole);

        $this->command->info('Admin users created successfully!');
        $this->command->info('Super Admin: superadmin@lmsebook.com / SuperAdmin123!');
        $this->command->info('Admin: admin@lmsebook.com / Admin123!');
        $this->command->info('Demo Admin: demo@lmsebook.com / Demo123!');
    }
}