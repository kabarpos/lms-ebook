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
        // Create permissions if they don't exist
        $permissions = [
            'manage users',
            'manage courses',
            'manage categories',
            'manage transactions',
            'manage pricing',
            'manage mentors',
            'view reports',
            'system settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create super admin role with all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // Create admin role with limited permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([
            'manage users',
            'manage courses',
            'manage categories',
            'manage transactions',
            'manage pricing',
            'manage mentors',
            'view reports',
        ]);

        // Create Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@obitobwalms.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('SuperAdmin123!'),
                'email_verified_at' => now(),
                'occupation' => 'System Administrator',
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@obitobwalms.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('Admin123!'),
                'email_verified_at' => now(),
                'occupation' => 'Administrator',
            ]
        );
        $admin->assignRole($adminRole);

        // Create Demo Admin User (for testing)
        $demoAdmin = User::firstOrCreate(
            ['email' => 'demo@obitobwalms.com'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('Demo123!'),
                'email_verified_at' => now(),
                'occupation' => 'Demo Administrator',
            ]
        );
        $demoAdmin->assignRole($adminRole);

        $this->command->info('Admin users created successfully!');
        $this->command->info('Super Admin: superadmin@obitobwalms.com / SuperAdmin123!');
        $this->command->info('Admin: admin@obitobwalms.com / Admin123!');
        $this->command->info('Demo Admin: demo@obitobwalms.com / Demo123!');
    }
}