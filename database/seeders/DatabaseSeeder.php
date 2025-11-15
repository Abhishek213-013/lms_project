<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@pathshala.com',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create default teacher user
        User::factory()->create([
            'name' => 'Teacher User',
            'email' => 'teacher@pathshala.com',
            'username' => 'teacher',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'education_qualification' => 'M.Ed, B.Sc',
            'experience' => '5 years',
            'email_verified_at' => now(),
        ]);

        // Create default student user
        User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@pathshala.com',
            'username' => 'student',
            'password' => Hash::make('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Call other seeders in order
        $this->call([
            AcademicClassSeeder::class,

        ]);

    }
}