<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $facultyRole = Role::firstOrCreate(['name' => 'faculty']);

        // 2. Create Admin User
        $admin = User::create([
            'name'      => 'Tristan Sangangbayan',
            'email'     => 'tristan.sangangbayan@cvsu.edu.ph',
            'google_id' => null,
            'avatar'    => null,
        ]);

        $admin->assignRole($adminRole);

        // Create Profile for Admin
        $admin->facultyProfile()->create([
            'first_name' => 'Tristan',
            'last_name'  => 'Sangangbayan',
            'branch'     => 'Main Campus',
            'department' => 'Department of Information Technology',
            'email'      => 'tristan.sangangbayan@cvsu.edu.ph',
        ]);

        // 3. Create Faculty User
        $faculty = User::create([
            'name'      => 'Faculty Account',
            'email'     => 'sangangbayant@gmail.com',
            'google_id' => null,
            'avatar'    => null,
        ]);

        $faculty->assignRole($facultyRole);

        // Create Profile for Faculty
        $faculty->facultyProfile()->create([
            'first_name' => 'Faculty',
            'last_name'  => 'Account',
            'branch'     => 'Cavite City Campus',
            'department' => 'Department of Arts and Sciences',
            'email'      => 'sangangbayant@gmail.com',
        ]);
    }
}
