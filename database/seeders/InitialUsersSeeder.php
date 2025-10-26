<?php
// database/seeders/InitialUsersSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialUsersSeeder extends Seeder
{
    public function run()
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@skillgro.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'dob' => '1990-01-01',
            'education_qualification' => 'MSC',
            'institute' => 'SkillGro',
            'experience' => '5 years',
        ]);

        // Create Admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@skillgro.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'dob' => '1990-01-01',
            'education_qualification' => 'BSC',
            'institute' => 'SkillGro',
            'experience' => '3 years',
        ]);

        // Create Teacher
        User::create([
            'name' => 'John Teacher',
            'username' => 'teacher',
            'email' => 'teacher@skillgro.com',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
            'dob' => '1985-05-15',
            'education_qualification' => 'MSC',
            'institute' => 'University of Education',
            'experience' => '8 years teaching experience',
        ]);


        $this->command->info('Initial users created successfully!');
        $this->command->info('Super Admin: superadmin / password123');
        $this->command->info('Admin: admin / password123');
        $this->command->info('Teacher: teacher / password123');
    }
}