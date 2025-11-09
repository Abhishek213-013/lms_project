<?php

use Illuminate\Http\Request;
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
use App\Http\Controllers\InstructorRequestController;
use App\Http\Controllers\Admin\ContentManagementController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\VideoProxyController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\MyCoursesController;
use App\Http\Controllers\LearningProgressController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\InstructorController; // Add this line
use Illuminate\Support\Facades\Route;

// ============ PUBLIC ROUTES ============

// Frontend Routes
Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/courses', [FrontendController::class, 'courses'])->name('courses');
Route::get('/course/{id}', [FrontendController::class, 'courseSingle'])->name('course.single');
Route::get('/instructors', [FrontendController::class, 'instructors'])->name('instructors');
Route::get('/instructor/{id}', [FrontendController::class, 'instructorDetails'])->name('instructor.details');
Route::post('/instructors/reorder', [FrontendController::class, 'reorder'])->name('instructors.reorder');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::get('/blog', [FrontendController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [FrontendController::class, 'blogPost'])->name('blog.post');

// Language switching routes
Route::post('/switch-language/{lang}', [FrontendController::class, 'switchLanguage'])->name('switch.language');
Route::get('/switch-language/{lang}', [FrontendController::class, 'switchLanguage'])->name('switch.language.get');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/registration', [AuthController::class, 'showRegistration'])->name('registration');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// TEACHER REGISTRATION ROUTES
Route::get('/teacher-registration', [AuthController::class, 'showTeacherRegistration'])->name('teacher.registration');
Route::post('/teacher-registration', [AuthController::class, 'teacherRegister'])->name('teacher.registration.post');

Route::get('/student-login', [AuthController::class, 'showStudentLogin'])->name('student.login');
Route::post('/student-login', [AuthController::class, 'studentLogin'])->name('student.login.post');
Route::get('/student-registration', [AuthController::class, 'showStudentRegistration'])->name('student.registration');
Route::post('/student-registration', [AuthController::class, 'studentRegister'])->name('student.registration.post');
Route::get('/phone-verification', [AuthController::class, 'showPhoneVerification'])->name('phone.verification');
Route::post('/send-otp', [AuthController::class, 'sendOTP'])->name('send.otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('verify.otp');

// Public API Routes
Route::prefix('api')->middleware('web')->group(function () {
    Route::get('/public/courses', [CourseController::class, 'getPublicCourses']);
    Route::get('/public/teachers', [TeacherController::class, 'getPublicTeachers']);
    Route::get('/public/courses/{id}', [CourseController::class, 'getPublicCourseDetails']);
    Route::get('/public/teachers/{id}', [TeacherController::class, 'getTeacherPublicProfile']);
    Route::get('/public/categories', [CourseController::class, 'getPublicCategories']);
    
    // Search classes route
    Route::get('/search-classes', [CourseController::class, 'searchClasses'])->name('api.search-classes');
    
    // Content API Routes (Public - for frontend pages)
    Route::get('/content/about/{language?}', [ContentController::class, 'getAboutContent'])->name('api.content.about');
    Route::get('/content/home/{language?}', [ContentController::class, 'getHomeContent'])->name('api.content.home');
    Route::get('/content/{language?}', [ContentController::class, 'getAllContent'])->name('api.content.all');
    
    // Language API Routes
    Route::post('/switch-language/{lang}', [FrontendController::class, 'switchLanguage'])->name('api.switch.language');
    Route::get('/current-language', function() {
        return response()->json([
            'language' => session('lang', 'en'),
            'available_languages' => ['en', 'bn']
        ]);
    })->name('api.current.language');
    
    // Instructor Request Public Route
    Route::post('/public/instructor-requests', [InstructorRequestController::class, 'submitRequest'])->name('api.instructor-requests.submit');
    
    // Admission public routes
    Route::post('/public/admissions/apply', [AdmissionController::class, 'storeApplication']);
    Route::get('/public/admissions/status/{token}', [AdmissionController::class, 'checkApplicationStatus']);
    
    // Video Proxy Public Routes
    Route::get('/video-proxy', [VideoProxyController::class, 'proxy']);
    Route::get('/video-player', [VideoProxyController::class, 'player']);
    
    // Direct Video Stream Routes
    Route::get('/youtube-direct-stream', [VideoController::class, 'getYouTubeDirectStream']);
    Route::get('/video-proxy/{videoId}', [VideoController::class, 'proxyVideo']);
    
    // User Management Public Routes (for registration)
    Route::post('/public/users/check-username', [UserController::class, 'checkUsernameAvailability']);
    Route::post('/public/users/check-email', [UserController::class, 'checkEmailAvailability']);
    
    // 🔥 PROFILE PICTURE UPLOAD ROUTE - MOVE TO AUTHENTICATED SECTION
    // Route::post('/profile-picture/teacher/{id}/upload', [TeacherController::class, 'uploadProfilePicture'])
    //     ->name('api.profile-picture.upload');
    
    // Health check
    Route::get('/health', function () {
        return response()->json(['status' => 'OK', 'timestamp' => now()]);
    });
});

// Custom Video Player Route
Route::get('/custom-video-player', function (Request $request) {
    $videoId = $request->get('videoId');
    $title = $request->get('title', 'Video Player');
    
    if (!$videoId) {
        abort(400, 'Video ID is required');
    }
    
    return view('custom-video-player', [
        'videoId' => $videoId,
        'title' => $title
    ]);
})->name('custom.video.player');

// ============ AUTHENTICATED ROUTES ============
Route::middleware(['auth'])->group(function () {
    
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ============ STUDENT PROFILE ROUTES ============
    Route::middleware(['role:student'])->group(function () {
        // New Profile Dropdown Routes - accessible at root level
        Route::get('/student-profile', [StudentProfileController::class, 'show'])->name('student.profile.new');
        Route::get('/my-courses', [MyCoursesController::class, 'index'])->name('my-courses.new');
        Route::get('/learning-progress', [LearningProgressController::class, 'index'])->name('learning-progress.new');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.new');
    });

    // ============ STUDENT DASHBOARD ROUTES (under /student prefix) ============
    Route::middleware(['role:student'])->prefix('student')->group(function () {
        // Student Dashboard
        Route::get('/', [StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('student.my-courses');
        Route::get('/assignments', [AssignmentController::class, 'studentAssignments'])->name('student.assignments');
        Route::get('/schedule', [ScheduleController::class, 'studentSchedule'])->name('student.schedule');
        Route::get('/grades', [StudentController::class, 'grades'])->name('student.grades');
        Route::get('/profile', [StudentController::class, 'profile'])->name('student.profile');
        Route::get('/progress', [StudentController::class, 'progress'])->name('student.progress');
        Route::get('/settings', [StudentController::class, 'settings'])->name('student.settings');
    });

    // ============ ADMIN & SUPER ADMIN ROUTES ============
    Route::middleware(['role:admin,super_admin'])->group(function () {
        // Admin Dashboard
        Route::get('/admin', [AdminController::class, 'admin'])->name('admin');
        
        // Super Admin Dashboard (only for super admins)
        Route::middleware(['role:super_admin'])->group(function () {
            Route::get('/super-admin', [AdminController::class, 'superAdmin'])->name('super.admin');
            
            // Super Admins Management
            Route::prefix('/admin/users/super-admins')->group(function () {
                Route::get('/', [UserController::class, 'superAdminsPage'])->name('super.admins');
                Route::post('/', [UserController::class, 'createSuperAdmin'])->name('super.admins.store');
                Route::put('/{id}', [UserController::class, 'updateUser'])->name('super.admins.update');
                Route::delete('/{id}', [UserController::class, 'deleteUser'])->name('super.admins.destroy');
            });

            // Admins Management
            Route::prefix('/admin/users/admins')->group(function () {
                Route::get('/', [UserController::class, 'adminsPage'])->name('admins');
                Route::post('/', [UserController::class, 'createAdmin'])->name('admins.store');
                Route::put('/{id}', [UserController::class, 'updateUser'])->name('admins.update');
                Route::delete('/{id}', [UserController::class, 'deleteUser'])->name('admins.destroy');
            });
        });

        // Teachers Management (for both admin and super_admin)
        Route::prefix('/admin/users/teachers')->group(function () {
            Route::get('/', [UserController::class, 'teachersPage'])->name('teachers');
            Route::post('/', [UserController::class, 'createTeacher'])->name('teachers.store');
            Route::put('/{id}', [UserController::class, 'updateUser'])->name('teachers.update');
            Route::delete('/{id}', [UserController::class, 'deleteUser'])->name('teachers.destroy');
        });

        // Students Management (for both admin and super_admin)
        Route::prefix('/admin/users/students')->group(function () {
            Route::get('/', [UserController::class, 'studentsPage'])->name('students');
            Route::post('/', [UserController::class, 'createStudent'])->name('students.store');
            Route::get('/create', [UserController::class, 'createUserPage'])->name('students.create');
            Route::get('/edit/{id}', [UserController::class, 'editUserPage'])->name('students.edit');
            Route::put('/{id}', [UserController::class, 'updateUser'])->name('students.update');
            Route::delete('/{id}', [UserController::class, 'deleteUser'])->name('students.destroy');
        });

        // User Creation Routes
        Route::prefix('/admin/users')->group(function () {
            Route::get('/create/{role}', [UserController::class, 'createUserPage'])->name('users.create');
        });
        
        // Teacher Portal Route
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

        // Instructor Requests Management (for both admin and super_admin)
        Route::prefix('/admin/instructor-requests')->group(function () {
            Route::get('/pending', [InstructorRequestController::class, 'pendingRequests'])->name('admin.instructor-requests.pending');
        });

        // Content Management Routes (for both admin and super_admin)
        Route::prefix('admin/content-management')->group(function () {
            Route::get('/', [ContentManagementController::class, 'index'])->name('content.management');
            Route::post('/save', [ContentManagementController::class, 'save'])->name('content.save');
            Route::post('/upload-image', [ContentManagementController::class, 'uploadImage'])->name('content.upload.image');
            Route::post('/remove-image', [ContentManagementController::class, 'removeImage'])->name('content.remove.image');
            Route::post('/bulk-save', [ContentManagementController::class, 'bulkSave'])->name('content.bulk.save');
            
            // API routes for frontend
            Route::get('/api/content/{language?}', [ContentManagementController::class, 'getContent'])->name('content.get');
            Route::get('/api/content-with-languages', [ContentManagementController::class, 'getAllContentWithLanguages'])->name('content.with.languages');
        });
    });

    // ============ TEACHER ROUTES ============
    Route::middleware(['role:teacher,admin,super_admin'])->prefix('teacher')->group(function () {
        // Teacher Portal Dashboard
        Route::get('/', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
        Route::get('/portal', [TeacherController::class, 'portal'])->name('teacher.portal');
        
        // ============ MY CLASSES SECTION ============
        Route::prefix('/classes')->group(function () {
            // All Classes List
            Route::get('/', [TeacherController::class, 'classesList'])->name('teacher.classes');
            
            // Class Schedule
            Route::get('/schedule', [ScheduleController::class, 'teacherSchedule'])->name('teacher.class-schedule');
            
            // Student Roster
            Route::get('/students', [TeacherController::class, 'studentRoster'])->name('teacher.student-roster');
        });

        // ============ RESOURCES SECTION ============
        Route::prefix('/resources')->group(function () {
            // My Resources List
            Route::get('/', [TeacherController::class, 'teacherResources'])->name('teacher.resources');
            
            // Shared Resources
            Route::get('/shared', [TeacherController::class, 'sharedResources'])->name('teacher.shared-resources');
            
            // Upload Resources
            Route::get('/upload', [TeacherController::class, 'createResource'])->name('teacher.resources.upload');
            Route::post('/upload', [TeacherController::class, 'storeResource'])->name('teacher.resources.store');
        });

        // ============ ASSESSMENTS SECTION ============
        Route::prefix('/assignments')->group(function () {
            // Grade Assignments List
            Route::get('/', [AssignmentController::class, 'teacherAssignments'])->name('teacher.assignments');
            
            // Student Progress
            Route::get('/progress', [AssignmentController::class, 'studentProgress'])->name('teacher.student-progress');
            
            // Create Assignment
            Route::get('/create', [AssignmentController::class, 'create'])->name('teacher.assignments.create');
            Route::post('/create', [AssignmentController::class, 'store'])->name('teacher.assignments.store');
        });

        // ============ INDIVIDUAL CLASS MANAGEMENT ============
        Route::prefix('/class/{classId}')->group(function () {
            // Class Dashboard - FIXED: Added teacher data
            Route::get('/', [TeacherController::class, 'classDashboard'])->name('teacher.class.dashboard');
            
            // Class-specific Assignments - FIXED: Added teacher data
            Route::get('/assignments', [TeacherController::class, 'classAssignments'])->name('teacher.class.assignments');
            Route::get('/assignments/create', [AssignmentController::class, 'createAssignmentPage'])->name('teacher.class.assignments.create');
            Route::get('/assignments/{assignmentId}/edit', [AssignmentController::class, 'editAssignmentPage'])->name('teacher.class.assignments.edit');
            
            // Class-specific Resources - FIXED: Added teacher data
            Route::get('/resources', [TeacherController::class, 'classResources'])->name('teacher.class.resources');
            
            // Class Schedule - FIXED: Added teacher data
            Route::get('/schedule', [ScheduleController::class, 'classSchedule'])->name('teacher.class.schedule');
            Route::post('/schedules', [ScheduleController::class, 'storeSchedule'])->name('teacher.class.schedules.store');
            Route::put('/schedules/{scheduleId}', [ScheduleController::class, 'updateSchedule'])->name('teacher.class.schedules.update');
            Route::delete('/schedules/{scheduleId}', [ScheduleController::class, 'destroySchedule'])->name('teacher.class.schedules.destroy');
            Route::get('/schedule/create', [ScheduleController::class, 'createSchedulePage'])->name('teacher.class.schedule.create');
            Route::get('/schedule/{scheduleId}/edit', [ScheduleController::class, 'editSchedulePage'])->name('teacher.class.schedule.edit');
            Route::get('/schedule/calendar', [ScheduleController::class, 'calendarView'])->name('teacher.class.schedule.calendar');
        });

        // Analytics & Settings
        Route::get('/analytics', [TeacherController::class, 'analytics'])->name('teacher.analytics');
        Route::get('/settings', [TeacherController::class, 'settings'])->name('teacher.settings');
        
        // Teacher Profile Edit
        Route::get('/profile/edit', [TeacherController::class, 'editProfile'])->name('teacher.profile.edit');
    });

    // ============ PROTECTED API ROUTES ============
    Route::prefix('api')->middleware('web')->group(function () {

        Route::get('/video-proxy', [VideoProxyController::class, 'proxy']);
        Route::get('/video-player', [VideoProxyController::class, 'player']);
        
        // Direct Video Stream Routes
        Route::get('/youtube-direct-stream', [VideoController::class, 'getYouTubeDirectStream']);
        Route::get('/video-proxy/{videoId}', [VideoController::class, 'proxyVideo']);

        // Content API Routes (Protected - for admin management)
        Route::prefix('content')->group(function () {
            Route::get('/all/{language?}', [ContentController::class, 'getAllContent'])->name('api.content.all');
            Route::post('/save', [ContentController::class, 'saveContent']);
            Route::post('/save-bulk', [ContentController::class, 'saveBulkContent']);
            
            // Content image upload routes
            Route::post('/upload-image', [ContentController::class, 'uploadImage'])->name('api.content.upload-image');
            Route::post('/remove-image', [ContentController::class, 'removeImage'])->name('api.content.remove-image');
        });

        // User Management API Routes
        Route::prefix('users')->group(function () {
            Route::get('/profile', [UserController::class, 'getProfile']);
            Route::put('/profile', [UserController::class, 'updateProfile']);
            Route::get('/students', [UserController::class, 'getStudents'])->name('users.students');
            Route::get('/teachers', [UserController::class, 'getTeachers'])->name('users.teachers');
            Route::get('/admins', [UserController::class, 'getAdmins'])->name('users.admins');
            Route::get('/super-admins', [UserController::class, 'getSuperAdmins'])->name('users.super-admins');
            Route::get('/other-users', [UserController::class, 'getOtherUsers']);
            Route::get('/{id}', [UserController::class, 'getUser']);
            Route::post('/bulk-actions', [UserController::class, 'bulkUserActions'])->name('users.bulk-actions');
            Route::get('/statistics', [UserController::class, 'getUserStatistics'])->name('users.statistics');
            
            // Student specific routes
            Route::post('/students/create', [UserController::class, 'createStudent'])->name('api.users.students.create');
        });

        // Course API Routes
        Route::prefix('courses')->group(function () {
            Route::get('/classes', [CourseController::class, 'getClasses']);
            Route::post('/classes', [CourseController::class, 'createClass']);
            Route::get('/class/{grade}/subjects', [CourseController::class, 'getClassSubjects']);
            Route::get('/subject/{subjectId}/teachers', [CourseController::class, 'getSubjectTeachers']);
            Route::post('/subject/{subjectId}/assign-teacher', [CourseController::class, 'assignTeacher']);
            Route::delete('/subject/{subjectId}/teacher/{teacherId}', [CourseController::class, 'removeTeacher']);
            
            Route::put('/{courseId}', [CourseController::class, 'updateCourse'])->name('api.courses.update');
            
            Route::get('/{courseId}', [CourseController::class, 'getCourse']);
            Route::get('/class/{grade}/enrollments', [CourseController::class, 'getClassEnrollments']);
            Route::get('/categories', [CourseController::class, 'getCourseCategories']);
            Route::get('/category/{category}/classes', [CourseController::class, 'getCategoryClasses']);
            Route::get('/academic-classes', [CourseController::class, 'getAcademicClasses']);
            Route::get('/other-courses', [CourseController::class, 'getOtherCourses']);
            Route::get('/all-classes', [CourseController::class, 'getAllClasses']);
            Route::delete('/{courseId}', [CourseController::class, 'deleteCourse']);
            Route::get('/{courseId}/teachers', [CourseController::class, 'getCourseTeachers']);
            Route::post('/{courseId}/assign-teacher', [CourseController::class, 'assignTeacherToCourse']);
            Route::delete('/{courseId}/teacher/{teacherId}', [CourseController::class, 'removeTeacherFromCourse']);
            Route::post('/{courseId}/enroll', [CourseController::class, 'enrollStudent']);
            Route::post('/{courseId}/unenroll', [CourseController::class, 'unenrollStudent']);
            Route::get('/user/my-courses', [CourseController::class, 'getMyCourses']);
            Route::get('/{courseId}/subjects', [CourseController::class, 'getCourseSubjects']);
            Route::post('/debug-upload', [CourseController::class, 'debugFileUpload']);
            
            // Teacher classes
            Route::get('/teacher/classes', [CourseController::class, 'getTeacherClasses'])->name('api.teacher.classes');
            
            // Debug route for course issues
            Route::get('/debug/courses', [CourseController::class, 'debugCourses']);
            
            // Search classes route
            Route::get('/search-classes', [CourseController::class, 'searchClasses'])->name('api.courses.search-classes');
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
            Route::post('/{id}/resources', [TeacherController::class, 'uploadResource'])->name('teachers.upload.resource');
            Route::put('/{id}/profile', [TeacherController::class, 'updateTeacherProfile']);
            Route::get('/{id}/public-profile', [TeacherController::class, 'getTeacherPublicProfile']);
            Route::get('/{id}/public-courses', [TeacherController::class, 'getTeacherPublicCourses']);
            Route::get('/{id}/portal-data', [TeacherController::class, 'getTeacherPortalData']);
            
            // 🔥 PROFILE PICTURE ROUTES - CORRECTLY PLACED IN AUTHENTICATED SECTION
            Route::post('/{id}/profile-picture/upload', [TeacherController::class, 'uploadProfilePicture'])->name('api.teacher.profile-picture.upload');
            Route::delete('/{id}/profile-picture', [TeacherController::class, 'deleteProfilePicture'])->name('api.teacher.profile-picture.delete');
            Route::get('/{id}/profile-picture', [TeacherController::class, 'getProfilePicture'])->name('api.teacher.profile-picture.get');
            
            // Teacher roster and schedule
            Route::get('/{id}/roster', [TeacherController::class, 'getTeacherRoster'])->name('api.teacher.roster');
            Route::get('/{id}/schedule', [ScheduleController::class, 'getTeacherSchedule'])->name('api.teacher.schedule');
        });
        
        // Class API Routes
        Route::prefix('classes')->group(function () {
            Route::get('/{classId}/resources', [TeacherController::class, 'getClassResources']);
            Route::get('/{classId}/students', [CourseController::class, 'getClassStudents']);
            Route::get('/{classId}/info', [CourseController::class, 'getClassInfo']);
            Route::get('/{classId}/schedule', [ScheduleController::class, 'getClassSchedule']);
            
            // Teacher class roster
            Route::get('/{classId}/roster', [CourseController::class, 'getClassRoster'])->name('api.class.roster');
        });
        
        // Resource API Routes
        Route::prefix('resources')->group(function () {
            // Get resources
            Route::get('/', [TeacherController::class, 'getAllResources']);
            Route::get('/class/{classId}', [TeacherController::class, 'getClassResources']);
            Route::get('/teacher/{teacherId}', [TeacherController::class, 'getTeacherResources'])->name('api.teacher.resources');
            Route::get('/shared', [TeacherController::class, 'getSharedResources'])->name('api.resources.shared');
            
            // Upload resources
            Route::post('/', [TeacherController::class, 'uploadResource']);
            Route::post('/upload/{teacherId}', [TeacherController::class, 'uploadResource']);
            
            // Manage resources
            Route::delete('/{resourceId}', [TeacherController::class, 'deleteResource']);
            Route::post('/{resourceId}/download', [TeacherController::class, 'updateDownloadCount']);
            Route::post('/{resourceId}/share', [TeacherController::class, 'shareResource'])->name('api.resources.share');
        });

        // Assignment API Routes
        Route::prefix('assignments')->group(function () {
            // Get assignments for a class
            Route::get('/class/{classId}', [AssignmentController::class, 'getClassAssignments']);
            
            // Get teacher assignments
            Route::get('/teacher', [AssignmentController::class, 'getTeacherAssignments'])->name('api.teacher.assignments');
            
            // Get student progress
            Route::get('/teacher/progress', [AssignmentController::class, 'getStudentProgress'])->name('api.teacher.progress');
            
            // Create new assignment
            Route::post('/class/{classId}', [AssignmentController::class, 'storeAssignment']);
            Route::post('/teacher/create', [AssignmentController::class, 'storeTeacherAssignment'])->name('api.teacher.assignments.store');
            
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
            
            // Get teacher schedule
            Route::get('/teacher', [ScheduleController::class, 'getTeacherSchedules'])->name('api.teacher.schedules');
            
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

        // Instructor Request API Routes
        Route::prefix('instructor-requests')->group(function () {
            Route::get('/pending', [InstructorRequestController::class, 'getPendingRequests']);
            Route::get('/stats', [InstructorRequestController::class, 'getRequestStats']);
            Route::post('/{id}/approve', [InstructorRequestController::class, 'approveRequest']);
            Route::post('/{id}/reject', [InstructorRequestController::class, 'rejectRequest']);
        });


        // Student Profile API Routes
        Route::prefix('student-profile')->group(function () {
            Route::get('/data', [StudentProfileController::class, 'getProfileData'])->name('api.student-profile.data');
            Route::put('/update', [StudentProfileController::class, 'updateProfile'])->name('api.student-profile.update');
            Route::get('/progress', [LearningProgressController::class, 'getProgressData'])->name('api.student-profile.progress');
            Route::get('/courses', [MyCoursesController::class, 'getCoursesData'])->name('api.student-profile.courses');
            Route::put('/preferences', [SettingsController::class, 'updatePreferences'])->name('api.student-profile.preferences');
        });

        // Video Proxy Protected API Routes
        Route::prefix('video-proxy')->group(function () {
            Route::get('/embed-url', [VideoProxyController::class, 'getEmbedUrl']);
            Route::post('/process-video', [VideoProxyController::class, 'processVideo']);
        });

        // File Upload Routes
        Route::prefix('upload')->group(function () {
            Route::post('/assignment-attachments', [AssignmentController::class, 'uploadAttachments']);
            Route::post('/resource-files', [TeacherController::class, 'uploadResourceFiles']);
            Route::delete('/files/{filename}', [TeacherController::class, 'deleteFile']);
            Route::post('/profile-image', [UserController::class, 'uploadProfileImage']);
            
            // Content image upload route
            Route::post('/content-image', [ContentManagementController::class, 'uploadImage'])->name('api.upload.content-image');
        });

        // Dashboard and Analytics API Routes
        Route::prefix('dashboard')->group(function () {
            Route::get('/stats', [AdminController::class, 'getDashboardStats']);
            Route::get('/teacher-stats', [TeacherController::class, 'getTeacherStats']);
            Route::get('/admin-stats', [AdminController::class, 'getAdminStats']);
            Route::get('/student-stats', [StudentController::class, 'getStudentStats']);
        });

        // 🔥 PROFILE PICTURE API ROUTES (Alternative location - also authenticated)
        Route::prefix('profile-picture')->group(function () {
            Route::post('/teacher/{id}/upload', [TeacherController::class, 'uploadProfilePicture'])->name('api.profile-picture.teacher.upload');
            Route::delete('/teacher/{id}', [TeacherController::class, 'deleteProfilePicture'])->name('api.profile-picture.teacher.delete');
            Route::get('/teacher/{id}', [TeacherController::class, 'getProfilePicture'])->name('api.profile-picture.teacher.get');
        });
    });
});

// Public file access routes
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

// Content images public access route
Route::get('/storage/content-images/{filename}', function ($filename) {
    $path = storage_path('app/public/content-images/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
})->name('storage.content-images');

// 🔥 PROFILE PICTURES PUBLIC ACCESS ROUTE
Route::get('/storage/profile-pictures/{filename}', function ($filename) {
    $path = storage_path('app/public/profile-pictures/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
})->name('storage.profile-pictures');

// Fallback route for SPA
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|storage|assets).*$');

// Debug route
Route::get('/debug-session', function() {
    session(['debug_test' => 'session_working']);
    
    return response()->json([
        'session_id' => session()->getId(),
        'session_test' => session('debug_test'),
        'csrf_token' => csrf_token(),
        'session_status' => session()->isStarted() ? 'started' : 'not_started',
        'all_cookies' => request()->cookies->all()
    ]);
});