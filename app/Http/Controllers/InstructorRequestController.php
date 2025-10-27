<?php
// app/Http/Controllers/InstructorRequestController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InstructorRequestController extends Controller
{
    /**
     * Display pending instructor requests
     */
    public function pendingRequests(): Response
    {
        $pendingRequests = User::where('role', 'teacher')
            ->where('status', 'pending')
            ->select([
                'id', 'name', 'username', 'email', 'dob',
                'education_qualification', 'institute', 'experience',
                'created_at' // Removed 'bio' since it doesn't exist
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/InstructorRequests/Pending', [
            'user' => Auth::user(),
            'pendingRequests' => $pendingRequests,
            'stats' => [
                'total_pending' => $pendingRequests->count(),
                'approved_today' => User::where('role', 'teacher')
                    ->where('status', 'active')
                    ->whereDate('updated_at', today())
                    ->count(),
                'total_teachers' => User::where('role', 'teacher')
                    ->where('status', 'active')
                    ->count(),
            ]
        ]);
    }

    /**
     * Submit instructor request (from frontend)
     */
    public function submitRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'dob' => 'required|date|before:-18 years',
            'education_qualification' => 'required|in:HSC,BSC,BA,MA,MSC,PhD,Other',
            'institute' => 'required|string|max:255',
            'experience' => 'nullable|string|max:500',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'dob.before' => 'You must be at least 18 years old to become an instructor.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

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
                'status' => 'pending', // Set status to pending for approval
            ]);

            DB::commit();

            Log::info("New instructor request submitted: {$user->name} ({$user->email})");

            return response()->json([
                'success' => true,
                'message' => 'Your instructor application has been submitted successfully. It will be reviewed by our admin team.',
                'user_id' => $user->id
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting instructor request: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve instructor request
     */
    public function approveRequest($id)
    {
        try {
            $user = User::where('role', 'teacher')
                ->where('status', 'pending')
                ->findOrFail($id);

            $user->update([
                'status' => 'active',
                'email_verified_at' => now(), // Auto-verify email upon approval
            ]);

            Log::info("Instructor request approved: {$user->name} ({$user->email})");

            return response()->json([
                'success' => true,
                'message' => 'Instructor request approved successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving instructor request: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve instructor request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject instructor request
     */
    public function rejectRequest(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::where('role', 'teacher')
                ->where('status', 'pending')
                ->findOrFail($id);

            $user->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
            ]);

            Log::info("Instructor request rejected: {$user->name} ({$user->email}) - Reason: {$request->rejection_reason}");

            return response()->json([
                'success' => true,
                'message' => 'Instructor request rejected successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting instructor request: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject instructor request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all pending requests for API
     */
    public function getPendingRequests()
    {
        try {
            $requests = User::where('role', 'teacher')
                ->where('status', 'pending')
                ->select([
                    'id', 'name', 'username', 'email', 'dob',
                    'education_qualification', 'institute', 'experience',
                    'created_at' // Removed 'bio'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $requests,
                'count' => $requests->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching pending requests: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get request statistics
     */
    public function getRequestStats()
    {
        try {
            $stats = [
                'pending' => User::where('role', 'teacher')->where('status', 'pending')->count(),
                'approved' => User::where('role', 'teacher')->where('status', 'active')->count(),
                'rejected' => User::where('role', 'teacher')->where('status', 'rejected')->count(),
                'total_requests' => User::where('role', 'teacher')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching request stats: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch request statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}