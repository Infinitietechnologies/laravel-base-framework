<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
//        User::factory()->create([
//            'name' => 'Admin',
//            'mobile' => 9876543210,
//            'email' => 'admin@gmail.com',
//            'password' => bcrypt('12345678'),
//            'status' => 'active',
//            'referral_code' => 'ADMIN',
//        ]);

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'member']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $merchantRole = Role::firstOrCreate(['name' => 'merchant']);

//        $super_admin = User::find(1);
//        $super_admin->assignRole($superAdminRole);
//        // Create admin user
        $admin = User::create([
            'name' => 'Admin 2',
            'mobile' => 9876543210,
            'status' => 'active',
            'email' => 'admin12@example.com',
            'password' => Hash::make('12345678')
        ]);
        $admin->assignRole($superAdminRole);
//
//        // Create normal user
//        $user = User::create([
//            'name' => 'Normal User',
//            'mobile' => 1234567890,
//            'status' => 'active',
//            'email' => 'user@example.com',
//            'password' => Hash::make('password')
//        ]);
//        $user->assignRole($userRole);
    }
}
