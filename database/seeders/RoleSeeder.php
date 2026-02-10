<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $facultyRole = Role::firstOrCreate(['name' => 'faculty']);

        // Create Admin User
        $user = User::create([
            'name' => 'System Admin',
            'email' => 'tristan.sangangbayan@cvsu.edu.ph',
            'google_id' => null,
            'avatar' => null,
        ]);
        // Assign Role
        $user->assignRole($adminRole);

        // Create Faculty User
        $user2 = User::create([
            'name' => 'Faculty Account',
            'email' => 'sangangbayant@gmail.com',
            'google_id' => null,
            'avatar' => null,
        ]);

        $user2->assignRole($facultyRole);
    }
}
