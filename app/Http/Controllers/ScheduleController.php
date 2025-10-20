<?php
// app/Http/Controllers/ScheduleController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Schedule;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Added missing import

class ScheduleController extends Controller
{
    // Get schedules for a class
    public function getClassSchedules($classId)
    {
        try {
            Log::info("Fetching schedules for class ID: {$classId}");

            $schedules = Schedule::where('class_id', $classId)
                ->with(['teacher:id,name,email', 'class:id,name'])
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->get()
                ->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'title' => $schedule->title,
                        'description' => $schedule->description,
                        'date' => $schedule->date->toDateString(),
                        'time' => $schedule->time,
                        'duration' => $schedule->duration,
                        'type' => $schedule->type,
                        'status' => $schedule->status,
                        'recurring' => $schedule->recurring,
                        'recurrence_pattern' => $schedule->recurrence_pattern,
                        'recurrence_end_date' => $schedule->recurrence_end_date,
                        'students_attending' => $schedule->students_attending,
                        'created_at' => $schedule->created_at->toISOString(),
                        'teacher' => $schedule->teacher
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $schedules
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching schedules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch schedules'
            ], 500);
        }
    }

    // Create a new schedule
    public function createSchedule(Request $request, $classId)
    {
        try {
            Log::info("Creating schedule for class ID: {$classId}");

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'duration' => 'required|string',
                'type' => 'required|in:regular,extra,revision,test',
                'recurring' => 'boolean',
                'recurrence_pattern' => 'nullable|string',
                'recurrence_end_date' => 'nullable|date'
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
            $schedule = Schedule::create([
                'class_id' => $classId,
                'teacher_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'date' => $request->date,
                'time' => $request->time,
                'duration' => $request->duration,
                'type' => $request->type,
                'status' => 'scheduled',
                'recurring' => $request->recurring ?? false,
                'recurrence_pattern' => $request->recurrence_pattern,
                'recurrence_end_date' => $request->recurrence_end_date,
                'students_attending' => 0 // You can update this based on actual attendance
            ]);

            $schedule->load(['teacher:id,name,email']);

            return response()->json([
                'success' => true,
                'message' => 'Schedule created successfully',
                'data' => [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'description' => $schedule->description,
                    'date' => $schedule->date->toDateString(),
                    'time' => $schedule->time,
                    'duration' => $schedule->duration,
                    'type' => $schedule->type,
                    'status' => $schedule->status,
                    'recurring' => $schedule->recurring,
                    'recurrence_pattern' => $schedule->recurrence_pattern,
                    'recurrence_end_date' => $schedule->recurrence_end_date,
                    'students_attending' => $schedule->students_attending,
                    'created_at' => $schedule->created_at->toISOString(),
                    'teacher' => $schedule->teacher
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create schedule'
            ], 500);
        }
    }

    // Update a schedule
    public function updateSchedule(Request $request, $scheduleId)
    {
        try {
            Log::info("Updating schedule ID: {$scheduleId}");

            // FIXED: Use Auth::id() instead of auth()->id()
            $schedule = Schedule::where('id', $scheduleId)
                ->where('teacher_id', Auth::id())
                ->first();

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found or access denied'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'date' => 'sometimes|date',
                'time' => 'sometimes|date_format:H:i',
                'duration' => 'sometimes|string',
                'type' => 'sometimes|in:regular,extra,revision,test',
                'status' => 'sometimes|in:scheduled,cancelled,completed',
                'recurring' => 'boolean',
                'recurrence_pattern' => 'nullable|string',
                'recurrence_end_date' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $schedule->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully',
                'data' => $schedule
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedule'
            ], 500);
        }
    }

    // Delete a schedule
    public function deleteSchedule($scheduleId)
    {
        try {
            Log::info("Deleting schedule ID: {$scheduleId}");

            // FIXED: Use Auth::id() instead of auth()->id()
            $schedule = Schedule::where('id', $scheduleId)
                ->where('teacher_id', Auth::id())
                ->first();

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found or access denied'
                ], 404);
            }

            $schedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Schedule deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete schedule'
            ], 500);
        }
    }
}