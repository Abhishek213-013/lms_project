<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Get all teachers
     */
    public function getTeachers(Request $request)
    {
        try {
            Log::info('Fetching all teachers from UserController');
            
            $teachers = User::where('role', 'teacher')
                ->select([
                    'id', 'name', 'username', 'email', 'dob',
                    'education_qualification', 'institute', 'experience',
                    'created_at'
                ])
                ->orderBy('name')
                ->get();

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

    // ============ EXISTING METHODS (Keep these) ============

    public function getSuperAdmins(Request $request)
    {
        try {
            Log::info('Fetching super admins...');
            
            $superAdmins = User::where('role', 'super_admin')
                ->select('id', 'name', 'username', 'email', 'dob', 'education_qualification', 'institute', 'role', 'created_at')
                ->get();
                
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
            $admins = User::where('role', 'admin')
                ->select('id', 'name', 'username', 'email', 'dob', 'education_qualification', 'institute', 'role', 'created_at')
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $admins
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
                'data' => $otherUsers
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

            // Only update password if provided
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

            // Simple deletion without auth check for now
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
            $user = User::findOrFail($id);
            
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
}