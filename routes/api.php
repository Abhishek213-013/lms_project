<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ============ PUBLIC ROUTES (No Authentication Required) ============

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/student', [UserController::class, 'createStudent']); // ADDED: Student registration

// Frontend public routes
Route::prefix('frontend')->group(function () {
    // Course routes
    Route::get('/courses', [FrontendController::class, 'getPublicCourses']);
    Route::get('/courses/featured', [FrontendController::class, 'getFeaturedCourses']);
    Route::get('/courses/{slug}', [FrontendController::class, 'getCourseBySlug']);
    Route::get('/courses/category/{category}', [FrontendController::class, 'getCoursesByCategory']);
    
    // Instructor routes - UPDATED
    Route::get('/instructors', [FrontendController::class, 'getPublicInstructors']);
    Route::get('/instructors/all', [FrontendController::class, 'getAllInstructors']);
    Route::get('/instructors/{id}', [FrontendController::class, 'getInstructorProfile']);
    Route::get('/instructors/{id}/courses', [FrontendController::class, 'getInstructorCourses']);
    
    // Blog routes
    Route::get('/blog/posts', [FrontendController::class, 'getBlogPosts']);
    Route::get('/blog/posts/{slug}', [FrontendController::class, 'getBlogPost']);
    Route::get('/blog/categories', [FrontendController::class, 'getBlogCategories']);
    
    // Stats and general info
    Route::get('/stats', [FrontendController::class, 'getPublicStats']);
    Route::get('/testimonials', [FrontendController::class, 'getTestimonials']);
    
    // Newsletter subscription
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
    
    // Contact form
    Route::post('/contact', [ContactController::class, 'submit']);
});

// Teacher/Instructor Public Routes - FIXED
Route::prefix('teachers')->group(function () {
    // Main teacher endpoints that Vue component is looking for
    Route::get('/', [TeacherController::class, 'getAllTeachers']); // This matches /api/teachers
    Route::get('/public', [TeacherController::class, 'getPublicTeachers']);
    Route::get('/all', [TeacherController::class, 'getAllTeachers']); // Alternative endpoint
    Route::get('/{id}/public', [TeacherController::class, 'getTeacherPublicProfile']);
    Route::get('/{id}/courses', [TeacherController::class, 'getTeacherPublicCourses']);
});

// Simple test route
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now(),
        'status' => 'success'
    ]);
});

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'service' => 'LMS API',
        'version' => '1.0.0'
    ]);
});

// ============ PROTECTED ROUTES (Authentication Required) ============

Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User Management Routes
    Route::prefix('users')->group(function () {
        Route::get('/super-admins', [UserController::class, 'getSuperAdmins']);
        Route::post('/super-admins', [UserController::class, 'createSuperAdmin']);
        Route::get('/admins', [UserController::class, 'getAdmins']);
        Route::post('/admins', [UserController::class, 'createAdmin']);
        Route::get('/other-users', [UserController::class, 'getOtherUsers']);
        Route::post('/teachers', [UserController::class, 'createTeacher']);
        Route::get('/{id}', [UserController::class, 'getUser']);
        Route::put('/{id}', [UserController::class, 'updateUser']);
        Route::delete('/{id}', [UserController::class, 'deleteUser']);
        
        // Teacher-specific user routes
        Route::get('/role/teachers', [UserController::class, 'getTeachers']);
        
        // Student management routes - ADDED
        Route::get('/students', [UserController::class, 'getStudents']);
        Route::get('/students/active', [UserController::class, 'getActiveStudents']);
        Route::get('/students/class/{classId}', [UserController::class, 'getStudentsByClass']);
        Route::get('/students/{id}', [UserController::class, 'getStudent']);
        Route::put('/students/{id}', [UserController::class, 'updateStudent']);
        Route::delete('/students/{id}', [UserController::class, 'deleteStudent']);
    });

    // Teacher Management Routes - UPDATED
    Route::prefix('teachers')->group(function () {
        // Teacher listings
        Route::get('/all', [TeacherController::class, 'getAllTeachers']);
        Route::get('/active', [TeacherController::class, 'getActiveTeachers']);
        Route::get('/featured', [TeacherController::class, 'getFeaturedTeachers']);
        
        // Individual teacher operations
        Route::get('/{id}', [TeacherController::class, 'getTeacher']);
        Route::put('/{id}', [TeacherController::class, 'updateTeacher']);
        Route::post('/{id}/profile', [TeacherController::class, 'updateTeacherProfile']);
        Route::post('/{id}/feature', [TeacherController::class, 'toggleFeatured']);
        
        // Teacher classes and courses
        Route::get('/{id}/classes', [TeacherController::class, 'getTeacherClasses']);
        Route::get('/{id}/courses', [TeacherController::class, 'getTeacherCourses']);
        Route::get('/{id}/enrollments', [TeacherController::class, 'getTeacherEnrollments']);
        
        // Teacher resources
        Route::get('/{id}/resources', [TeacherController::class, 'getTeacherResources']);
        Route::post('/{id}/resources', [TeacherController::class, 'uploadResource']);
        Route::get('/resources/{id}', [TeacherController::class, 'getResource']);
        Route::delete('/resources/{id}', [TeacherController::class, 'deleteResource']);
        
        // Teacher stats and analytics
        Route::get('/{id}/stats', [TeacherController::class, 'getTeacherStats']);
        Route::get('/{id}/performance', [TeacherController::class, 'getTeacherPerformance']);
    });

    // Course Management Routes - UPDATED with subjects route
    Route::prefix('courses')->group(function () {
        // Main course listings
        Route::get('/classes', [CourseController::class, 'getClasses']);
        Route::post('/classes', [CourseController::class, 'createClass']);
        
        // Course categories
        Route::get('/categories', [CourseController::class, 'getCourseCategories']);
        Route::get('/categories/{category}/classes', [CourseController::class, 'getCategoryClasses']);
        
        // Course types
        Route::get('/academic-classes', [CourseController::class, 'getAcademicClasses']);
        Route::get('/other-courses', [CourseController::class, 'getOtherCourses']);
        Route::get('/all', [CourseController::class, 'getAllClasses']);
        
        // Class-specific routes (grades 1-12)
        Route::prefix('classes')->group(function () {
            Route::get('/{grade}/subjects', [CourseController::class, 'getClassSubjects']);
            Route::get('/{grade}/enrollments', [CourseController::class, 'getClassEnrollments']);
            Route::get('/{grade}/students', [CourseController::class, 'getClassStudents']); // ADDED
        });
        
        // Subject management (for regular classes)
        Route::prefix('subjects')->group(function () {
            Route::get('/{subjectId}/teachers', [CourseController::class, 'getSubjectTeachers']);
            Route::post('/{subjectId}/assign-teacher', [CourseController::class, 'assignTeacher']);
            Route::delete('/{subjectId}/teachers/{teacherId}', [CourseController::class, 'removeTeacher']);
        });
        
        // Individual course management (both regular and other courses)
        Route::prefix('{courseId}')->group(function () {
            // Course details and basic operations
            Route::get('/', [CourseController::class, 'getCourse']);
            Route::put('/', [CourseController::class, 'updateCourse']);
            Route::delete('/', [CourseController::class, 'deleteCourse']);
            
            // Course-specific routes
            Route::get('/details', [CourseController::class, 'getOtherCourseDetails']);
            Route::get('/teachers', [CourseController::class, 'getCourseTeachers']);
            Route::post('/teachers', [CourseController::class, 'assignTeacherToCourse']);
            Route::delete('/teachers/{teacherId}', [CourseController::class, 'removeTeacherFromCourse']);
            
            // Enrollment management
            Route::get('/enrollments', [CourseController::class, 'getCourseEnrollments']);
            Route::get('/students', [CourseController::class, 'getCourseStudents']); // ADDED
            
            // Course subjects with teachers - NEW ROUTE ADDED
            Route::get('/subjects', [CourseController::class, 'getCourseSubjects']);
        });
    });

    // Admission Management Routes
    Route::prefix('admissions')->group(function () {
        Route::get('/applications', [AdmissionController::class, 'getPendingApplications']);
        Route::get('/approval-stats', [AdmissionController::class, 'getApprovalStats']);
        Route::get('/enrolled-students', [AdmissionController::class, 'getEnrolledStudents']);
        Route::post('/applications/{applicationId}/approve', [AdmissionController::class, 'approveApplication']);
        Route::post('/applications/{applicationId}/reject', [AdmissionController::class, 'rejectApplication']);
    });

    // Assignment routes
    Route::prefix('assignments')->group(function () {
        Route::get('/class/{classId}', [AssignmentController::class, 'getClassAssignments']);
        Route::post('/class/{classId}', [AssignmentController::class, 'createAssignment']);
        Route::put('/{assignmentId}', [AssignmentController::class, 'updateAssignment']);
        Route::delete('/{assignmentId}', [AssignmentController::class, 'deleteAssignment']);
        
        // Student assignment routes - ADDED
        Route::get('/student/my-assignments', [AssignmentController::class, 'getMyAssignments']);
        Route::post('/{assignmentId}/submit', [AssignmentController::class, 'submitAssignment']);
        Route::get('/{assignmentId}/submission', [AssignmentController::class, 'getMySubmission']);
    });

    // Schedule routes
    Route::prefix('schedules')->group(function () {
        Route::get('/class/{classId}', [ScheduleController::class, 'getClassSchedules']);
        Route::post('/class/{classId}', [ScheduleController::class, 'createSchedule']);
        Route::put('/{scheduleId}', [ScheduleController::class, 'updateSchedule']);
        Route::delete('/{scheduleId}', [ScheduleController::class, 'deleteSchedule']);
        
        // Student schedule routes - ADDED
        Route::get('/student/my-schedule', [ScheduleController::class, 'getMySchedule']);
    });

    // Enhanced resource routes
    Route::prefix('resources')->group(function () {
        Route::get('/class/{classId}', [TeacherController::class, 'getClassResources']);
        Route::post('/upload/{teacherId}', [TeacherController::class, 'uploadResource']);
        Route::delete('/{resourceId}', [TeacherController::class, 'deleteResource']);
        
        // Student resource routes - ADDED
        Route::get('/student/my-resources', [TeacherController::class, 'getMyResources']);
    });

    // Student enrollment routes
    Route::prefix('enrollments')->group(function () {
        Route::post('/course/{courseId}', [CourseController::class, 'enrollStudent']);
        Route::get('/my-courses', [CourseController::class, 'getMyCourses']);
        Route::delete('/course/{courseId}', [CourseController::class, 'unenrollStudent']);
        
        // Enhanced enrollment routes - ADDED
        Route::get('/student/progress', [CourseController::class, 'getMyProgress']);
        Route::get('/student/certificates', [CourseController::class, 'getMyCertificates']);
        Route::post('/course/{courseId}/complete-lesson', [CourseController::class, 'completeLesson']);
    });

    // Student dashboard routes - ADDED
    Route::prefix('student')->group(function () {
        Route::get('/dashboard', [UserController::class, 'getStudentDashboard']);
        Route::get('/profile', [UserController::class, 'getStudentProfile']);
        Route::put('/profile', [UserController::class, 'updateStudentProfile']);
        Route::get('/academic-progress', [UserController::class, 'getAcademicProgress']);
        Route::get('/attendance', [UserController::class, 'getStudentAttendance']);
        Route::get('/grades', [UserController::class, 'getStudentGrades']);
    });

    // Frontend protected routes (for enrolled students)
    Route::prefix('frontend')->group(function () {
        Route::get('/my-progress', [FrontendController::class, 'getMyProgress']);
        Route::get('/my-certificates', [FrontendController::class, 'getMyCertificates']);
        Route::post('/course/{courseId}/complete-lesson', [FrontendController::class, 'completeLesson']);
        
        // Instructor dashboard routes
        Route::get('/instructor/dashboard', [FrontendController::class, 'getInstructorDashboard']);
        Route::get('/instructor/analytics', [FrontendController::class, 'getInstructorAnalytics']);
        
        // Student dashboard routes - ADDED
        Route::get('/student/dashboard', [FrontendController::class, 'getStudentDashboard']);
        Route::get('/student/profile', [FrontendController::class, 'getStudentProfile']);
    });

    // Test auth route
    Route::get('/test-auth', function (Request $request) {
        return response()->json([
            'message' => 'Auth test successful',
            'authenticated' => $request->user() !== null,
            'user' => $request->user(),
            'role' => $request->user()->role
        ]);
    });
});

// Legacy class routes for backward compatibility
Route::prefix('classes')->group(function () {
    Route::get('/', [CourseController::class, 'getClasses']);
    Route::get('/{grade}/subjects', [CourseController::class, 'getClassSubjects']);
});

// Catch-all for undefined API routes
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'available_endpoints' => [
            'public' => [
                'POST /api/login',
                'POST /api/register',
                'POST /api/register/student',
                'GET /api/frontend/courses',
                'GET /api/frontend/instructors',
                'GET /api/frontend/instructors/all',
                'GET /api/teachers',
                'GET /api/teachers/public',
                'GET /api/teachers/all',
                'GET /api/frontend/stats',
                'POST /api/frontend/contact',
                'POST /api/frontend/newsletter/subscribe'
            ],
            'protected' => [
                'GET /api/user',
                'POST /api/logout',
                'GET /api/courses/classes',
                'GET /api/courses/{courseId}/subjects', // ADDED to documentation
                'GET /api/users/*',
                'GET /api/teachers/*',
                'GET /api/admissions/*',
                'GET /api/students/*',
                'GET /api/student/*'
            ]
        ]
    ], 404);
});