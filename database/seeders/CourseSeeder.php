<?php
// database/seeders/CourseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Create sample teachers
        $teachers = [
            [
                'name' => 'Dr. Sarah Johnson',
                'username' => 'sarahj',
                'email' => 'sarah.johnson@school.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'education_qualification' => 'PhD',
                'institute' => 'University of Education',
                'experience' => '10 years'
            ],
            [
                'name' => 'Mr. Michael Brown',
                'username' => 'michaelb',
                'email' => 'michael.brown@school.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'education_qualification' => 'MSC',
                'institute' => 'State University',
                'experience' => '8 years'
            ]
        ];

        foreach ($teachers as $teacherData) {
            User::create($teacherData);
        }

        $teacherIds = User::where('role', 'teacher')->pluck('id')->toArray();

        // Create classes for grades 1-12 with subjects
        $subjects = ['Mathematics', 'Science', 'English', 'Social Studies', 'Computer Science', 'Physical Education'];
        
        for ($grade = 1; $grade <= 12; $grade++) {
            foreach ($subjects as $index => $subject) {
                ClassModel::create([
                    'name' => "Class {$grade}",
                    'subject' => $subject,
                    'teacher_id' => $teacherIds[array_rand($teacherIds)],
                    'status' => 'active',
                    'description' => "{$subject} for Class {$grade}",
                    'schedule' => json_encode(['days' => ['Mon', 'Wed', 'Fri'], 'time' => '10:00 AM'])
                ]);
            }
        }

        $this->command->info('Sample courses data seeded successfully!');
    }
}