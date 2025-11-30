<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard(): Response
    {
        return Inertia::render('Student/Dashboard');
    }

    public function myCourses(): Response
    {
        return Inertia::render('Student/MyCourses');
    }

    public function profile(): Response
    {
        return Inertia::render('Student/Profile');
    }

    public function progress(): Response
    {
        return Inertia::render('Student/Progress');
    }

    public function settings(): Response
    {
        return Inertia::render('Student/Settings');
    }

    public function grades(): Response
    {
        return Inertia::render('Student/Grades');
    }

    // ADD THIS METHOD
    public function learning($courseId): Response
    {
        try {
            // Get the course
            $course = ClassModel::with(['teacher:id,name', 'resources'])
                ->where('status', 'active')
                ->findOrFail($courseId);

            // Check if student is enrolled in this course
            $student = Student::where('user_id', Auth::id())->first();
            
            if (!$student) {
                abort(403, 'Student record not found');
            }

            $isEnrolled = $course->students()->where('student_id', $student->id)->exists();
            
            if (!$isEnrolled) {
                abort(403, 'You are not enrolled in this course');
            }

            // Get student progress for this course
            $enrollment = $course->students()->where('student_id', $student->id)->first();
            $progress = $enrollment ? $enrollment->pivot->progress : 0;

            // Get course resources
            $resources = $course->resources()
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($resource) {
                    return [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'description' => $resource->description,
                        'file_url' => $resource->file_url,
                        'thumbnail_url' => $resource->thumbnail_url,
                        'created_at' => $resource->created_at->format('M d, Y'),
                        'is_youtube' => $resource->is_youtube,
                        'youtube_video_id' => $resource->youtube_video_id,
                    ];
                });

            return Inertia::render('Student/Learning', [
                'course' => [
                    'id' => $course->id,
                    'name' => $course->name,
                    'description' => $course->description,
                    'subject' => $course->subject,
                    'grade' => $course->grade,
                    'type' => $course->type,
                    'teacher' => $course->teacher,
                    'thumbnail' => $course->image_url,
                    'progress' => $progress,
                ],
                'resources' => $resources,
            ]);

        } catch (\Exception $e) {
            abort(404, 'Course not found or access denied');
        }
    }

    public function getStudentStats()
    {
        // Return student stats as JSON
    }
}