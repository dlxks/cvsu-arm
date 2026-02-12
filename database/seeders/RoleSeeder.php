<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Roles Exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $facultyRole = Role::firstOrCreate(['name' => 'faculty']);

        // 2. Create/Update Specific Admin
        $admin = User::updateOrCreate(
            ['email' => 'tristan.sangangbayan@cvsu.edu.ph'],
            [
                'name' => 'Tristan Sangangbayan',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole($adminRole);

        // 3. Create/Update Specific Faculty
        $facultyUser = User::updateOrCreate(
            ['email' => 'sangangbayant@gmail.com'],
            [
                'name' => 'Faculty Account',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );
        $facultyUser->assignRole($facultyRole);

        // Ensure the manual faculty account has a profile
        $facultyUser->facultyProfile()->updateOrCreate(
            ['user_id' => $facultyUser->id],
            [
                'first_name' => 'Faculty',
                'last_name'  => 'Account',
                'branch'     => 'Main Campus',
                'department' => 'DIT',
                'email'      => 'sangangbayant@gmail.com',
            ]
        );

        // 4. Create Mass Random Data
        // This will create 10 users, assign roles, and create profiles automatically
        User::factory(5)->faculty()->create();
    }
}
