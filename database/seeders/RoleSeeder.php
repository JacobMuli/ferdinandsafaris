<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage-tours',
            'manage-bookings',
            'manage-guides',
            'manage-users',
            'manage-settings',
            'view-dashboard',
            'view-manifest',
            'accept-assignments',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create Roles and Assign Permissions
        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo(Permission::all());

        $superAdminRole = Role::findOrCreate('super-admin');
        $superAdminRole->givePermissionTo(Permission::all());

        $guideRole = Role::findOrCreate('guide');
        $guideRole->givePermissionTo([
            'view-dashboard',
            'view-manifest',
            'accept-assignments',
        ]);

        $customerRole = Role::findOrCreate('customer');
        $customerRole->givePermissionTo([
            'view-dashboard',
        ]);

        // Editor: Manage content and tours
        $editorRole = Role::findOrCreate('editor');
        $editorRole->givePermissionTo([
            'view-dashboard',
            'manage-tours',
            'manage-guides',
        ]);

        // Accountant: Manage bookings and billing
        $accountantRole = Role::findOrCreate('accountant');
        $accountantRole->givePermissionTo([
            'view-dashboard',
            'manage-bookings',
        ]);

        // Migrate existing boolean roles to Spatie roles
        $users = User::all();
        foreach ($users as $user) {
            if ($user->is_super_admin || $user->email === 'jacobmwalughs@gmail.com' || $user->email === 'ferdimwalugho@hotmail.com') {
                $user->assignRole('super-admin');
            } elseif ($user->is_admin) {
                $user->assignRole('admin');
            }

            if ($user->tourGuide) {
                $user->assignRole('guide');
            }

            if ($user->customer) {
                $user->assignRole('customer');
            }
        }
    }
}
