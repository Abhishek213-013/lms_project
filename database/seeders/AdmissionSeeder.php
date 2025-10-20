<?php
// database/seeders/AdmissionSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdmissionSeeder extends Seeder
{
    public function run()
    {
        // Create some classes if they don't exist
        $classes = [];
        for ($grade = 1; $grade <= 12; $grade++) {
            $classes[] = ClassModel::firstOrCreate([
                'name' => "Class {$grade}",
                'subject' => 'General',
                'grade' => $grade,
                'type' => 'regular',
                'status' => 'active',
                'capacity' => 30
            ]);
        }

        // Create other courses
        $otherCourses = [
            ['name' => 'Life Skills', 'category' => 'Life Skills'],
            ['name' => 'Spoken English', 'category' => 'Language'],
            ['name' => 'Computer Basics', 'category' => 'Technology'],
            ['name' => 'Art & Craft', 'category' => 'Arts'],
            ['name' => 'Music', 'category' => 'Arts'],
        ];

        foreach ($otherCourses as $course) {
            ClassModel::firstOrCreate([
                'name' => $course['name'],
                'subject' => $course['category'],
                'category' => $course['category'],
                'type' => 'other',
                'status' => 'active',
                'capacity' => 25
            ]);
        }

        // Create sample students (pending applications)
        $this->createPendingStudents();

        // Create enrolled students
        $this->createEnrolledStudents();
    }

    private function createPendingStudents()
    {
        $pendingStudents = [
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul.sharma@student.com',
                'class' => 'Class 10',
                'parent_name' => 'Rajesh Sharma',
                'parent_contact' => '+91 9876543210',
                'applied_date' => now()->subDays(2)
            ],
            [
                'name' => 'Priya Patel',
                'email' => 'priya.patel@student.com',
                'class' => 'Class 8',
                'parent_name' => 'Mohan Patel',
                'parent_contact' => '+91 9876543211',
                'applied_date' => now()->subDays(1)
            ],
            [
                'name' => 'Amit Kumar',
                'email' => 'amit.kumar@student.com',
                'class' => 'Life Skills',
                'parent_name' => 'Sanjay Kumar',
                'parent_contact' => '+91 9876543212',
                'applied_date' => now()->subHours(6)
            ],
            [
                'name' => 'Sneha Gupta',
                'email' => 'sneha.gupta@student.com',
                'class' => 'Class 12',
                'parent_name' => 'Vikram Gupta',
                'parent_contact' => '+91 9876543213',
                'applied_date' => now()->subHours(12)
            ],
            [
                'name' => 'Rohan Mehta',
                'email' => 'rohan.mehta@student.com',
                'class' => 'Spoken English',
                'parent_name' => 'Anil Mehta',
                'parent_contact' => '+91 9876543214',
                'applied_date' => now()->subDays(3)
            ]
        ];

        foreach ($pendingStudents as $studentData) {
            $user = User::firstOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'username' => Str::slug($studentData['name']),
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'email_verified_at' => null,
                ]
            );

            // For pending applications, we don't create student records yet
            // They will be created when approved
        }
    }

    private function createEnrolledStudents()
    {
        $enrolledStudents = [
            [
                'name' => 'Anjali Singh',
                'email' => 'anjali.singh@student.com',
                'class' => 'Class 10',
                'roll_number' => '2024-001',
                'parent_name' => 'Ramesh Singh',
                'parent_contact' => '+91 9876543220',
                'enrolled_date' => now()->subMonths(2)
            ],
            [
                'name' => 'Vikram Yadav',
                'email' => 'vikram.yadav@student.com',
                'class' => 'Class 8',
                'roll_number' => '2024-002',
                'parent_name' => 'Suresh Yadav',
                'parent_contact' => '+91 9876543221',
                'enrolled_date' => now()->subMonths(1)
            ],
            [
                'name' => 'Neha Joshi',
                'email' => 'neha.joshi@student.com',
                'class' => 'Life Skills',
                'roll_number' => '2024-003',
                'parent_name' => 'Prakash Joshi',
                'parent_contact' => '+91 9876543222',
                'enrolled_date' => now()->subWeeks(2)
            ],
            [
                'name' => 'Raj Malhotra',
                'email' => 'raj.malhotra@student.com',
                'class' => 'Class 12',
                'roll_number' => '2024-004',
                'parent_name' => 'Amit Malhotra',
                'parent_contact' => '+91 9876543223',
                'enrolled_date' => now()->subWeeks(3)
            ],
            [
                'name' => 'Pooja Reddy',
                'email' => 'pooja.reddy@student.com',
                'class' => 'Spoken English',
                'roll_number' => '2024-005',
                'parent_name' => 'Kiran Reddy',
                'parent_contact' => '+91 9876543224',
                'enrolled_date' => now()->subDays(10)
            ]
        ];

        foreach ($enrolledStudents as $studentData) {
            $user = User::firstOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'username' => Str::slug($studentData['name']),
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]
            );

            $class = ClassModel::where('name', $studentData['class'])->first();

            if ($class) {
                Student::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'class_id' => $class->id
                    ],
                    [
                        'roll_number' => $studentData['roll_number'],
                        'parent_name' => $studentData['parent_name'],
                        'parent_contact' => $studentData['parent_contact'],
                    ]
                );
            }
        }
    }
}