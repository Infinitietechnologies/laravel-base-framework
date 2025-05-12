<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'member']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $merchantRole = Role::firstOrCreate(['name' => 'merchant']);

        // Create Brand Permission
        $permissions = [
            'create brand',
            'view brand',
            'update brand',
            'delete brand',
            'update merchant',
            'delete merchant',
            'view merchant details',
            'update store',
            'delete store',
            'view store details',
            'create tax',
            'update tax',
            'delete tax',
            'view tax'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $superAdminRole->syncPermissions($permissions);
        $merchantRole->syncPermissions($permissions);
    }
}
