<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// Frontend Routes - These should return Inertia responses
Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/courses', [FrontendController::class, 'courses'])->name('courses');
Route::get('/course/{id}', [FrontendController::class, 'courseSingle'])->name('course.single');
Route::get('/instructors', [FrontendController::class, 'instructors'])->name('instructors');
Route::get('/instructor/{id}', [FrontendController::class, 'instructorDetails'])->name('instructor.details');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::get('/blog', [FrontendController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [FrontendController::class, 'blogPost'])->name('blog.post');

// Student Frontend Routes
Route::get('/student-profile', [StudentController::class, 'profile'])->name('student.profile');
Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('student.my-courses');
Route::get('/learning-progress', [StudentController::class, 'progress'])->name('student.progress');
Route::get('/settings', [StudentController::class, 'settings'])->name('student.settings');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/registration', [AuthController::class, 'showRegistration'])->name('registration');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/student-login', [AuthController::class, 'showStudentLogin'])->name('student.login');
Route::post('/student-login', [AuthController::class, 'studentLogin'])->name('student.login.post');
Route::get('/student-registration', [AuthController::class, 'showStudentRegistration'])->name('student.registration');
Route::post('/student-registration', [AuthController::class, 'studentRegister'])->name('student.registration.post');
Route::get('/phone-verification', [AuthController::class, 'showPhoneVerification'])->name('phone.verification');
Route::post('/send-otp', [AuthController::class, 'sendOTP'])->name('send.otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('verify.otp');

// Protected Admin Routes
Route::middleware(['auth'])->group(function () {
    // Super Admin Routes
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/super-admin', [AdminController::class, 'superAdmin'])->name('super.admin');
        
        // Super Admins Management
        Route::prefix('/admin/users/super-admins')->group(function () {
            Route::get('/', [AdminController::class, 'superAdmins'])->name('super.admins');
            Route::post('/', [AdminController::class, 'storeSuperAdmin'])->name('super.admins.store');
            Route::put('/{id}', [AdminController::class, 'updateSuperAdmin'])->name('super.admins.update');
            Route::delete('/{id}', [AdminController::class, 'destroySuperAdmin'])->name('super.admins.destroy');
        });

        // Admins Management
        Route::prefix('/admin/users/admins')->group(function () {
            Route::get('/', [AdminController::class, 'admins'])->name('admins');
            Route::post('/', [AdminController::class, 'storeAdmin'])->name('admins.store');
            Route::put('/{id}', [AdminController::class, 'updateAdmin'])->name('admins.update');
            Route::delete('/{id}', [AdminController::class, 'destroyAdmin'])->name('admins.destroy');
        });
    });

    // Admin Routes
    Route::middleware(['role:admin,super_admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'admin'])->name('admin');
        
        // Teachers Management
        Route::prefix('/admin/users/teachers')->group(function () {
            Route::get('/', [AdminController::class, 'teachers'])->name('teachers');
            Route::post('/', [AdminController::class, 'storeTeacher'])->name('teachers.store');
            Route::put('/{id}', [AdminController::class, 'updateTeacher'])->name('teachers.update');
            Route::delete('/{id}', [AdminController::class, 'deleteTeacher'])->name('teachers.destroy');
        });
        
        // Teacher Portal Route - FIXED: Use the authenticated teacher's ID
        Route::get('/admin/teacher-portal/{id?}', [AdminController::class, 'teacherPortal'])->name('teacher.portal');
        
        // Course Management
        Route::prefix('/admin/courses')->group(function () {
            Route::get('/all-courses', [CourseController::class, 'allCourses'])->name('courses.all');
            Route::get('/regular-courses', [CourseController::class, 'regularCourses'])->name('courses.regular');
            Route::get('/skill-courses', [CourseController::class, 'skillCourses'])->name('courses.skill');
            Route::get('/categories', [CourseController::class, 'categories'])->name('courses.categories');
            Route::get('/enrollments', [CourseController::class, 'enrollments'])->name('courses.enrollments');
            Route::get('/reviews', [CourseController::class, 'reviews'])->name('courses.reviews');
            Route::get('/class/{grade}/subjects', [CourseController::class, 'classSubjects'])->name('courses.class.subjects');
            Route::get('/class/{grade}/subject/{subjectId}/teachers', [CourseController::class, 'subjectTeachers'])->name('courses.subject.teachers');
            Route::get('/category/{category}', [CourseController::class, 'categoryClasses'])->name('courses.category.classes');
            Route::get('/enrollments/class/{grade}', [CourseController::class, 'classEnrollments'])->name('courses.class.enrollments');
            Route::get('/course/{courseId}/details', [CourseController::class, 'courseDetails'])->name('courses.details');
            Route::get('/course/{courseId}/teachers', [CourseController::class, 'courseTeachers'])->name('courses.teachers');
        });
        
        // Admission Management
        Route::prefix('/admin/admissions')->group(function () {
            Route::get('/applications', [AdmissionController::class, 'applications'])->name('admissions.applications');
            Route::get('/approvals', [AdmissionController::class, 'approvals'])->name('admissions.approvals');
            Route::get('/enrolled-students', [AdmissionController::class, 'enrolledStudents'])->name('admissions.enrolled');
        });
    });

    // Teacher Routes - FIXED: Added proper teacher portal route
    Route::middleware(['role:teacher,admin,super_admin'])->group(function () {
        // Teacher Portal Dashboard - FIXED: Use authenticated user's ID
        Route::get('/teacher', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
        Route::get('/teacher/portal', [TeacherController::class, 'portal'])->name('teacher.portal');
        
        // Class Management Routes
        Route::prefix('/teacher/class/{classId}')->group(function () {
            Route::get('/', [TeacherController::class, 'classDashboard'])->name('teacher.class.dashboard');
            Route::get('/assignments', [AssignmentController::class, 'classAssignments'])->name('teacher.class.assignments');
            Route::get('/assignments/create', [AssignmentController::class, 'createAssignmentPage'])->name('teacher.class.assignments.create');
            Route::get('/assignments/{assignmentId}/edit', [AssignmentController::class, 'editAssignmentPage'])->name('teacher.class.assignments.edit');
            Route::get('/resources', [TeacherController::class, 'classResources'])->name('teacher.class.resources');
            
            // Schedule Routes
            Route::get('/schedule', [ScheduleController::class, 'classSchedule'])->name('teacher.class.schedule');
            Route::post('/schedules', [ScheduleController::class, 'storeSchedule'])->name('teacher.class.schedules.store');
            Route::put('/schedules/{scheduleId}', [ScheduleController::class, 'updateSchedule'])->name('teacher.class.schedules.update');
            Route::delete('/schedules/{scheduleId}', [ScheduleController::class, 'destroySchedule'])->name('teacher.class.schedules.destroy');
            Route::get('/schedule/create', [ScheduleController::class, 'createSchedulePage'])->name('teacher.class.schedule.create');
            Route::get('/schedule/{scheduleId}/edit', [ScheduleController::class, 'editSchedulePage'])->name('teacher.class.schedule.edit');
            Route::get('/schedule/calendar', [ScheduleController::class, 'calendarView'])->name('teacher.class.schedule.calendar');
        });
        
        // Resource Management Routes
        Route::prefix('/teacher/resources')->group(function () {
            Route::get('/create/{classId?}', [TeacherController::class, 'createResource'])->name('teacher.resources.create');
        });

        // Analytics & Settings
        Route::get('/teacher/analytics', [TeacherController::class, 'analytics'])->name('teacher.analytics');
        Route::get('/teacher/settings', [TeacherController::class, 'settings'])->name('teacher.settings');
    });

    // Student Routes
    Route::middleware(['role:student'])->group(function () {
        Route::get('/student', [StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/student/my-courses', [StudentController::class, 'myCourses'])->name('student.my-courses');
        Route::get('/student/assignments', [AssignmentController::class, 'studentAssignments'])->name('student.assignments');
        Route::get('/student/schedule', [ScheduleController::class, 'studentSchedule'])->name('student.schedule');
        Route::get('/student/grades', [StudentController::class, 'grades'])->name('student.grades');
        Route::get('/student/profile', [StudentController::class, 'profile'])->name('student.profile');
        Route::get('/student/progress', [StudentController::class, 'progress'])->name('student.progress');
        Route::get('/student/settings', [StudentController::class, 'settings'])->name('student.settings');
    });
});

// API Routes - Return JSON responses
Route::middleware(['auth'])->prefix('api')->group(function () {
    // Course API Routes
    Route::prefix('courses')->group(function () {
        Route::get('/classes', [CourseController::class, 'getClasses']);
        Route::post('/classes', [CourseController::class, 'createClass']);
        Route::get('/class/{grade}/subjects', [CourseController::class, 'getClassSubjects']);
        Route::get('/subject/{subjectId}/teachers', [CourseController::class, 'getSubjectTeachers']);
        Route::post('/subject/{subjectId}/assign-teacher', [CourseController::class, 'assignTeacher']);
        Route::delete('/subject/{subjectId}/teacher/{teacherId}', [CourseController::class, 'removeTeacher']);
        Route::get('/{courseId}', [CourseController::class, 'getCourse']);
        Route::get('/class/{grade}/enrollments', [CourseController::class, 'getClassEnrollments']);
        Route::get('/categories', [CourseController::class, 'getCourseCategories']);
        Route::get('/category/{category}/classes', [CourseController::class, 'getCategoryClasses']);
        Route::get('/academic-classes', [CourseController::class, 'getAcademicClasses']);
        Route::get('/other-courses', [CourseController::class, 'getOtherCourses']);
        Route::get('/all-classes', [CourseController::class, 'getAllClasses']);
        Route::put('/{courseId}', [CourseController::class, 'updateCourse']);
        Route::delete('/{courseId}', [CourseController::class, 'deleteCourse']);
        Route::get('/{courseId}/teachers', [CourseController::class, 'getCourseTeachers']);
        Route::post('/{courseId}/assign-teacher', [CourseController::class, 'assignTeacherToCourse']);
        Route::delete('/{courseId}/teacher/{teacherId}', [CourseController::class, 'removeTeacherFromCourse']);
        Route::post('/{courseId}/enroll', [CourseController::class, 'enrollStudent']);
        Route::post('/{courseId}/unenroll', [CourseController::class, 'unenrollStudent']);
        Route::get('/user/my-courses', [CourseController::class, 'getMyCourses']);
        Route::get('/{courseId}/subjects', [CourseController::class, 'getCourseSubjects']);
        
        // Debug route for course issues
        Route::get('/debug/courses', [CourseController::class, 'debugCourses']);
    });

    // User API Routes
    Route::prefix('users')->group(function () {
        Route::get('/other-users', [UserController::class, 'getOtherUsers']);
        Route::get('/profile', [UserController::class, 'getProfile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::get('/students', [UserController::class, 'getStudents'])->name('users.students');
        Route::get('/teachers', [UserController::class, 'getTeachers'])->name('users.teachers');
        Route::get('/admins', [UserController::class, 'getAdmins'])->name('users.admins');
    });

    // Teacher API Routes
    Route::prefix('teachers')->group(function () {
        Route::get('/', [TeacherController::class, 'getAllTeachers']);
        Route::get('/public', [TeacherController::class, 'getPublicTeachers']);
        Route::get('/active', [TeacherController::class, 'getActiveTeachers']);
        Route::get('/featured', [TeacherController::class, 'getFeaturedTeachers']);
        Route::get('/{id}', [TeacherController::class, 'getTeacher']);
        Route::get('/{id}/classes', [TeacherController::class, 'getTeacherClasses']);
        Route::get('/{id}/resources', [TeacherController::class, 'getTeacherResources']);
        Route::post('/{id}/resources', [TeacherController::class, 'uploadResource']);
        Route::put('/{id}/profile', [TeacherController::class, 'updateTeacherProfile']);
        Route::get('/{id}/public-profile', [TeacherController::class, 'getTeacherPublicProfile']);
        Route::get('/{id}/public-courses', [TeacherController::class, 'getTeacherPublicCourses']);
        
        // FIXED: Add teacher portal data route
        Route::get('/{id}/portal-data', [TeacherController::class, 'getTeacherPortalData']);
    });
    
    // Class API Routes
    Route::prefix('classes')->group(function () {
        Route::get('/{classId}/resources', [TeacherController::class, 'getClassResources']);
        Route::get('/{classId}/students', [CourseController::class, 'getClassStudents']);
        Route::get('/{classId}/info', [CourseController::class, 'getClassInfo']);
        Route::get('/{classId}/schedule', [ScheduleController::class, 'getClassSchedule']);
    });
    
    // Resource API Routes - Complete set
    Route::prefix('resources')->group(function () {
        // Get resources
        Route::get('/', [TeacherController::class, 'getAllResources']);
        Route::get('/class/{classId}', [TeacherController::class, 'getClassResources']);
        
        // Upload resources
        Route::post('/', [TeacherController::class, 'uploadResource']);
        Route::post('/upload/{teacherId}', [TeacherController::class, 'uploadResource']);
        
        // Manage resources
        Route::delete('/{resourceId}', [TeacherController::class, 'deleteResource']);
        Route::post('/{resourceId}/download', [TeacherController::class, 'updateDownloadCount']);
    });

    // Assignment API Routes
    Route::prefix('assignments')->group(function () {
        // Get assignments for a class
        Route::get('/class/{classId}', [AssignmentController::class, 'getClassAssignments']);
        
        // Create new assignment
        Route::post('/class/{classId}', [AssignmentController::class, 'storeAssignment']);
        
        // Update assignment
        Route::put('/{assignmentId}/class/{classId}', [AssignmentController::class, 'updateAssignment']);
        
        // Delete assignment
        Route::delete('/{assignmentId}/class/{classId}', [AssignmentController::class, 'destroyAssignment']);
        
        // Get assignment statistics
        Route::get('/class/{classId}/stats', [AssignmentController::class, 'getAssignmentStats']);
        Route::get('/{assignmentId}/class/{classId}/stats', [AssignmentController::class, 'getAssignmentStats']);
        
        // Assignment submissions
        Route::get('/{assignmentId}/submissions', [AssignmentController::class, 'getSubmissions']);
        Route::post('/{assignmentId}/submissions', [AssignmentController::class, 'storeSubmission']);
        Route::put('/{assignmentId}/submissions/{submissionId}', [AssignmentController::class, 'gradeSubmission']);
    });

    // Schedule API Routes
    Route::prefix('schedules')->group(function () {
        // Get schedules for a class
        Route::get('/class/{classId}', [ScheduleController::class, 'getClassSchedules']);
        
        // Get calendar schedules
        Route::get('/class/{classId}/calendar', [ScheduleController::class, 'getCalendarSchedules']);
        
        // Create new schedule
        Route::post('/class/{classId}', [ScheduleController::class, 'storeSchedule']);
        
        // Update schedule
        Route::put('/{scheduleId}', [ScheduleController::class, 'updateSchedule']);
        
        // Delete schedule
        Route::delete('/{scheduleId}', [ScheduleController::class, 'destroySchedule']);
        
        // Bulk update schedules
        Route::post('/class/{classId}/bulk-update', [ScheduleController::class, 'bulkUpdateSchedules']);
        
        // Get upcoming schedules for teacher
        Route::get('/upcoming', [ScheduleController::class, 'getUpcomingSchedules']);
    });

    // Admission API Routes
    Route::prefix('admissions')->group(function () {
        Route::get('/applications', [AdmissionController::class, 'getApplications']);
        Route::get('/approvals', [AdmissionController::class, 'getApprovals']);
        Route::get('/enrolled-students', [AdmissionController::class, 'getEnrolledStudents']);
        Route::post('/applications/{id}/approve', [AdmissionController::class, 'approveApplication']);
        Route::post('/applications/{id}/reject', [AdmissionController::class, 'rejectApplication']);
        Route::post('/applications', [AdmissionController::class, 'storeApplication']);
        Route::get('/applications/{id}', [AdmissionController::class, 'getApplication']);
    });

    // File Upload Routes
    Route::prefix('upload')->group(function () {
        Route::post('/assignment-attachments', [AssignmentController::class, 'uploadAttachments']);
        Route::post('/resource-files', [TeacherController::class, 'uploadResourceFiles']);
        Route::delete('/files/{filename}', [TeacherController::class, 'deleteFile']);
        Route::post('/profile-image', [UserController::class, 'uploadProfileImage']);
    });

    // Dashboard and Analytics API Routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [AdminController::class, 'getDashboardStats']);
        Route::get('/teacher-stats', [TeacherController::class, 'getTeacherStats']);
        Route::get('/admin-stats', [AdminController::class, 'getAdminStats']);
        Route::get('/student-stats', [StudentController::class, 'getStudentStats']);
    });
});

// Public API Routes (No auth required)
Route::prefix('api')->group(function () {
    Route::get('/public/courses', [CourseController::class, 'getPublicCourses']);
    Route::get('/public/teachers', [TeacherController::class, 'getPublicTeachers']);
    Route::get('/public/courses/{id}', [CourseController::class, 'getPublicCourseDetails']);
    Route::get('/public/teachers/{id}', [TeacherController::class, 'getTeacherPublicProfile']);
    Route::get('/public/categories', [CourseController::class, 'getPublicCategories']);
    
    // Admission public routes
    Route::post('/public/admissions/apply', [AdmissionController::class, 'storeApplication']);
    Route::get('/public/admissions/status/{token}', [AdmissionController::class, 'checkApplicationStatus']);
    
    // Health check and CSRF
    Route::get('/health', function () {
        return response()->json(['status' => 'OK', 'timestamp' => now()]);
    });
    
    Route::get('/sanctum/csrf-cookie', function () {
        return response()->json(['message' => 'CSRF cookie set']);
    });
});

// Public file access routes (if needed)
Route::get('/storage/resources/{filename}', function ($filename) {
    $path = storage_path('app/public/resources/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
})->name('storage.resources');

Route::get('/storage/assignments/{filename}', function ($filename) {
    $path = storage_path('app/public/assignments/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
})->name('storage.assignments');

// Fallback route for SPA - Make sure this is the last route
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|storage|assets).*$'); // Exclude API and storage routes from SPA catch-all