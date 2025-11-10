<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\CourseProgress;
use Carbon\Carbon;

class MyCoursesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get student profile data for avatar
        $student = Student::where('user_id', $user->id)->first();
        
        $coursesData = [
            'enrolled' => $this->getEnrolledCourses($user->id),
            'completed' => $this->getCompletedCourses($user->id),
            'wishlist' => $this->getWishlistCourses($user->id),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $student ? $this->getAvatarUrl($student->profile_picture) : null,
                'student_info' => $student ? [
                    'roll_number' => $student->roll_number,
                    'class' => $student->class ? $student->class->name : 'Not assigned'
                ] : null
            ]
        ];

        return Inertia::render('MyCourses/Index', [
            'courses' => $coursesData
        ]);
    }

    private function getAvatarUrl($profilePicture)
    {
        if (!$profilePicture) {
            return null;
        }
        
        // Check if it's already a full URL
        if (str_starts_with($profilePicture, 'http')) {
            return $profilePicture;
        }
        
        // Return storage URL
        return asset('storage/profile-pictures/' . $profilePicture);
    }

    private function getEnrolledCourses($userId)
    {
        return Enrollment::with(['class' => function($query) {
                $query->select('id', 'name', 'subject', 'description', 'teacher_id', 'category', 'image', 'thumbnail');
            }])
            ->with(['class.teacher' => function($query) {
                $query->select('id', 'name');
            }])
            ->where('user_id', $userId)
            ->where('progress', '<', 100)
            ->where('status', 'active')
            ->orderBy('last_accessed', 'desc')
            ->get()
            ->map(function ($enrollment) {
                $class = $enrollment->class;
                $progress = $this->calculateCourseProgress($enrollment->class_id, $enrollment->user_id);
                
                return [
                    'id' => $class->id,
                    'title' => $class->name,
                    'instructor' => $class->teacher->name ?? 'Unknown Instructor',
                    'thumbnail' => $class->thumbnail_url ?? $class->image_url,
                    'progress' => $progress,
                    'duration' => $this->estimateCourseDuration($class->id),
                    'lessons_completed' => $this->getCompletedLessonsCount($class->id, $enrollment->user_id),
                    'total_lessons' => $this->getTotalLessonsCount($class->id),
                    'category' => $class->category ?? 'General',
                    'last_accessed' => $enrollment->last_accessed ? $enrollment->last_accessed->diffForHumans() : 'Never',
                    'rating' => $this->getCourseRating($class->id)
                ];
            })
            ->toArray();
    }

    private function getCompletedCourses($userId)
    {
        return Enrollment::with(['class' => function($query) {
                $query->select('id', 'name', 'subject', 'description', 'teacher_id', 'category', 'image', 'thumbnail');
            }])
            ->with(['class.teacher' => function($query) {
                $query->select('id', 'name');
            }])
            ->where('user_id', $userId)
            ->where('progress', '>=', 100)
            ->where('status', 'active')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($enrollment) {
                $class = $enrollment->class;
                
                return [
                    'id' => $class->id,
                    'title' => $class->name,
                    'instructor' => $class->teacher->name ?? 'Unknown Instructor',
                    'thumbnail' => $class->thumbnail_url ?? $class->image_url,
                    'progress' => 100,
                    'duration' => $this->estimateCourseDuration($class->id),
                    'lessons_completed' => $this->getCompletedLessonsCount($class->id, $enrollment->user_id),
                    'total_lessons' => $this->getTotalLessonsCount($class->id),
                    'category' => $class->category ?? 'General',
                    'completed_date' => $enrollment->updated_at->format('M d, Y'),
                    'rating' => $this->getCourseRating($class->id),
                    'certificate_available' => true
                ];
            })
            ->toArray();
    }

    private function getWishlistCourses($userId)
    {
        // You'll need to create a wishlist table for this
        // For now, return empty array or implement your wishlist logic
        return [];
        
        // Example implementation if you have a wishlist table:
        /*
        return Wishlist::with(['class' => function($query) {
                $query->select('id', 'name', 'subject', 'description', 'teacher_id', 'category', 'image', 'thumbnail', 'price');
            }])
            ->with(['class.teacher' => function($query) {
                $query->select('id', 'name');
            }])
            ->where('user_id', $userId)
            ->get()
            ->map(function ($wishlist) {
                $class = $wishlist->class;
                
                return [
                    'id' => $class->id,
                    'title' => $class->name,
                    'instructor' => $class->teacher->name ?? 'Unknown Instructor',
                    'thumbnail' => $class->thumbnail_url ?? $class->image_url,
                    'duration' => $this->estimateCourseDuration($class->id),
                    'price' => $class->price ?? 0,
                    'category' => $class->category ?? 'General',
                    'rating' => $this->getCourseRating($class->id),
                    'students' => $this->getEnrolledStudentsCount($class->id)
                ];
            })
            ->toArray();
        */
    }

    private function calculateCourseProgress($classId, $userId)
    {
        $totalLessons = $this->getTotalLessonsCount($classId);
        if ($totalLessons === 0) return 0;
        
        $completedLessons = $this->getCompletedLessonsCount($classId, $userId);
        return round(($completedLessons / $totalLessons) * 100);
    }

    private function getTotalLessonsCount($classId)
    {
        // You'll need to implement this based on your lessons structure
        // For now, return a mock value
        return 20;
        
        // Example implementation:
        // return Lesson::where('class_id', $classId)->count();
    }

    private function getCompletedLessonsCount($classId, $userId)
    {
        // You'll need to implement this based on your progress tracking
        // For now, return a mock value
        return rand(0, 20);
        
        // Example implementation:
        // return CourseProgress::where('class_id', $classId)
        //     ->where('user_id', $userId)
        //     ->where('completed', true)
        //     ->count();
    }

    private function estimateCourseDuration($classId)
    {
        // You'll need to implement this based on your course structure
        // For now, return mock values
        $durations = ['15 hours', '30 hours', '45 hours', '60 hours'];
        return $durations[array_rand($durations)];
        
        // Example implementation:
        // $totalMinutes = Lesson::where('class_id', $classId)->sum('duration_minutes');
        // return round($totalMinutes / 60) . ' hours';
    }

    private function getCourseRating($classId)
    {
        // You'll need to implement ratings system
        // For now, return mock ratings
        return round(4 + (mt_rand(0, 10) / 10), 1);
        
        // Example implementation:
        // return Review::where('class_id', $classId)->avg('rating') ?? 0;
    }

    private function getEnrolledStudentsCount($classId)
    {
        return Enrollment::where('class_id', $classId)
            ->where('status', 'active')
            ->count();
    }

    public function getCoursesData(Request $request)
    {
        $user = $request->user();
        
        $courses = [
            'enrolled' => $this->getEnrolledCourses($user->id),
            'completed' => $this->getCompletedCourses($user->id),
            'wishlist' => $this->getWishlistCourses($user->id)
        ];

        return response()->json($courses);
    }

    public function continueCourse(Request $request, $courseId)
    {
        $user = $request->user();
        
        // Update last accessed time
        Enrollment::where('user_id', $user->id)
            ->where('class_id', $courseId)
            ->update(['last_accessed' => now()]);
        
        // Redirect to course learning page
        return response()->json([
            'redirect_url' => "/course/{$courseId}/learn"
        ]);
    }

    public function enrollCourse(Request $request, $courseId)
    {
        $user = $request->user();
        
        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('class_id', $courseId)
            ->first();
            
        if ($existingEnrollment) {
            return response()->json([
                'message' => 'You are already enrolled in this course',
                'enrollment' => $existingEnrollment
            ]);
        }
        
        // Create new enrollment
        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'class_id' => $courseId,
            'progress' => 0,
            'status' => 'active',
            'enrolled_at' => now(),
            'last_accessed' => now()
        ]);
        
        return response()->json([
            'message' => 'Successfully enrolled in the course',
            'enrollment' => $enrollment
        ]);
    }

    public function removeFromWishlist(Request $request, $courseId)
    {
        $user = $request->user();
        
        // Remove from wishlist
        // Wishlist::where('user_id', $user->id)->where('class_id', $courseId)->delete();
        
        return response()->json([
            'message' => 'Course removed from wishlist'
        ]);
    }
}