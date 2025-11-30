<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Payment;
use App\Models\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MyCoursesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        Log::info('MyCoursesController accessed by user: ' . $user->id);
        
        // Get student profile data for avatar
        $student = Student::where('user_id', $user->id)->first();
        
        Log::info('Student found: ' . ($student ? 'Yes' : 'No'));
        
        if ($student) {
            Log::info('Student ID: ' . $student->id);
        }
        
        // Debug: Check what's actually in the database
        $debugInfo = $this->getDebugInfo($user->id, $student);
        
        $coursesData = [
            'enrolled' => $this->getPaidEnrolledCourses($user->id, $student),
            'completed' => $this->getPaidCompletedCourses($user->id, $student),
            'wishlist' => $this->getWishlistCourses($user->id),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $student ? $this->getAvatarUrl($student->profile_picture) : null,
                'student_info' => $student ? [
                    'roll_number' => $student->roll_number,
                    'class' => $student->academicClass->name ?? 'Not assigned'
                ] : null
            ],
            'debug' => $debugInfo
        ];

        Log::info('Courses data prepared:', [
            'enrolled_count' => count($coursesData['enrolled']),
            'completed_count' => count($coursesData['completed']),
            'wishlist_count' => count($coursesData['wishlist'])
        ]);

        return Inertia::render('MyCourses/Index', [
            'courses' => $coursesData
        ]);
    }

    /**
     * Get enrolled courses that have been paid for
     */
    private function getPaidEnrolledCourses($userId, $student = null)
    {
        try {
            Log::info('Getting PAID enrolled courses for user: ' . $userId);
            
            if (!$student) {
                Log::warning('No student profile found for user: ' . $userId);
                return [];
            }
            
            Log::info('Using student ID: ' . $student->id . ' for user: ' . $userId);
            
            // Get enrollments that have successful payments
            $paidEnrollments = DB::table('class_student')
                ->join('payments', function($join) use ($student) {
                    $join->on('class_student.class_id', '=', 'payments.class_id')
                         ->where('payments.student_id', $student->id)
                         ->where('payments.status', 'completed');
                })
                ->where('class_student.student_id', $student->id)
                ->select('class_student.*', 'payments.verified_at', 'payments.payment_method')
                ->get();

            Log::info('Paid enrollments found:', $paidEnrollments->toArray());

            $courses = [];
            
            foreach ($paidEnrollments as $enrollment) {
                $class = ClassModel::with(['teacher:id,name'])
                    ->select('id', 'name', 'subject', 'description', 'teacher_id', 'category', 'image', 'thumbnail', 'type')
                    ->find($enrollment->class_id);

                if ($class) {
                    $progress = $enrollment->progress ?? 0;
                    
                    // Only include courses that are not completed (progress < 100)
                    if ($progress < 100) {
                        $courses[] = [
                            'id' => $class->id,
                            'title' => $class->name,
                            'instructor' => $class->teacher->name ?? 'Unknown Instructor',
                            'thumbnail' => $this->getCourseThumbnail($class),
                            'progress' => $progress,
                            'duration' => $this->estimateCourseDuration($class->id),
                            'lessons_completed' => $this->getCompletedLessonsCount($class->id, $student->id),
                            'total_lessons' => $this->getTotalLessonsCount($class->id),
                            'category' => $class->category ?? ($class->type === 'regular' ? 'Academic' : 'Skill'),
                            'last_accessed' => $enrollment->last_accessed ? 
                                \Carbon\Carbon::parse($enrollment->last_accessed)->diffForHumans() : 'Never',
                            'rating' => $this->getCourseRating($class->id),
                            'payment_verified_at' => $enrollment->verified_at ? 
                                \Carbon\Carbon::parse($enrollment->verified_at)->format('M d, Y') : null,
                            'payment_method' => $enrollment->payment_method
                        ];
                    }
                }
            }

            Log::info('Processed paid enrolled courses:', $courses);

            return $courses;

        } catch (\Exception $e) {
            Log::error('Error getting paid enrolled courses: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get completed courses that have been paid for
     */
    private function getPaidCompletedCourses($userId, $student = null)
    {
        try {
            if (!$student) {
                return [];
            }
            
            // Get completed enrollments that have successful payments
            $paidCompletedEnrollments = DB::table('class_student')
                ->join('payments', function($join) use ($student) {
                    $join->on('class_student.class_id', '=', 'payments.class_id')
                         ->where('payments.student_id', $student->id)
                         ->where('payments.status', 'completed');
                })
                ->where('class_student.student_id', $student->id)
                ->where('class_student.progress', '>=', 100)
                ->select('class_student.*', 'payments.verified_at', 'payments.payment_method')
                ->get();

            $courses = [];
            
            foreach ($paidCompletedEnrollments as $enrollment) {
                $class = ClassModel::with(['teacher:id,name'])
                    ->select('id', 'name', 'subject', 'description', 'teacher_id', 'category', 'image', 'thumbnail', 'type')
                    ->find($enrollment->class_id);

                if ($class) {
                    $courses[] = [
                        'id' => $class->id,
                        'title' => $class->name,
                        'instructor' => $class->teacher->name ?? 'Unknown Instructor',
                        'thumbnail' => $this->getCourseThumbnail($class),
                        'progress' => 100,
                        'duration' => $this->estimateCourseDuration($class->id),
                        'lessons_completed' => $this->getCompletedLessonsCount($class->id, $student->id),
                        'total_lessons' => $this->getTotalLessonsCount($class->id),
                        'category' => $class->category ?? ($class->type === 'regular' ? 'Academic' : 'Skill'),
                        'completed_date' => $enrollment->updated_at ? 
                            \Carbon\Carbon::parse($enrollment->updated_at)->format('M d, Y') : 'Unknown',
                        'rating' => $this->getCourseRating($class->id),
                        'certificate_available' => true,
                        'payment_verified_at' => $enrollment->verified_at ? 
                            \Carbon\Carbon::parse($enrollment->verified_at)->format('M d, Y') : null,
                        'payment_method' => $enrollment->payment_method
                    ];
                }
            }

            return $courses;

        } catch (\Exception $e) {
            Log::error('Error getting paid completed courses: ' . $e->getMessage());
            return [];
        }
    }

    // In MyCoursesController - update the showLearning method with payment check
    public function showLearning(Request $request, $courseId)
    {
        $user = $request->user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            abort(403, 'Student profile not found');
        }
        
        Log::info('Learning page accessed for course: ' . $courseId . ' by user: ' . $user->id . ', student: ' . $student->id);
        
        // Check if user is enrolled in this course AND has paid for it
        $enrollment = DB::table('class_student')
            ->join('payments', function($join) use ($student) {
                $join->on('class_student.class_id', '=', 'payments.class_id')
                     ->where('payments.student_id', $student->id)
                     ->where('payments.status', 'completed');
            })
            ->where('class_student.student_id', $student->id)
            ->where('class_student.class_id', $courseId)
            ->first();
            
        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course or payment is not verified');
        }
        
        // Get course details
        $course = ClassModel::with(['teacher:id,name'])
            ->select('id', 'name', 'subject', 'description', 'teacher_id', 'category', 'image', 'thumbnail', 'type')
            ->find($courseId);
            
        if (!$course) {
            abort(404, 'Course not found');
        }
        
        // Get course resources
        $resources = $this->getCourseResources($courseId);
        
        // Format course data
        $courseData = [
            'id' => $course->id,
            'name' => $course->name,
            'description' => $course->description,
            'subject' => $course->subject,
            'teacher' => [
                'name' => $course->teacher->name ?? 'Unknown Instructor'
            ],
            'progress' => $enrollment->progress ?? 0,
            'thumbnail' => $this->getCourseThumbnail($course)
        ];
        
        return Inertia::render('Learning/Index', [
            'course' => $courseData,
            'resources' => $resources
        ]);
    }

    /**
     * Get resources for a course
     */
    private function getCourseResources($courseId)
    {
        try {
            $resources = Resource::where('class_id', $courseId)
                ->where('status', 'active')
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($resource) {
                    return [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'description' => $resource->description,
                        'file_url' => $resource->file_url,
                        'thumbnail_url' => $resource->thumbnail_url,
                        'is_youtube' => $resource->is_youtube,
                        'youtube_video_id' => $resource->youtube_video_id,
                        'youtube_embed_url' => $resource->youtube_embed_url,
                        'duration' => $resource->duration ?? '15 min',
                        'created_at' => $resource->created_at->format('M d, Y'),
                    ];
                });
                
            return $resources;
            
        } catch (\Exception $e) {
            Log::error('Error getting course resources: ' . $e->getMessage());
            return [];
        }
    }

    private function getDebugInfo($userId, $student = null)
    {
        $studentId = $student ? $student->id : null;
        
        // Check paid enrollments instead of all enrollments
        $paidEnrollmentsCount = $studentId ? DB::table('payments')
            ->where('student_id', $studentId)
            ->where('status', 'completed')
            ->count() : 0;

        // Check if classes exist
        $classesCount = ClassModel::count();

        // Check user's role
        $user = User::find($userId);

        return [
            'user_id' => $userId,
            'student_id' => $studentId,
            'paid_enrollments' => $paidEnrollmentsCount,
            'total_classes' => $classesCount,
            'user_role' => $user->role,
        ];
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

    private function getWishlistCourses($userId)
    {
        // Return empty array for now - implement wishlist later
        return [];
    }

    private function getCourseThumbnail($class)
    {
        if ($class->thumbnail && $class->thumbnail !== 'null') {
            return $this->formatImageUrl($class->thumbnail);
        }
        
        if ($class->image && $class->image !== 'null') {
            return $this->formatImageUrl($class->image);
        }
        
        // Fallback to default thumbnails based on course type
        if ($class->type === 'regular') {
            return '/assets/img/courses/h5_course_thumb01.jpg';
        } else {
            return '/assets/img/courses/h5_course_thumb05.jpg';
        }
    }

    private function formatImageUrl($imagePath)
    {
        if (!$imagePath) return null;
        
        // If it's already a full URL, return as is
        if (str_starts_with($imagePath, 'http')) {
            return $imagePath;
        }
        
        // If it starts with storage/, make it accessible via public storage
        if (str_starts_with($imagePath, 'storage/')) {
            $publicPath = str_replace('storage/', '', $imagePath);
            return asset("storage/{$publicPath}");
        }
        
        // If it's a relative path, assume it's in storage
        if (str_starts_with($imagePath, 'courses/')) {
            return asset("storage/{$imagePath}");
        }
        
        // Default case - prepend /storage/
        return asset("storage/{$imagePath}");
    }

    private function getTotalLessonsCount($classId)
    {
        // Mock value for now - implement based on your actual lessons structure
        return 12;
    }

    private function getCompletedLessonsCount($classId, $studentId)
    {
        // Mock value for now - implement based on your progress tracking
        $enrollment = DB::table('class_student')
            ->where('student_id', $studentId)
            ->where('class_id', $classId)
            ->first();
            
        $progress = $enrollment ? ($enrollment->progress ?? 0) : 0;
        $totalLessons = $this->getTotalLessonsCount($classId);
        
        return round(($progress / 100) * $totalLessons);
    }

    private function estimateCourseDuration($classId)
    {
        $durations = ['4 weeks', '8 weeks', '12 weeks', '16 weeks'];
        return $durations[array_rand($durations)];
    }

    private function getCourseRating($classId)
    {
        return round(4 + (mt_rand(0, 10) / 10), 1);
    }

    // API methods for frontend
    public function getCoursesData(Request $request)
    {
        $user = $request->user();
        $student = Student::where('user_id', $user->id)->first();
        
        $courses = [
            'enrolled' => $this->getPaidEnrolledCourses($user->id, $student),
            'completed' => $this->getPaidCompletedCourses($user->id, $student),
            'wishlist' => $this->getWishlistCourses($user->id)
        ];

        return response()->json($courses);
    }

    public function continueCourse(Request $request, $courseId)
    {
        $user = $request->user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return response()->json([
                'message' => 'Student profile not found'
            ], 404);
        }
        
        // Check if course is paid for before allowing access
        $hasPaid = DB::table('payments')
            ->where('student_id', $student->id)
            ->where('class_id', $courseId)
            ->where('status', 'completed')
            ->exists();
            
        if (!$hasPaid) {
            return response()->json([
                'message' => 'Payment required to access this course'
            ], 403);
        }
        
        // Update last accessed time
        DB::table('class_student')
            ->where('student_id', $student->id)
            ->where('class_id', $courseId)
            ->update(['last_accessed' => now()]);
        
        // Redirect to course learning page
        return response()->json([
            'redirect_url' => route('student.learning', ['courseId' => $courseId])
        ]);
    }

    public function enrollCourse(Request $request, $courseId)
    {
        $user = $request->user();
        
        // Check if student exists
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json([
                'message' => 'Student profile not found'
            ], 404);
        }
        
        // Check if already enrolled AND paid for
        $existingEnrollment = DB::table('class_student')
            ->join('payments', function($join) use ($student) {
                $join->on('class_student.class_id', '=', 'payments.class_id')
                     ->where('payments.student_id', $student->id)
                     ->where('payments.status', 'completed');
            })
            ->where('class_student.student_id', $student->id)
            ->where('class_student.class_id', $courseId)
            ->first();
            
        if ($existingEnrollment) {
            return response()->json([
                'message' => 'You are already enrolled in this course'
            ], 400);
        }
        
        // For free enrollment (if any), create enrollment but require payment for access
        // This method should only be used for free courses
        DB::table('class_student')->insert([
            'student_id' => $student->id,
            'class_id' => $courseId,
            'progress' => 0,
            'enrolled_at' => now(),
            'last_accessed' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json([
            'message' => 'Successfully enrolled in the course. Payment required for full access.'
        ]);
    }

    public function removeFromWishlist(Request $request, $courseId)
    {
        // Implement when you have wishlist functionality
        return response()->json([
            'message' => 'Course removed from wishlist'
        ]);
    }

    /**
     * Check if user has paid for a specific course
     */
    public function checkCoursePaymentStatus(Request $request, $courseId)
    {
        $user = $request->user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return response()->json([
                'has_paid' => false,
                'message' => 'Student profile not found'
            ]);
        }
        
        $payment = Payment::where('student_id', $student->id)
            ->where('class_id', $courseId)
            ->where('status', 'completed')
            ->first();
            
        return response()->json([
            'has_paid' => !is_null($payment),
            'payment' => $payment ? [
                'payment_method' => $payment->payment_method,
                'verified_at' => $payment->verified_at,
                'amount' => $payment->amount
            ] : null
        ]);
    }
}