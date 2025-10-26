<?php
// app/Http/Controllers/AssignmentController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Assignment;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // ADD THIS IMPORT
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentController extends Controller
{
    // Display assignments page for a class
    public function classAssignments($classId): Response
    {
        return Inertia::render('Teacher/Class/Assignments', [
            'classId' => $classId,
            'assignments' => $this->getClassAssignmentsData($classId),
            'classInfo' => $this->getClassInfo($classId),
        ]);
    }

    // Display create assignment page
    public function createAssignmentPage($classId): Response
    {
        return Inertia::render('Teacher/Class/CreateAssignment', [
            'user' => Auth::user(),
            'classId' => $classId,
            'classInfo' => $this->getClassInfo($classId),
        ]);
    }

    // Display edit assignment page
    public function editAssignmentPage($classId, $assignmentId): Response
    {
        $assignment = Assignment::with(['teacher', 'class'])
            ->where('id', $assignmentId)
            ->where('class_id', $classId)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();

        return Inertia::render('Teacher/Class/EditAssignment', [
            'user' => Auth::user(),
            'classId' => $classId,
            'assignment' => $assignment,
            'classInfo' => $this->getClassInfo($classId),
        ]);
    }

    // API: Get assignments for a class
    public function getClassAssignments($classId)
    {
        try {
            Log::info("📡 [DEBUG] Fetching assignments for class ID: {$classId}");

            $assignments = $this->getClassAssignmentsData($classId);

            return response()->json([
                'success' => true,
                'data' => $assignments,
                'classInfo' => $this->getClassInfo($classId)
            ]);
        } catch (\Exception $e) {
            Log::error('❌ [DEBUG] Error fetching assignments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assignments'
            ], 500);
        }
    }

    // API: Create a new assignment
    public function storeAssignment(Request $request, $classId)
    {
        DB::beginTransaction();
        try {
            Log::info("📡 [DEBUG] Creating assignment for class ID: {$classId}");
            Log::info("📡 [DEBUG] Request data: ", $request->all());

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'points' => 'required|integer|min:0',
                'due_date' => 'required|date',
                'attachments' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $class = ClassModel::find($classId);
            if (!$class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Class not found'
                ], 404);
            }

            // Create the assignment
            $assignment = Assignment::create([
                'class_id' => $classId,
                'teacher_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'points' => $request->points,
                'due_date' => $request->due_date,
                'status' => 'active',
                'attachments' => $request->attachments ?: [] // Store attachment metadata
            ]);

            Log::info("✅ [DEBUG] Assignment created successfully with ID: {$assignment->id}");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Assignment created successfully',
                'data' => $this->formatAssignmentData($assignment)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ [DEBUG] Error creating assignment: ' . $e->getMessage());
            Log::error('❌ [DEBUG] Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    // API: Update an assignment
    public function updateAssignment(Request $request, $assignmentId, $classId)
    {
        DB::beginTransaction();
        try {
            Log::info("📡 [DEBUG] Updating assignment ID: {$assignmentId} for class ID: {$classId}");

            $assignment = Assignment::where('id', $assignmentId)
                ->where('class_id', $classId)
                ->where('teacher_id', Auth::id())
                ->first();

            if (!$assignment) {
                Log::warning("❌ [DEBUG] Assignment not found or access denied");
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment not found or access denied'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'points' => 'sometimes|integer|min:0',
                'due_date' => 'sometimes|date',
                'status' => 'sometimes|in:active,draft,completed',
                'attachments' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update the assignment
            $assignment->update([
                'title' => $request->title ?? $assignment->title,
                'description' => $request->description ?? $assignment->description,
                'points' => $request->points ?? $assignment->points,
                'due_date' => $request->due_date ?? $assignment->due_date,
                'status' => $request->status ?? $assignment->status,
                'attachments' => $request->attachments ?? $assignment->attachments
            ]);

            Log::info("✅ [DEBUG] Assignment updated successfully");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Assignment updated successfully',
                'data' => $this->formatAssignmentData($assignment)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ [DEBUG] Error updating assignment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    // API: Delete an assignment
    public function destroyAssignment($assignmentId, $classId)
    {
        DB::beginTransaction();
        try {
            Log::info("📡 [DEBUG] Deleting assignment ID: {$assignmentId} from class ID: {$classId}");

            $assignment = Assignment::where('id', $assignmentId)
                ->where('class_id', $classId)
                ->where('teacher_id', Auth::id())
                ->first();

            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment not found or access denied'
                ], 404);
            }

            $assignment->delete();

            Log::info("✅ [DEBUG] Assignment deleted successfully");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Assignment deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ [DEBUG] Error deleting assignment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    // API: Get assignment statistics
    public function getAssignmentStats($classId, $assignmentId = null)
    {
        try {
            $stats = [
                'total_assignments' => Assignment::where('class_id', $classId)->count(),
                'active_assignments' => Assignment::where('class_id', $classId)->where('status', 'active')->count(),
                'completed_assignments' => Assignment::where('class_id', $classId)->where('status', 'completed')->count(),
                'upcoming_deadlines' => Assignment::where('class_id', $classId)
                    ->where('status', 'active')
                    ->where('due_date', '>', now())
                    ->where('due_date', '<=', now()->addDays(7))
                    ->count(),
            ];

            if ($assignmentId) {
                $assignment = Assignment::find($assignmentId);
                if ($assignment) {
                    $stats['assignment_specific'] = [
                        'submission_rate' => 75, // Mock data
                        'average_score' => 82,   // Mock data
                        'graded_count' => 15,    // Mock data
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching assignment stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assignment statistics'
            ], 500);
        }
    }

    // Data methods for Inertia
    private function getClassAssignmentsData($classId)
    {
        try {
            $assignments = Assignment::where('class_id', $classId)
                ->with(['teacher:id,name,email', 'class:id,name'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($assignment) {
                    return $this->formatAssignmentData($assignment);
                });

            return $assignments;

        } catch (\Exception $e) {
            Log::error('Error in getClassAssignmentsData: ' . $e->getMessage());
            return $this->getMockAssignmentsData();
        }
    }

    private function getClassInfo($classId)
    {
        try {
            $class = ClassModel::with(['teacher:id,name', 'students'])->find($classId);
            
            if ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'subject' => $class->subject,
                    'grade' => $class->grade,
                    'teacher_name' => $class->teacher->name ?? 'Unknown Teacher',
                    'student_count' => $class->students->count() ?? 0,
                ];
            }

            return [
                'id' => $classId,
                'name' => 'Unknown Class',
                'subject' => 'N/A',
                'grade' => 'N/A',
                'teacher_name' => 'Unknown Teacher',
                'student_count' => 0,
            ];

        } catch (\Exception $e) {
            Log::error('Error in getClassInfo: ' . $e->getMessage());
            return [
                'id' => $classId,
                'name' => 'Class Information Unavailable',
                'subject' => 'N/A',
                'grade' => 'N/A',
                'teacher_name' => 'Unknown Teacher',
                'student_count' => 0,
            ];
        }
    }

    private function formatAssignmentData($assignment)
    {
        return [
            'id' => $assignment->id,
            'title' => $assignment->title,
            'description' => $assignment->description,
            'points' => $assignment->points,
            'due_date' => $assignment->due_date->toISOString(),
            'status' => $assignment->status,
            'attachments' => $assignment->attachments ?? [],
            'submitted_count' => 0, // You can implement submission counting later
            'graded_count' => 0,   // You can implement grading counting later
            'created_at' => $assignment->created_at->toISOString(),
            'updated_at' => $assignment->updated_at->toISOString(),
            'teacher' => $assignment->teacher ? [
                'id' => $assignment->teacher->id,
                'name' => $assignment->teacher->name,
                'email' => $assignment->teacher->email
            ] : null,
            'class' => $assignment->class ? [
                'id' => $assignment->class->id,
                'name' => $assignment->class->name
            ] : null,
            'is_past_due' => $assignment->due_date->isPast(),
            'days_until_due' => $assignment->due_date->diffInDays(now(), false),
        ];
    }

    // Mock data for development
    private function getMockAssignmentsData()
    {
        return [
            [
                'id' => 1,
                'title' => 'Mathematics Chapter 1 Exercises',
                'description' => 'Complete all exercises from chapter 1 of your mathematics textbook.',
                'points' => 100,
                'due_date' => now()->addDays(7)->toISOString(),
                'status' => 'active',
                'attachments' => [],
                'submitted_count' => 15,
                'graded_count' => 10,
                'created_at' => now()->subDays(2)->toISOString(),
                'teacher' => ['id' => 1, 'name' => 'John Smith', 'email' => 'john@example.com'],
                'is_past_due' => false,
                'days_until_due' => 7,
            ],
            [
                'id' => 2,
                'title' => 'Science Project Proposal',
                'description' => 'Submit your science project proposal with detailed methodology.',
                'points' => 50,
                'due_date' => now()->addDays(3)->toISOString(),
                'status' => 'active',
                'attachments' => [],
                'submitted_count' => 8,
                'graded_count' => 5,
                'created_at' => now()->subDays(5)->toISOString(),
                'teacher' => ['id' => 1, 'name' => 'John Smith', 'email' => 'john@example.com'],
                'is_past_due' => false,
                'days_until_due' => 3,
            ],
        ];
    }
}