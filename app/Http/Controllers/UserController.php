<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    // ============ INERTIA PAGE METHODS ============

    /**
     * Display super admins management page
     */
    public function superAdminsPage(): Response
    {
        return Inertia::render('Admin/Users/SuperAdmins', [
            'user' => Auth::user(),
            'initialData' => [
                'superAdmins' => $this->getSuperAdminsData(),
                'userStats' => $this->getUserStatsData(),
            ]
        ]);
    }

    /**
     * Display admins management page
     */
    public function adminsPage(): Response
    {
        return Inertia::render('Admin/Users/Admins', [
            'user' => Auth::user(),
            'initialData' => [
                'admins' => $this->getAdminsData(),
                'userStats' => $this->getUserStatsData(),
            ]
        ]);
    }

    /**
     * Display teachers management page
     */
    public function teachersPage(): Response
    {
        return Inertia::render('Admin/Users/Teachers', [
            'user' => Auth::user(),
            'initialData' => [
                'teachers' => $this->getTeachersData(),
                'userStats' => $this->getUserStatsData(),
            ]
        ]);
    }

    /**
     * Display students management page
     */
    public function studentsPage(): Response
    {
        return Inertia::render('Admin/Users/Students', [
            'user' => Auth::user(),
            'initialData' => [
                'students' => $this->getStudentsData(),
                'userStats' => $this->getUserStatsData(),
                'classes' => $this->getClassesData(),
            ]
        ]);
    }

    /**
     * Display create user page
     */
    public function createUserPage($role): Response
    {
        $validRoles = ['super_admin', 'admin', 'teacher', 'student'];
        
        if (!in_array($role, $validRoles)) {
            abort(404, 'Invalid user role');
        }

        return Inertia::render('Admin/Users/CreateUser', [
            'user' => Auth::user(),
            'role' => $role,
            'initialData' => [
                'qualifications' => $this->getQualifications(),
                'institutes' => $this->getInstitutes(),
                'classes' => $role === 'student' ? $this->getClassesData() : [],
                'countryCodes' => $this->getCountryCodes(),
            ]
        ]);
    }

    /**
     * Display edit user page
     */
    public function editUserPage($id): Response
    {
        $user = User::with(['student'])->findOrFail($id);
        
        return Inertia::render('Admin/Users/EditUser', [
            'user' => Auth::user(),
            'editingUser' => $user,
            'initialData' => [
                'qualifications' => $this->getQualifications(),
                'institutes' => $this->getInstitutes(),
                'classes' => $user->role === 'student' ? $this->getClassesData() : [],
                'countryCodes' => $this->getCountryCodes(),
            ]
        ]);
    }

    // ============ API METHODS ============

    /**
     * Get all teachers
     */
    public function getTeachers(Request $request)
    {
        try {
            Log::info('Fetching all teachers from UserController');
            
            $teachers = $this->getTeachersData();

            return response()->json([
                'success' => true,
                'data' => $teachers,
                'count' => $teachers->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching teachers from UserController: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teachers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSuperAdmins(Request $request)
    {
        try {
            Log::info('Fetching super admins...');
            
            $superAdmins = $this->getSuperAdminsData();
                
            Log::info('Found ' . $superAdmins->count() . ' super admins');
                
            return response()->json([
                'success' => true,
                'data' => $superAdmins,
                'count' => $superAdmins->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching super admins: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch super admins: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAdmins(Request $request)
    {
        try {
            $admins = $this->getAdminsData();
                
            return response()->json([
                'success' => true,
                'data' => $admins,
                'count' => $admins->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching admins: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch admins',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getOtherUsers(Request $request)
    {
        try {
            $otherUsers = User::whereNotIn('role', ['super_admin', 'admin'])
                ->select('id', 'name', 'username', 'email', 'dob', 'education_qualification', 'institute', 'role', 'created_at')
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $otherUsers,
                'count' => $otherUsers->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching other users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch other users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createSuperAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'dob' => 'required|date',
            'education_qualification' => 'required|in:HSC,BSC,BA,MA,MSC,PhD,Other',
            'institute' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'dob' => $request->dob,
                'education_qualification' => $request->education_qualification,
                'institute' => $request->institute,
                'password' => Hash::make($request->password),
                'role' => 'super_admin',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Super admin created successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'dob' => $user->dob,
                    'education_qualification' => $user->education_qualification,
                    'institute' => $user->institute,
                    'role' => $user->role
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating super admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create super admin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'dob' => 'required|date',
            'education_qualification' => 'required|in:HSC,BSC,BA,MA,MSC,PhD,Other',
            'institute' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'dob' => $request->dob,
                'education_qualification' => $request->education_qualification,
                'institute' => $request->institute,
                'password' => Hash::make($request->password),
                'role' => 'admin',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin created successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'dob' => $user->dob,
                    'education_qualification' => $user->education_qualification,
                    'institute' => $user->institute,
                    'role' => $user->role
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'dob' => 'required|date',
            'education_qualification' => 'required|in:HSC,BSC,BA,MA,MSC,PhD,Other',
            'institute' => 'required|string|max:255',
            'experience' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'dob' => $request->dob,
                'education_qualification' => $request->education_qualification,
                'institute' => $request->institute,
                'experience' => $request->experience,
                'bio' => $request->bio,
                'password' => Hash::make($request->password),
                'role' => 'teacher',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Teacher created successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'dob' => $user->dob,
                    'education_qualification' => $user->education_qualification,
                    'institute' => $user->institute,
                    'experience' => $user->experience,
                    'bio' => $user->bio,
                    'role' => $user->role
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating teacher: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create teacher',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|unique:users,username,' . $id . '|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id . '|max:255',
            'dob' => 'sometimes|required|date',
            'education_qualification' => 'sometimes|required|in:HSC,BSC,BA,MA,MSC,PhD,Other',
            'institute' => 'sometimes|required|string|max:255',
            'experience' => 'sometimes|nullable|string|max:255',
            'bio' => 'sometimes|nullable|string|max:1000',
            'password' => 'sometimes|nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [
                'name' => $request->name ?? $user->name,
                'username' => $request->username ?? $user->username,
                'email' => $request->email ?? $user->email,
                'dob' => $request->dob ?? $user->dob,
                'education_qualification' => $request->education_qualification ?? $user->education_qualification,
                'institute' => $request->institute ?? $user->institute,
                'experience' => $request->experience ?? $user->experience,
                'bio' => $request->bio ?? $user->bio,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'dob' => $user->dob,
                    'education_qualification' => $user->education_qualification,
                    'institute' => $user->institute,
                    'experience' => $user->experience,
                    'bio' => $user->bio,
                    'role' => $user->role
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::find($id);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Prevent deletion of current user
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account'
                ], 422);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUser($id)
    {
        try {
            $user = User::with(['student'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function createStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'country_code' => 'required|string|max:10',
            'parent_contact' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
            ]);

            $rollNumber = $this->generateRollNumber($request->class_id);

            $student = Student::create([
                'user_id' => $user->id,
                'class_id' => $request->class_id,
                'roll_number' => $rollNumber,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'parent_contact' => $request->parent_contact,
                'country_code' => $request->country_code,
                'status' => 'active',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student registered successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'role' => $user->role
                    ],
                    'student' => [
                        'id' => $student->id,
                        'roll_number' => $student->roll_number,
                        'class_id' => $student->class_id,
                        'parent_contact' => $student->full_parent_contact
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating student: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudents(Request $request)
    {
        try {
            $students = $this->getStudentsData();

            return response()->json([
                'success' => true,
                'data' => $students,
                'count' => $students->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching students: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ============ DATA METHODS FOR INERTIA ============

    private function getSuperAdminsData()
    {
        return User::where('role', 'super_admin')
            ->select('id', 'name', 'username', 'email', 'dob', 'education_qualification', 'institute', 'role', 'created_at')
            ->orderBy('name')
            ->get();
    }

    private function getAdminsData()
    {
        return User::where('role', 'admin')
            ->select('id', 'name', 'username', 'email', 'dob', 'education_qualification', 'institute', 'role', 'created_at')
            ->orderBy('name')
            ->get();
    }

    private function getTeachersData()
    {
        return User::where('role', 'teacher')
            ->select([
                'id', 'name', 'username', 'email', 'dob',
                'education_qualification', 'institute', 'experience', 'bio',
                'created_at'
            ])
            ->orderBy('name')
            ->get();
    }

    private function getStudentsData()
    {
        return Student::with(['user', 'class'])
            ->select([
                'id', 'user_id', 'class_id', 'roll_number', 
                'father_name', 'mother_name', 'parent_contact',
                'country_code', 'status', 'created_at', 'updated_at'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'user_id' => $student->user_id,
                    'name' => $student->user->name,
                    'email' => $student->user->email,
                    'class_name' => $student->class->name ?? 'N/A',
                    'roll_number' => $student->roll_number,
                    'father_name' => $student->father_name,
                    'mother_name' => $student->mother_name,
                    'parent_contact' => $student->parent_contact,
                    'status' => $student->status,
                    'enrolled_date' => $student->created_at->format('Y-m-d'),
                ];
            });
    }

    private function getUserStatsData()
    {
        return [
            'total_users' => User::count(),
            'super_admins' => User::where('role', 'super_admin')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'students' => User::where('role', 'student')->count(),
            'active_users' => User::where('status', 'active')->count(),
        ];
    }

    private function getClassesData()
    {
        return ClassModel::select('id', 'name', 'grade', 'subject')
            ->orderBy('name')
            ->get()
            ->map(function ($class) {
                return [
                    'value' => $class->id,
                    'label' => $class->name . ' (' . $class->grade . ' - ' . $class->subject . ')'
                ];
            });
    }

    private function getQualifications()
    {
        return [
            ['value' => 'HSC', 'label' => 'Higher Secondary Certificate'],
            ['value' => 'BSC', 'label' => 'Bachelor of Science'],
            ['value' => 'BA', 'label' => 'Bachelor of Arts'],
            ['value' => 'MA', 'label' => 'Master of Arts'],
            ['value' => 'MSC', 'label' => 'Master of Science'],
            ['value' => 'PhD', 'label' => 'Doctor of Philosophy'],
            ['value' => 'Other', 'label' => 'Other Qualification'],
        ];
    }

    private function getInstitutes()
    {
        // You can make this dynamic by querying from the database
        return [
            'University of Technology',
            'Science College',
            'Arts University',
            'Engineering Institute',
            'Medical College',
            'Business School',
            'Other Institute',
        ];
    }

    private function getCountryCodes()
    {
        return [
            ['value' => '+91', 'label' => '+91 (India)'],
            ['value' => '+1', 'label' => '+1 (USA/Canada)'],
            ['value' => '+44', 'label' => '+44 (UK)'],
            ['value' => '+61', 'label' => '+61 (Australia)'],
            ['value' => '+65', 'label' => '+65 (Singapore)'],
            ['value' => '+971', 'label' => '+971 (UAE)'],
        ];
    }

    // ============ HELPER METHODS ============

    private function generateRollNumber($classId)
    {
        $currentYear = date('Y');
        $classCode = str_pad($classId, 2, '0', STR_PAD_LEFT);
        
        $lastStudent = Student::where('class_id', $classId)
            ->orderBy('id', 'desc')
            ->first();
            
        $sequence = $lastStudent ? (int)substr($lastStudent->roll_number, -3) + 1 : 1;
        $sequenceCode = str_pad($sequence, 3, '0', STR_PAD_LEFT);
        
        return "ST{$currentYear}{$classCode}{$sequenceCode}";
    }

    /**
     * Get user statistics for dashboard
     */
    public function getUserStatistics()
    {
        try {
            $stats = $this->getUserStatsData();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user statistics'
            ], 500);
        }
    }

    /**
     * Bulk user actions (activate, deactivate, delete)
     */
    public function bulkUserActions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:activate,deactivate,delete'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $userIds = $request->user_ids;
            $action = $request->action;
            $currentUserId = Auth::id();

            // Prevent current user from modifying themselves
            if (in_array($currentUserId, $userIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot perform this action on your own account'
                ], 422);
            }

            switch ($action) {
                case 'activate':
                    User::whereIn('id', $userIds)->update(['status' => 'active']);
                    $message = 'Users activated successfully';
                    break;
                
                case 'deactivate':
                    User::whereIn('id', $userIds)->update(['status' => 'inactive']);
                    $message = 'Users deactivated successfully';
                    break;
                
                case 'delete':
                    User::whereIn('id', $userIds)->delete();
                    $message = 'Users deleted successfully';
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'affected_count' => count($userIds)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk user actions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action'
            ], 500);
        }
    }
}