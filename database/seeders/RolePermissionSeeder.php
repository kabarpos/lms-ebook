<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create comprehensive permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Course Management
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'publish courses',
            
            // Category Management
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Transaction Management
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            'process refunds',
            
            // Pricing Management
            'view pricing',
            'create pricing',
            'edit pricing',
            'delete pricing',
            
            // Mentor Management
            'view mentors',
            'create mentors',
            'edit mentors',
            'delete mentors',
            
            // Content Management
            'view content',
            'create content',
            'edit content',
            'delete content',
            
            // Reporting
            'view reports',
            'export reports',
            
            // System
            'system settings',
            'backup system',
            'view logs',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $instructorRole = Role::firstOrCreate(['name' => 'instructor', 'guard_name' => 'web']);
        $mentorRole = Role::firstOrCreate(['name' => 'mentor', 'guard_name' => 'web']);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // Assign permissions to super admin (all permissions)
        $superAdminRole->syncPermissions(Permission::all());
        
        // Assign permissions to admin (most permissions except system critical ones)
        $adminPermissions = Permission::whereNotIn('name', [
            'backup system',
            'system settings',
            'delete users',
        ])->get();
        $adminRole->syncPermissions($adminPermissions);
        
        // Assign permissions to instructor (similar to mentor but can create courses)
        $instructorPermissions = Permission::whereIn('name', [
            'view courses',
            'create courses',
            'edit courses',
            'view content',
            'create content',
            'edit content',
            'view users',
            'view reports',
            'view mentors',
            'create mentors',
            'edit mentors',
        ])->get();
        $instructorRole->syncPermissions($instructorPermissions);
        
        // Assign permissions to mentor (course and content related)
        $mentorPermissions = Permission::whereIn('name', [
            'view courses',
            'edit courses',
            'view content',
            'create content',
            'edit content',
            'view users',
            'view reports',
        ])->get();
        $mentorRole->syncPermissions($mentorPermissions);
        
        // Student role has minimal permissions (handled in application logic)
        $studentPermissions = Permission::whereIn('name', [
            'view courses',
            'view content',
        ])->get();
        $studentRole->syncPermissions($studentPermissions);

        // Create default admin user for backward compatibility
        $user = User::firstOrCreate(
            ['email' => 'team@LMS.com'],
            [
                'name' => 'Team LMS',
                'password' => Hash::make('123123123'),
                'email_verified_at' => now(),
                'occupation' => 'Administrator',
            ]
        );
        $user->assignRole($adminRole);

        $this->command->info('Roles and permissions created successfully!');
    }
}
