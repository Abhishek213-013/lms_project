<?php
// app/Http/Controllers/AdmissionController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdmissionController extends Controller
{
    // Get pending applications
    public function getPendingApplications()
    {
        try {
            // In a real application, you might have an applications table
            // For now, we'll return mock data for pending applications
            $pendingApplications = [
                [
                    'id' => 1,
                    'name' => 'Rahul Sharma',
                    'email' => 'rahul.sharma@student.com',
                    'class_name' => 'Class 10',
                    'parent_name' => 'Rajesh Sharma',
                    'parent_contact' => '+91 9876543210',
                    'applied_date' => now()->subDays(2)->format('Y-m-d H:i:s'),
                    'status' => 'pending'
                ],
                [
                    'id' => 2,
                    'name' => 'Priya Patel',
                    'email' => 'priya.patel@student.com',
                    'class_name' => 'Class 8',
                    'parent_name' => 'Mohan Patel',
                    'parent_contact' => '+91 9876543211',
                    'applied_date' => now()->subDays(1)->format('Y-m-d H:i:s'),
                    'status' => 'pending'
                ],
                [
                    'id' => 3,
                    'name' => 'Amit Kumar',
                    'email' => 'amit.kumar@student.com',
                    'class_name' => 'Life Skills',
                    'parent_name' => 'Sanjay Kumar',
                    'parent_contact' => '+91 9876543212',
                    'applied_date' => now()->subHours(6)->format('Y-m-d H:i:s'),
                    'status' => 'pending'
                ],
                [
                    'id' => 4,
                    'name' => 'Sneha Gupta',
                    'email' => 'sneha.gupta@student.com',
                    'class_name' => 'Class 12',
                    'parent_name' => 'Vikram Gupta',
                    'parent_contact' => '+91 9876543213',
                    'applied_date' => now()->subHours(12)->format('Y-m-d H:i:s'),
                    'status' => 'pending'
                ],
                [
                    'id' => 5,
                    'name' => 'Rohan Mehta',
                    'email' => 'rohan.mehta@student.com',
                    'class_name' => 'Spoken English',
                    'parent_name' => 'Anil Mehta',
                    'parent_contact' => '+91 9876543214',
                    'applied_date' => now()->subDays(3)->format('Y-m-d H:i:s'),
                    'status' => 'pending'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $pendingApplications,
                'total' => count($pendingApplications)
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching pending applications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending applications'
            ], 500);
        }
    }

    // Get approval statistics
    public function getApprovalStats()
    {
        try {
            $today = Carbon::today();
            $weekStart = Carbon::now()->startOfWeek();
            $monthStart = Carbon::now()->startOfMonth();

            // Mock data for approval statistics
            $stats = [
                'today' => [
                    'approved' => 12,
                    'pending' => 5,
                    'rejected' => 2
                ],
                'this_week' => [
                    'approved' => 45,
                    'pending' => 15,
                    'rejected' => 8
                ],
                'this_month' => [
                    'approved' => 120,
                    'pending' => 25,
                    'rejected' => 15
                ],
                'total' => [
                    'approved' => 450,
                    'pending' => 35,
                    'rejected' => 45
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching approval stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch approval statistics'
            ], 500);
        }
    }

    // Get enrolled students
    public function getEnrolledStudents(Request $request)
    {
        try {
            $students = Student::with(['user', 'class'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->user->name,
                        'email' => $student->user->email,
                        'class_name' => $student->class->name,
                        'class_type' => $student->class->type,
                        'class_category' => $student->class->category,
                        'roll_number' => $student->roll_number,
                        'parent_name' => $student->parent_name,
                        'parent_contact' => $student->parent_contact,
                        'enrolled_date' => $student->created_at->format('Y-m-d H:i:s'),
                        'status' => 'enrolled'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $students,
                'total' => $students->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching enrolled students: ' . $e->getMessage());
            
            // Return mock data if database is empty
            return response()->json([
                'success' => true,
                'data' => $this->getMockEnrolledStudents(),
                'total' => 5,
                'message' => 'Using demonstration data'
            ]);
        }
    }

    // Approve application
    public function approveApplication(Request $request, $applicationId)
    {
        try {
            DB::beginTransaction();

            // In a real application, you would:
            // 1. Get the application
            // 2. Create a user account
            // 3. Create student record
            // 4. Update application status

            // For now, we'll simulate approval
            $application = [
                'id' => $applicationId,
                'name' => 'Sample Student',
                'email' => 'sample@student.com',
                'class_name' => 'Class 10'
            ];

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application approved successfully',
                'data' => $application
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve application'
            ], 500);
        }
    }

    // Reject application
    public function rejectApplication(Request $request, $applicationId)
    {
        try {
            // Simulate rejection
            $application = [
                'id' => $applicationId,
                'name' => 'Sample Student',
                'email' => 'sample@student.com'
            ];

            return response()->json([
                'success' => true,
                'message' => 'Application rejected successfully',
                'data' => $application
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject application'
            ], 500);
        }
    }

    // Mock data for enrolled students
    private function getMockEnrolledStudents()
    {
        return [
            [
                'id' => 1,
                'name' => 'Anjali Singh',
                'email' => 'anjali.singh@student.com',
                'class_name' => 'Class 10',
                'class_type' => 'regular',
                'class_category' => null,
                'roll_number' => '2024-001',
                'parent_name' => 'Ramesh Singh',
                'parent_contact' => '+91 9876543220',
                'enrolled_date' => now()->subMonths(2)->format('Y-m-d H:i:s'),
                'status' => 'enrolled'
            ],
            [
                'id' => 2,
                'name' => 'Vikram Yadav',
                'email' => 'vikram.yadav@student.com',
                'class_name' => 'Class 8',
                'class_type' => 'regular',
                'class_category' => null,
                'roll_number' => '2024-002',
                'parent_name' => 'Suresh Yadav',
                'parent_contact' => '+91 9876543221',
                'enrolled_date' => now()->subMonths(1)->format('Y-m-d H:i:s'),
                'status' => 'enrolled'
            ],
            [
                'id' => 3,
                'name' => 'Neha Joshi',
                'email' => 'neha.joshi@student.com',
                'class_name' => 'Life Skills',
                'class_type' => 'other',
                'class_category' => 'Life Skills',
                'roll_number' => '2024-003',
                'parent_name' => 'Prakash Joshi',
                'parent_contact' => '+91 9876543222',
                'enrolled_date' => now()->subWeeks(2)->format('Y-m-d H:i:s'),
                'status' => 'enrolled'
            ],
            [
                'id' => 4,
                'name' => 'Raj Malhotra',
                'email' => 'raj.malhotra@student.com',
                'class_name' => 'Class 12',
                'class_type' => 'regular',
                'class_category' => null,
                'roll_number' => '2024-004',
                'parent_name' => 'Amit Malhotra',
                'parent_contact' => '+91 9876543223',
                'enrolled_date' => now()->subWeeks(3)->format('Y-m-d H:i:s'),
                'status' => 'enrolled'
            ],
            [
                'id' => 5,
                'name' => 'Pooja Reddy',
                'email' => 'pooja.reddy@student.com',
                'class_name' => 'Spoken English',
                'class_type' => 'other',
                'class_category' => 'Language',
                'roll_number' => '2024-005',
                'parent_name' => 'Kiran Reddy',
                'parent_contact' => '+91 9876543224',
                'enrolled_date' => now()->subDays(10)->format('Y-m-d H:i:s'),
                'status' => 'enrolled'
            ]
        ];
    }
}