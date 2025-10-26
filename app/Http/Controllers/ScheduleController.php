<?php
// app/Http/Controllers/ScheduleController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Schedule;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display class schedule page
     */
    // In your ScheduleController
    public function classSchedule($classId): Response
    {
        $class = ClassModel::with(['teacher'])->findOrFail($classId);
        
        return Inertia::render('Teacher/Class/Schedule', [
            'classId' => $classId,
            'initialData' => [
                'classInfo' => $this->getClassInfo($classId),
                'schedules' => $this->getClassSchedulesData($classId),
                'scheduleStats' => $this->getScheduleStatsData($classId),
                'scheduleTypes' => $this->getScheduleTypes(),
                'recurrencePatterns' => $this->getRecurrencePatterns(),
                'timeSlots' => $this->getTimeSlots(),
            ]
        ]);
    }

    /**
     * Display create schedule page
     */
    public function createSchedulePage($classId): Response
    {
        $class = ClassModel::findOrFail($classId);
        
        return Inertia::render('Teacher/Class/CreateSchedule', [
            'classInfo' => $this->getClassInfo($classId),
            'scheduleTypes' => $this->getScheduleTypes(),
            'recurrencePatterns' => $this->getRecurrencePatterns(),
            'timeSlots' => $this->getTimeSlots(),
            'defaultDuration' => '60 minutes',
        ]);
    }

    /**
     * Display edit schedule page
     */
    public function editSchedulePage($classId, $scheduleId): Response
    {
        $schedule = Schedule::with(['teacher', 'class'])
            ->where('id', $scheduleId)
            ->where('class_id', $classId)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();

        return Inertia::render('Teacher/Class/EditSchedule', [
            'schedule' => $this->formatScheduleData($schedule),
            'classInfo' => $this->getClassInfo($classId),
            'scheduleTypes' => $this->getScheduleTypes(),
            'recurrencePatterns' => $this->getRecurrencePatterns(),
            'timeSlots' => $this->getTimeSlots(),
        ]);
    }

    /**
     * Display calendar view for class schedule
     */
    public function calendarView($classId): Response
    {
        $class = ClassModel::findOrFail($classId);
        
        return Inertia::render('Teacher/Class/Calendar', [
            'classInfo' => $this->getClassInfo($classId),
            'calendarEvents' => $this->getCalendarEventsData($classId),
            'scheduleStats' => $this->getScheduleStatsData($classId),
        ]);
    }

    // ============ API METHODS ============

    /**
     * Get schedules for a class
     */
    public function getClassSchedules($classId)
    {
        try {
            Log::info("Fetching schedules for class ID: {$classId}");

            $schedules = $this->getClassSchedulesData($classId);

            return response()->json([
                'success' => true,
                'data' => $schedules,
                'classInfo' => $this->getClassInfo($classId),
                'scheduleStats' => $this->getScheduleStatsData($classId)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching schedules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch schedules'
            ], 500);
        }
    }

    /**
     * Get schedules for calendar view
     */
    public function getCalendarSchedules($classId)
    {
        try {
            Log::info("Fetching calendar schedules for class ID: {$classId}");

            $events = $this->getCalendarEventsData($classId);

            return response()->json([
                'success' => true,
                'data' => $events
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching calendar schedules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch calendar schedules'
            ], 500);
        }
    }

            /**
     * Create a new schedule
     */
    public function storeSchedule(Request $request, $classId)
    {
        try {
            Log::info("Creating schedule for class ID: {$classId}", $request->all());

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date',
                'time' => 'required',
                'duration' => 'required',
                'type' => 'required|in:regular,extra,revision,test,meeting,workshop',
                'location' => 'nullable|string|max:255',
                'recurring' => 'nullable|boolean',
                'recurrence_pattern' => 'nullable|in:daily,weekly,monthly',
                'recurrence_end_date' => 'nullable|date|after:date',
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed:', $validator->errors()->toArray());
                return redirect()->back()->withErrors($validator->errors());
            }

            $class = ClassModel::find($classId);
            if (!$class) {
                return redirect()->back()->withErrors(['error' => 'Class not found']);
            }

            $scheduleData = [
                'class_id' => $classId,
                'teacher_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'date' => $request->date,
                'time' => $request->time,
                'duration' => $request->duration,
                'type' => $request->type,
                'location' => $request->location,
                'status' => 'scheduled',
                'recurring' => $request->recurring ?? false,
                'recurrence_pattern' => $request->recurrence_pattern,
                'recurrence_end_date' => $request->recurrence_end_date,
                'students_attending' => 0
            ];

            $schedule = Schedule::create($scheduleData);
            $schedule->load(['teacher:id,name,email', 'class:id,name']);

            Log::info('Schedule created successfully:', ['schedule_id' => $schedule->id]);

            // Return to the schedule page with fresh data
            return redirect()->route('teacher.class.schedule', ['classId' => $classId])
                ->with('success', 'Schedule created successfully')
                ->with('initialData', [
                    'classInfo' => $this->getClassInfo($classId),
                    'schedules' => $this->getClassSchedulesData($classId), // Fresh data
                    'scheduleStats' => $this->getScheduleStatsData($classId),
                    'scheduleTypes' => $this->getScheduleTypes(),
                    'recurrencePatterns' => $this->getRecurrencePatterns(),
                ]);

        } catch (\Exception $e) {
            Log::error('Error creating schedule: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->withErrors(['error' => 'Failed to create schedule: ' . $e->getMessage()]);
        }
    }

    /**
     * Update a schedule
     */
    public function updateSchedule(Request $request, $classId, $scheduleId)
    {
        try {
            Log::info("Updating schedule ID: {$scheduleId} for class ID: {$classId}");

            $schedule = Schedule::where('id', $scheduleId)
                ->where('class_id', $classId)
                ->where('teacher_id', Auth::id())
                ->first();

            if (!$schedule) {
                return redirect()->back()->withErrors(['error' => 'Schedule not found or access denied']);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'date' => 'sometimes|date',
                'time' => 'sometimes',
                'duration' => 'sometimes',
                'type' => 'sometimes|in:regular,extra,revision,test,meeting,workshop',
                'location' => 'nullable|string|max:255',
                'status' => 'sometimes|in:scheduled,cancelled,completed,postponed',
                'recurring' => 'boolean',
                'recurrence_pattern' => 'nullable|in:daily,weekly,monthly',
                'recurrence_end_date' => 'nullable|date|after:date'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed:', $validator->errors()->toArray());
                return redirect()->back()->withErrors($validator->errors());
            }

            // FIX: Use data as-is without additional parsing
            $updateData = $request->all();
            
            $schedule->update($updateData);
            $schedule->load(['teacher:id,name,email', 'class:id,name']);

            Log::info('Schedule updated successfully:', ['schedule_id' => $schedule->id]);

            return redirect()->back()->with('success', 'Schedule updated successfully');

        } catch (\Exception $e) {
            Log::error('Error updating schedule: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->withErrors(['error' => 'Failed to update schedule: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a schedule
     */
    public function destroySchedule($classId, $scheduleId)
    {
        try {
            Log::info("Deleting schedule ID: {$scheduleId} from class ID: {$classId}");

            $schedule = Schedule::where('id', $scheduleId)
                ->where('class_id', $classId)
                ->where('teacher_id', Auth::id())
                ->first();

            if (!$schedule) {
                // FIX: Return Inertia response
                return redirect()->back()->withErrors([
                    'error' => 'Schedule not found or access denied'
                ]);
            }

            $schedule->delete();

            Log::info('Schedule deleted successfully:', ['schedule_id' => $scheduleId]);

            // FIX: Return Inertia response
            return redirect()->back()->with([
                'success' => 'Schedule deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting schedule: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // FIX: Return Inertia response for errors
            return redirect()->back()->withErrors([
                'error' => 'Failed to delete schedule: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk update schedule status
     */
    public function bulkUpdateSchedules(Request $request, $classId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'schedule_ids' => 'required|array',
                'schedule_ids.*' => 'exists:schedules,id',
                'status' => 'required|in:scheduled,cancelled,completed,postponed'
            ]);

            if ($validator->fails()) {
                if ($request->header('X-Inertia')) {
                    return back()->withErrors($validator->errors());
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updatedCount = Schedule::where('class_id', $classId)
                ->where('teacher_id', Auth::id())
                ->whereIn('id', $request->schedule_ids)
                ->update(['status' => $request->status]);

            if ($request->header('X-Inertia')) {
                return back()->with('success', "{$updatedCount} schedules updated successfully");
            }

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} schedules updated successfully",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error in bulk update schedules: ' . $e->getMessage());
            
            if ($request->header('X-Inertia')) {
                return back()->withErrors(['error' => 'Failed to update schedules']);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedules'
            ], 500);
        }
    }

    /**
     * Get upcoming schedules for teacher
     */
    public function getUpcomingSchedules(Request $request)
    {
        try {
            $teacherId = Auth::id();
            $upcomingSchedules = Schedule::with(['class:id,name', 'teacher:id,name'])
                ->where('teacher_id', $teacherId)
                ->where('date', '>=', now()->toDateString())
                ->where('status', 'scheduled')
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->limit(10)
                ->get()
                ->map(function ($schedule) {
                    return $this->formatScheduleData($schedule);
                });

            return response()->json([
                'success' => true,
                'data' => $upcomingSchedules
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching upcoming schedules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch upcoming schedules'
            ], 500);
        }
    }

    // ============ DATA METHODS FOR INERTIA ============

    private function getClassSchedulesData($classId)
    {
        try {
            $schedules = Schedule::where('class_id', $classId)
                ->with(['teacher:id,name,email', 'class:id,name'])
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->get()
                ->map(function ($schedule) {
                    return $this->formatScheduleData($schedule);
                });

            return $schedules;

        } catch (\Exception $e) {
            Log::error('Error in getClassSchedulesData: ' . $e->getMessage());
            return $this->getMockSchedulesData();
        }
    }

    private function getCalendarEventsData($classId)
    {
        try {
            $schedules = Schedule::where('class_id', $classId)
                ->where('date', '>=', now()->subDays(30))
                ->get()
                ->map(function ($schedule) {
                    $start = Carbon::parse($schedule->date . ' ' . $schedule->time);
                    $end = $start->copy()->addMinutes($this->parseDuration($schedule->duration));
                    
                    return [
                        'id' => $schedule->id,
                        'title' => $schedule->title,
                        'start' => $start->toISOString(),
                        'end' => $end->toISOString(),
                        'type' => $schedule->type,
                        'status' => $schedule->status,
                        'className' => $this->getEventClass($schedule->type, $schedule->status),
                        'extendedProps' => [
                            'description' => $schedule->description,
                            'location' => $schedule->location,
                            'teacher' => $schedule->teacher->name ?? 'Unknown',
                        ]
                    ];
                });

            return $schedules;

        } catch (\Exception $e) {
            Log::error('Error in getCalendarEventsData: ' . $e->getMessage());
            return [];
        }
    }

    private function getClassInfo($classId)
    {
        try {
            $class = ClassModel::with(['teacher:id,name'])->find($classId);
            
            if ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'subject' => $class->subject,
                    'grade' => $class->grade,
                    'teacher_name' => $class->teacher->name ?? 'Unknown Teacher',
                    'student_count' => $class->students_count ?? 0,
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

    private function getScheduleStatsData($classId)
    {
        try {
            $totalSchedules = Schedule::where('class_id', $classId)->count();
            $upcomingSchedules = Schedule::where('class_id', $classId)
                ->where('date', '>=', now()->toDateString())
                ->where('status', 'scheduled')
                ->count();
            $completedSchedules = Schedule::where('class_id', $classId)
                ->where('status', 'completed')
                ->count();
            $cancelledSchedules = Schedule::where('class_id', $classId)
                ->where('status', 'cancelled')
                ->count();

            return [
                'total_schedules' => $totalSchedules,
                'upcoming_schedules' => $upcomingSchedules,
                'completed_schedules' => $completedSchedules,
                'cancelled_schedules' => $cancelledSchedules,
                'completion_rate' => $totalSchedules > 0 ? round(($completedSchedules / $totalSchedules) * 100, 1) : 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getScheduleStatsData: ' . $e->getMessage());
            return [
                'total_schedules' => 0,
                'upcoming_schedules' => 0,
                'completed_schedules' => 0,
                'cancelled_schedules' => 0,
                'completion_rate' => 0,
            ];
        }
    }

    private function getScheduleTypes()
    {
        return [
            ['value' => 'regular', 'label' => 'Regular Class', 'color' => 'blue'],
            ['value' => 'extra', 'label' => 'Extra Class', 'color' => 'green'],
            ['value' => 'revision', 'label' => 'Revision', 'color' => 'purple'],
            ['value' => 'test', 'label' => 'Test/Exam', 'color' => 'red'],
            ['value' => 'meeting', 'label' => 'Meeting', 'color' => 'orange'],
            ['value' => 'workshop', 'label' => 'Workshop', 'color' => 'teal'],
        ];
    }

    private function getRecurrencePatterns()
    {
        return [
            ['value' => 'daily', 'label' => 'Daily'],
            ['value' => 'weekly', 'label' => 'Weekly'],
            ['value' => 'monthly', 'label' => 'Monthly'],
        ];
    }

    private function getTimeSlots()
    {
        $slots = [];
        for ($hour = 8; $hour <= 20; $hour++) {
            foreach (['00', '30'] as $minute) {
                $time = sprintf('%02d:%s', $hour, $minute);
                $slots[] = [
                    'value' => $time,
                    'label' => date('g:i A', strtotime($time))
                ];
            }
        }
        return $slots;
    }

    // ============ HELPER METHODS ============

    private function formatScheduleData($schedule)
    {
        return [
            'id' => $schedule->id,
            'title' => $schedule->title,
            'description' => $schedule->description,
            'date' => $schedule->date->toDateString(),
            'time' => $schedule->time,
            'duration' => $schedule->duration,
            'type' => $schedule->type,
            'type_label' => $this->getScheduleTypeLabel($schedule->type),
            'status' => $schedule->status,
            'location' => $schedule->location,
            'recurring' => $schedule->recurring,
            'recurrence_pattern' => $schedule->recurrence_pattern,
            'recurrence_end_date' => $schedule->recurrence_end_date?->toDateString(),
            'students_attending' => $schedule->students_attending,
            'created_at' => $schedule->created_at->toISOString(),
            'teacher' => $schedule->teacher ? [
                'id' => $schedule->teacher->id,
                'name' => $schedule->teacher->name,
                'email' => $schedule->teacher->email
            ] : null,
            'class' => $schedule->class ? [
                'id' => $schedule->class->id,
                'name' => $schedule->class->name
            ] : null,
        ];
    }

    private function getScheduleTypeLabel($type)
    {
        $types = [
            'regular' => 'Regular Class',
            'extra' => 'Extra Class',
            'revision' => 'Revision',
            'test' => 'Test/Exam',
            'meeting' => 'Meeting',
            'workshop' => 'Workshop',
        ];
        
        return $types[$type] ?? 'Unknown';
    }

    private function parseDuration($duration)
    {
        // Convert duration string to minutes
        if (strpos($duration, 'hour') !== false) {
            return (int)$duration * 60;
        }
        if (strpos($duration, 'minute') !== false) {
            return (int)$duration;
        }
        return 60; // Default to 60 minutes
    }

    private function getEventClass($type, $status)
    {
        $classes = [
            'regular' => 'bg-blue-500',
            'extra' => 'bg-green-500',
            'revision' => 'bg-purple-500',
            'test' => 'bg-red-500',
            'meeting' => 'bg-orange-500',
            'workshop' => 'bg-teal-500',
        ];
        
        $baseClass = $classes[$type] ?? 'bg-gray-500';
        
        if ($status === 'cancelled') {
            return $baseClass . ' opacity-50 line-through';
        }
        
        if ($status === 'completed') {
            return $baseClass . ' opacity-75';
        }
        
        return $baseClass;
    }

    // Mock data for development
    private function getMockSchedulesData()
    {
        return [
            [
                'id' => 1,
                'title' => 'Mathematics Chapter 1',
                'description' => 'Introduction to algebra and basic equations',
                'date' => now()->addDays(1)->toDateString(),
                'time' => '10:00',
                'duration' => '60 minutes',
                'type' => 'regular',
                'status' => 'scheduled',
                'location' => 'Room 101',
                'students_attending' => 25,
                'is_past' => false,
                'is_today' => false,
                'is_upcoming' => true,
            ],
            [
                'id' => 2,
                'title' => 'Science Lab Session',
                'description' => 'Practical experiments in chemistry',
                'date' => now()->addDays(2)->toDateString(),
                'time' => '14:00',
                'duration' => '90 minutes',
                'type' => 'extra',
                'status' => 'scheduled',
                'location' => 'Science Lab',
                'students_attending' => 20,
                'is_past' => false,
                'is_today' => false,
                'is_upcoming' => true,
            ],
        ];
    }
}