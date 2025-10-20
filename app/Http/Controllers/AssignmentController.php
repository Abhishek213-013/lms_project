<?php
// app/Http/Controllers/AssignmentController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Assignment;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Added missing import

class AssignmentController extends Controller
{
    // Get assignments for a class
    public function getClassAssignments($classId)
    {
        try {
            Log::info("Fetching assignments for class ID: {$classId}");

            $assignments = Assignment::where('class_id', $classId)
                ->with(['teacher:id,name,email', 'class:id,name'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'title' => $assignment->title,
                        'description' => $assignment->description,
                        'points' => $assignment->points,
                        'due_date' => $assignment->due_date->toISOString(),
                        'status' => $assignment->status,
                        'attachments' => $assignment->attachments ?? [],
                        'submitted_count' => 0, // You can add submission tracking later
                        'created_at' => $assignment->created_at->toISOString(),
                        'teacher' => $assignment->teacher
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $assignments
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching assignments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assignments'
            ], 500);
        }
    }

    // Create a new assignment
    public function createAssignment(Request $request, $classId)
    {
        try {
            Log::info("Creating assignment for class ID: {$classId}");

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'points' => 'required|integer|min:0',
                'due_date' => 'required|date',
                'attachments' => 'nullable|array'
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

            // FIXED: Use Auth::id() instead of auth()->id()
            $assignment = Assignment::create([
                'class_id' => $classId,
                'teacher_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'points' => $request->points,
                'due_date' => $request->due_date,
                'status' => 'active',
                'attachments' => $request->attachments ?? []
            ]);

            $assignment->load(['teacher:id,name,email']);

            return response()->json([
                'success' => true,
                'message' => 'Assignment created successfully',
                'data' => [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'points' => $assignment->points,
                    'due_date' => $assignment->due_date->toISOString(),
                    'status' => $assignment->status,
                    'attachments' => $assignment->attachments ?? [],
                    'submitted_count' => 0,
                    'created_at' => $assignment->created_at->toISOString(),
                    'teacher' => $assignment->teacher
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating assignment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create assignment'
            ], 500);
        }
    }

    // Update an assignment
    public function updateAssignment(Request $request, $assignmentId)
    {
        try {
            Log::info("Updating assignment ID: {$assignmentId}");

            // FIXED: Use Auth::id() instead of auth()->id()
            $assignment = Assignment::where('id', $assignmentId)
                ->where('teacher_id', Auth::id())
                ->first();

            if (!$assignment) {
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
                'status' => 'sometimes|in:draft,active,completed',
                'attachments' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $assignment->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Assignment updated successfully',
                'data' => $assignment
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating assignment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update assignment'
            ], 500);
        }
    }

    // Delete an assignment
    public function deleteAssignment($assignmentId)
    {
        try {
            Log::info("Deleting assignment ID: {$assignmentId}");

            // FIXED: Use Auth::id() instead of auth()->id()
            $assignment = Assignment::where('id', $assignmentId)
                ->where('teacher_id', Auth::id())
                ->first();

            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment not found or access denied'
                ], 404);
            }

            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Assignment deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting assignment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete assignment'
            ], 500);
        }
    }
}