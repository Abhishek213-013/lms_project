<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClassModel;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function superAdmin(): Response
    {
        return Inertia::render('Admin/Dashboard/SuperAdmin', [
            'user' => Auth::user(),
            'stats' => [
                'totalUsers' => User::count(),
                'totalTeachers' => User::where('role', 'teacher')->count(),
                'totalAdmins' => User::where('role', 'admin')->count(),
                'totalStudents' => User::where('role', 'student')->count(),
                'pendingApprovals' => 23,
                'activeCourses' => 89,
                'newStudentsThisWeek' => 42,
                'courseCompletions' => 127,
                'pendingTeacherApprovals' => 8,
                'activeClasses' => 34,
            ],
            'recentActivities' => []
        ]);
    }

    public function admin(): Response
    {
        return Inertia::render('Admin/Dashboard/Admin', [
            'user' => Auth::user(),
            'stats' => [
                'totalTeachers' => User::where('role', 'teacher')->count(),
                'totalStudents' => User::where('role', 'student')->count(),
                'pendingApplications' => 0,
            ]
        ]);
    }

    public function superAdmins(): Response
    {
        return Inertia::render('Admin/Users/SuperAdmins', [
            'superAdmins' => User::where('role', 'super_admin')->get(),
        ]);
    }

    public function admins(): Response
    {
        return Inertia::render('Admin/Users/Admins', [
            'admins' => User::where('role', 'admin')->get(),
        ]);
    }

    public function teachers(): Response
    {
        $teachers = User::where('role', 'teacher')->get();
        $students = User::where('role', 'student')->get();

        return Inertia::render('Admin/Users/Teachers', [
            'teachers' => $teachers,
            'students' => $students,
        ]);
    }

    public function teacherPortal($id = null): Response
    {
        $teacherId = $id ?? Auth::id();
        
        $teacher = User::with(['taughtClasses', 'resources'])->findOrFail($teacherId);
        
        return Inertia::render('Teacher/Portal', [ // Changed from 'Admin/TeacherPortal'
            'teacher' => $teacher,
            'teacherClasses' => $teacher->taughtClasses ?? [],
            'recentResources' => $teacher->resources ?? [],
        ]);
    }

    /**
     * Store a newly created super admin
     */
    public function storeSuperAdmin(Request $request)
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
            return redirect()->back()->withErrors($validator->errors());
        }

        try {
            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'dob' => $request->dob,
                'education_qualification' => $request->education_qualification,
                'institute' => $request->institute,
                'password' => Hash::make($request->password),
                'role' => 'super_admin',
            ]);

            return redirect()->route('super.admins')->with('success', 'Super admin created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Failed to create super admin: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an existing super admin
     */
    public function updateSuperAdmin(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id . '|max:255',
            'email' => 'required|email|unique:users,email,' . $id . '|max:255',
            'dob' => 'required|date',
            'education_qualification' => 'required|in:HSC,BSC,BA,MA,MSC,PhD,Other',
            'institute' => 'required|string|max:255',
            'password' => 'sometimes|nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        try {
            $updateData = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'dob' => $request->dob,
                'education_qualification' => $request->education_qualification,
                'institute' => $request->institute,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return redirect()->route('super.admins')->with('success', 'Super admin updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Failed to update super admin: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a super admin
     */
    public function destroySuperAdmin($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent deleting yourself
            if ($user->id === Auth::id()) {
                return redirect()->back()->withErrors(['message' => 'You cannot delete your own account!']);
            }

            $user->delete();

            return redirect()->route('super.admins')->with('success', 'Super admin deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Failed to delete super admin: ' . $e->getMessage()]);
        }
    }

    /**
     * Store a newly created admin
     */
    public function storeAdmin(Request $request)
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
            return redirect()->back()->withErrors($validator->errors());
        }

        try {
            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'dob' => $request->dob,
                'education_qualification' => $request->education_qualification,
                'institute' => $request->institute,
                'password' => Hash::make($request->password),
                'role' => 'admin',
            ]);

            return redirect()->route('admins')->with('success', 'Admin created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Failed to create admin: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an existing admin
     */
    public function updateAdmin(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id . '|max:255',
            'email' => 'required|email|unique:users,email,' . $id . '|max:255',
            'dob' => 'required|date',
            'education_qualification' => 'required|in:HSC,BSC,BA,MA,MSC,PhD,Other',
            'institute' => 'required|string|max:255',
            'password' => 'sometimes|nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        try {
            $updateData = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'dob' => $request->dob,
                'education_qualification' => $request->education_qualification,
                'institute' => $request->institute,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return redirect()->route('admins')->with('success', 'Admin updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Failed to update admin: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete an admin
     */
    public function destroyAdmin($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent deleting yourself
            if ($user->id === Auth::id()) {
                return redirect()->back()->withErrors(['message' => 'You cannot delete your own account!']);
            }

            $user->delete();

            return redirect()->route('admins')->with('success', 'Admin deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Failed to delete admin: ' . $e->getMessage()]);
        }
    }

    /**
     * Store a newly created teacher
     */
    public function storeTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'dob' => 'required|date',
            'education_qualification' => 'required|in:HSC,BSC,BA,MA,MSC,PhD,Other',
            'institute' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        try {
            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'dob' => $request->dob,
                'education_qualification' => $request->education_qualification,
                'institute' => $request->institute,
                'experience' => $request->experience,
                'password' => Hash::make($request->password),
                'role' => 'teacher',
            ]);

            return redirect()->route('teachers')->with('success', 'Teacher created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Failed to create teacher: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an existing teacher
     */
    public function updateTeacher(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id . '|max:255',
            'email' => 'required|email|unique:users,email,' . $id . '|max:255',
            'dob' => 'required|date',
            'education_qualification' => 'required|in:HSC,BSC,BA,MA,MSC,PhD,Other',
            'institute' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'password' => 'sometimes|nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        try {
            $updateData = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'dob' => $request->dob,
                'education_qualification' => $request->education_qualification,
                'institute' => $request->institute,
                'experience' => $request->experience,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return redirect()->route('teachers')->with('success', 'Teacher updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Failed to update teacher: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a teacher
     */
    public function deleteTeacher($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('teachers')->with('success', 'Teacher deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Failed to delete teacher: ' . $e->getMessage()]);
        }
    }
}