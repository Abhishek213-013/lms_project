<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use App\Models\Student;
use App\Models\Resource;
use App\Models\Announcement; // ADD THIS IMPORT
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Content;

class FrontendController extends Controller
{
    /**
     * Home page - simple language handling
     */
    public function home(Request $request): Response
    {
        // FIXED: Handle announcements without eager loading problematic relationships
        $activeAnnouncements = Announcement::where('is_active', true)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'title_bn' => $announcement->title_bn,
                    'content' => $announcement->content,
                    'content_bn' => $announcement->content_bn,
                    'image' => $announcement->image,
                    'date' => $announcement->date,
                    'date_bn' => $announcement->date_bn,
                    'type' => $announcement->type,
                    'related_id' => $announcement->related_id,
                    'related_type' => $announcement->related_type,
                    'is_active' => $announcement->is_active,
                    'created_at' => $announcement->created_at,
                    'updated_at' => $announcement->updated_at,
                ];
            });

            

        Log::info("📢 Loaded {$activeAnnouncements->count()} active announcements");

        // SIMPLE: Get language from session, default to Bengali
        $language = session('lang', 'bn');
        
        // FORCE: Ensure session and app locale match
        session(['lang' => $language]);
        app()->setLocale($language);
        session()->save(); // Force save

        Log::info("🏠 Home page - Language: {$language}, Session: " . session('lang'));

        // Get content - this will now return Bengali content if language is 'bn'
        $content = Content::getHomeContent($language);

        // Debug log to see what content we're getting
        Log::info("📦 Content sample for {$language}:", [
            'hero_title' => $content['home_hero_title'] ?? 'Not found',
            'hero_subtitle' => $content['home_hero_subtitle'] ?? 'Not found'
        ]);

        // Rest of your method remains the same...
        $featuredCourses = ClassModel::where('status', 'active')
            ->with(['teacher:id,name', 'students'])
            ->select([
                'id', 'name', 'subject', 'type', 'category', 'description', 
                'grade', 'created_at', 'image', 'thumbnail'
            ])
            ->inRandomOrder()
            ->limit(6)
            ->get()
            ->map(function($course) use ($language) {
                return [
                    'id' => $course->id,
                    'name' => $this->getTranslatedCourseName($course, $language),
                    'subject' => $this->getTranslatedSubject($course->subject, $language),
                    'type' => $course->type,
                    'category' => $this->getTranslatedCategory($course->category, $language),
                    'description' => $this->getTranslatedDescription($course, $language),
                    'thumbnail' => $this->getCourseThumbnail($course),
                    'image' => $course->image,
                    'thumbnail_url' => $this->getCourseThumbnail($course),
                    'fee' => 0,
                    'student_count' => $course->students->count(),
                    'teacher' => $course->teacher,
                    'slug' => $this->generateSlug($course->name),
                    'grade' => $course->grade,
                    'original_name' => $course->name,
                    'original_subject' => $course->subject,
                    'original_description' => $course->description,
                ];
            });

        $instructors = User::where('role', 'teacher')
            ->select([
                'id', 'name', 'username', 'education_qualification', 
                'institute', 'experience', 'profile_picture'
            ])
            ->limit(8)
            ->get()
            ->map(function($instructor) use ($language) {
                $coursesCount = ClassModel::where('teacher_id', $instructor->id)->count();
                
                $totalStudents = DB::table('class_student')
                    ->join('classes', 'class_student.class_id', '=', 'classes.id')
                    ->where('classes.teacher_id', $instructor->id)
                    ->distinct('class_student.student_id')
                    ->count();

                return [
                    'id' => $instructor->id,
                    'name' => $instructor->name,
                    'username' => $instructor->username,
                    'education_qualification' => $this->getTranslatedQualification($instructor->education_qualification, $language),
                    'institute' => $instructor->institute,
                    'experience' => $this->getTranslatedExperience($instructor->experience, $language),
                    'profile_picture' => $instructor->profile_picture,
                    'avatar' => $this->getInstructorAvatar($instructor),
                    'courses_count' => $coursesCount,
                    'students_count' => $totalStudents,
                    'rating' => 4.8,
                    'original_qualification' => $instructor->education_qualification,
                    'original_experience' => $instructor->experience,
                ];
            });

        $stats = [
            'total_students' => Student::count() ?: 1200,
            'total_instructors' => User::where('role', 'teacher')->count() ?: 45,
            'total_courses' => ClassModel::where('status', 'active')->count() ?: 85,
            'total_enrollments' => DB::table('class_student')->count() ?: 2500,
        ];

        // Get student data for authenticated users
        $studentData = $this->getStudentData();

        return Inertia::render('Frontend/Home', [
            'content' => $content,
            'featuredCourses' => $featuredCourses,
            'instructors' => $instructors,
            'stats' => $stats,
            'testimonials' => $this->getTestimonials($language),
            'announcements' => $activeAnnouncements,
            'pageTitle' => $content['home_hero_title'] ?? 'Pathshala',
            'metaDescription' => $content['home_hero_subtitle'] ?? 'Learn with Expert Teachers',
            'auth' => [
                'user' => Auth::check() ? [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role,
                    'student' => $studentData,
                ] : null
            ],
            'currentLanguage' => $language,
            'availableLanguages' => ['en', 'bn']
        ]);
    }

    /**
     * About page
     */
    public function about(): Response
    {
        $language = $this->getCurrentLanguage();
        
        Log::info("🌐 Loading about page with language: " . $language);
        
        // Get about page content from database with language support
        $content = Content::getAboutContent($language);

        Log::info("🌐 About content loaded for language {$language}", [
            'content_keys' => array_keys($content),
            'sample_content' => [
                'banner_title' => $content['about_banner_title'] ?? 'Not found',
                'our_story_title' => $content['about_our_story_title'] ?? 'Not found'
            ]
        ]);

        // Get student data for authenticated users
        $studentData = $this->getStudentData();

        return Inertia::render('Frontend/About', [
            'content' => $content,
            'currentLanguage' => $language,
            'availableLanguages' => ['en', 'bn'],
            'pageTitle' => $content['about_banner_title'] ?? ($language === 'bn' ? 'আমাদের সম্পর্কে - পাঠশালা' : 'About Us - Pathshala'),
            'metaDescription' => $content['about_banner_description'] ?? ($language === 'bn' 
                ? 'পাঠশালা সম্পর্কে জানুন। আমাদের মিশন, ভিশন এবং শিক্ষার দর্শন আবিষ্কার করুন।'
                : 'Learn about Pathshala. Discover our mission, vision, and educational philosophy.'),
            'auth' => [
                'user' => Auth::check() ? [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role,
                    'student' => $studentData,
                ] : null
            ]
        ]);
    }

    /**
     * Courses page
     */
    public function courses(Request $request): Response
    {
        try {
            $language = $this->getCurrentLanguage();
            $user = $request->user();
            
            Log::info('📚 Loading courses page with language: ' . $language);
            Log::info('🔐 User authentication:', [
                'is_logged_in' => !!$user,
                'user_role' => $user ? $user->role : 'guest',
                'user_id' => $user ? $user->id : null
            ]);

            // Base query for courses
            $query = ClassModel::with(['teacher:id,name', 'students'])
                ->select('id', 'name', 'subject', 'grade', 'type', 'category', 'description', 'capacity', 'status', 'created_at', 'image', 'thumbnail')
                ->where('status', 'active');

            $studentInfo = [];
            
            // Handle authenticated student - show only their enrolled subjects
            if ($user && $user->role === 'student') {
                $student = Student::with(['academicClass', 'subjects'])
                    ->where('user_id', $user->id)
                    ->first();

                if ($student) {
                    Log::info('🎓 Student found:', [
                        'student_id' => $student->id,
                        'academic_class_id' => $student->academic_class_id,
                        'academic_class_name' => $student->academicClass->name ?? 'None',
                        'enrolled_subjects_count' => $student->subjects->count()
                    ]);

                    // Get enrolled subject IDs
                    $enrolledSubjectIds = $student->subjects->pluck('id')->toArray();
                    
                    if (!empty($enrolledSubjectIds)) {
                        // Only show enrolled subjects
                        $query->whereIn('id', $enrolledSubjectIds);
                        
                        // Add progress data to the query
                        $query->with(['enrolledStudents' => function($q) use ($student) {
                            $q->where('student_id', $student->id)
                            ->select('student_id', 'class_id', 'progress');
                        }]);
                    } else {
                        // Student has no enrolled subjects
                        $query->whereRaw('1 = 0'); // Force empty results
                    }

                    // Prepare student info for frontend
                    $studentInfo = [
                        'name' => $user->name,
                        'academic_class' => $student->academicClass->name ?? 'Not assigned',
                        'avatar' => $student->profile_picture_url,
                        'roll_number' => $student->roll_number,
                        'admission_date' => $student->admission_date?->format('M d, Y')
                    ];
                } else {
                    Log::warning('❌ Student record not found for user:', ['user_id' => $user->id]);
                    $studentInfo = [
                        'name' => $user->name,
                        'academic_class' => 'Not assigned',
                        'avatar' => null,
                        'roll_number' => 'N/A',
                        'admission_date' => 'N/A'
                    ];
                }
            } else {
                // Guest or non-student users - show all courses with filters
                Log::info('👤 Guest user or non-student - showing all courses');

                // Filter by category (only for guests/non-students)
                if ($request->has('category') && $request->category) {
                    $query->where('category', $request->category);
                }

                // Filter by type (only for guests/non-students)
                if ($request->has('type') && $request->type) {
                    $query->where('type', $request->type);
                }

                // Filter by grade (only for guests/non-students)
                if ($request->has('grade') && $request->grade) {
                    $query->where('grade', $request->grade);
                }

                // Search (available for all users)
                if ($request->has('search') && $request->search) {
                    $query->where(function($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('subject', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%')
                        ->orWhere('category', 'like', '%' . $request->search . '%');
                    });
                }
            }

            // Sort (available for all users)
            $sort = $request->get('sort', 'latest');
            switch ($sort) {
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                case 'grade':
                    $query->orderBy('grade', 'asc');
                    break;
                case 'popular':
                    // We'll handle this after getting student counts
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'latest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            // Get paginated results
            $perPage = $request->get('per_page', 12);
            $courses = $query->paginate($perPage)->withQueryString();

            // Transform courses for frontend with language support and progress data
            $courses->getCollection()->transform(function ($course) use ($language, $user) {
                $courseData = [
                    'id' => $course->id,
                    'name' => $course->name,
                    'subject' => $course->subject,
                    'grade' => $course->grade,
                    'type' => $course->type,
                    'category' => $course->category,
                    'description' => $course->description,
                    // Image data
                    'image' => $course->image,
                    'thumbnail' => $course->thumbnail,
                    'image_url' => $this->getCourseThumbnail($course),
                    'thumbnail_url' => $this->getCourseThumbnail($course),
                    'raw_image' => $course->image,
                    // Additional data
                    'fee' => 0,
                    'capacity' => $course->capacity,
                    'student_count' => $course->students->count(),
                    'enrollment_count' => $course->students->count(),
                    'studentCount' => $course->students->count(),
                    'teacher' => $course->teacher,
                    'created_at' => $course->created_at->format('M d, Y'),
                    'slug' => $this->generateSlug($course->name),
                    'status' => $course->status,
                    'original_name' => $course->name,
                    'original_subject' => $course->subject,
                    'original_description' => $course->description,
                ];

                // Add progress data for enrolled students
                if ($user && $user->role === 'student' && isset($course->enrolledStudents)) {
                    $enrollment = $course->enrolledStudents->first();
                    if ($enrollment) {
                        $courseData['progress'] = $enrollment->progress;
                        $courseData['last_accessed'] = $enrollment->last_accessed;
                        $courseData['is_enrolled'] = true;
                    }
                } else {
                    $courseData['progress'] = 0;
                    $courseData['is_enrolled'] = false;
                }

                return $courseData;
            });

            // Handle popular sort after transformation
            if ($sort === 'popular') {
                $sortedCollection = $courses->getCollection()->sortByDesc('student_count');
                $courses->setCollection($sortedCollection);
            }

            // Get filter options (only for guests/non-students)
            $categories = [];
            $types = [];
            $grades = [];

            if (!$user || $user->role !== 'student') {
                $categories = ClassModel::where('status', 'active')
                    ->whereNotNull('category')
                    ->distinct()
                    ->pluck('category')
                    ->filter()
                    ->values()
                    ->toArray(); // Convert to array

                $types = ClassModel::where('status', 'active')
                    ->distinct()
                    ->pluck('type')
                    ->filter()
                    ->values()
                    ->toArray(); // Convert to array

                $grades = ClassModel::where('status', 'active')
                    ->whereNotNull('grade')
                    ->distinct()
                    ->pluck('grade')
                    ->filter()
                    ->values()
                    ->sort()
                    ->toArray(); // Convert to array
            }

            // Translate categories for frontend
            $translatedCategories = [];
            foreach ($categories as $category) {
                $translatedCategories[] = $this->getTranslatedCategory($category, $language);
            }

            // Determine page title and description based on user type
            if ($user && $user->role === 'student') {
                $pageTitle = $language === 'bn' 
                    ? 'আমার কোর্সসমূহ - পাঠশালা' 
                    : 'My Courses - Pathshala';
                
                $metaDescription = $language === 'bn'
                    ? 'আপনার নিবন্ধিত বিষয়সমূহ এবং অগ্রগতি দেখুন। আপনার শিক্ষাগত যাত্রা অব্যাহত রাখুন।'
                    : 'View your enrolled subjects and track your progress. Continue your learning journey.';
            } else {
                $pageTitle = $language === 'bn' 
                    ? 'আমাদের কোর্সসমূহ - পাঠশালা' 
                    : 'Our Courses - Pathshala';
                
                $metaDescription = $language === 'bn' 
                    ? 'আমাদের ব্যাপক কোর্স এবং ক্লাস ক্যাটালগ ব্রাউজ করুন। আপনার শিক্ষাগত যাত্রার জন্য নিখুঁত লার্নিং পথ খুঁজুন।'
                    : 'Browse our comprehensive catalog of courses and classes. Find the perfect learning path for your educational journey.';
            }

            Log::info("✅ Successfully loaded {$courses->count()} courses", [
                'user_type' => $user ? $user->role : 'guest',
                'has_student_info' => !empty($studentInfo),
                'total_courses' => $courses->total(),
                'categories_count' => count($categories),
                'types_count' => count($types),
                'grades_count' => count($grades)
            ]);

            // Get student data for authenticated users
            $studentData = $this->getStudentData();

            return Inertia::render('Frontend/Courses', [
                'courses' => $courses,
                'filters' => [
                    'search' => $request->search,
                    'category' => $request->category,
                    'type' => $request->type,
                    'grade' => $request->grade,
                    'sort' => $sort,
                    'per_page' => $perPage,
                ],
                'categories' => $translatedCategories, // Use the translated array
                'types' => $types,
                'grades' => $grades,
                'studentInfo' => $studentInfo,
                'pageTitle' => $pageTitle,
                'metaDescription' => $metaDescription,
                'currentLanguage' => $language,
                'availableLanguages' => ['en', 'bn'],
                'auth' => [
                    'user' => Auth::check() ? [
                        'id' => Auth::user()->id,
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                        'role' => Auth::user()->role,
                        'student' => $studentData,
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Courses page error: ' . $e->getMessage());
            Log::error('📝 Stack trace: ' . $e->getTraceAsString());
            
            return $this->renderCoursesWithFallback($request);
        }
    }

    /**
     * Single course page
     */
    public function courseSingle($id): Response
    {
        try {
            $course = ClassModel::where('status', 'active')
                ->with(['teacher:id,name,email,experience,education_qualification,institute', 'students'])
                ->find($id);

            if (!$course) {
                return $this->renderNotFound('Course not found');
            }

            $language = $this->getCurrentLanguage();

            $courseData = [
                'id' => $course->id,
                'name' => $this->getTranslatedCourseName($course, $language),
                'subject' => $this->getTranslatedSubject($course->subject, $language),
                'grade' => $course->grade,
                'type' => $course->type,
                'category' => $this->getTranslatedCategory($course->category, $language),
                'description' => $this->getTranslatedDescription($course, $language),
                'full_description' => $this->getTranslatedDescription($course, $language),
                'thumbnail' => $this->getCourseThumbnail($course),
                'fee' => 0,
                'capacity' => $course->capacity,
                'duration' => '12 weeks',
                'level' => 'Beginner',
                'student_count' => $course->students->count(),
                'teacher' => $course->teacher,
                'schedule' => $course->schedule,
                'requirements' => 'No specific requirements',
                'learning_outcomes' => 'Comprehensive understanding of the subject matter',
                'slug' => $this->generateSlug($course->name),
                'created_at' => $course->created_at->format('M d, Y'),
                'updated_at' => $course->updated_at->format('M d, Y')
            ];

            // Check if user is enrolled (if authenticated)
            $isEnrolled = false;
            if (Auth::check()) {
                $isEnrolled = $course->students()->where('student_id', Auth::id())->exists();
            }

            // Get related courses
            $relatedCourses = ClassModel::where('status', 'active')
                ->where(function($query) use ($course) {
                    $query->where('category', $course->category)
                          ->orWhere('type', $course->type)
                          ->orWhere('teacher_id', $course->teacher_id);
                })
                ->where('id', '!=', $course->id)
                ->with(['teacher:id,name', 'students'])
                ->limit(4)
                ->get()
                ->map(function($relatedCourse) use ($language) {
                    return [
                        'id' => $relatedCourse->id,
                        'name' => $this->getTranslatedCourseName($relatedCourse, $language),
                        'subject' => $this->getTranslatedSubject($relatedCourse->subject, $language),
                        'description' => $this->getTranslatedDescription($relatedCourse, $language),
                        'thumbnail' => $this->getCourseThumbnail($relatedCourse),
                        'fee' => 0,
                        'student_count' => $relatedCourse->students->count(),
                        'teacher' => $relatedCourse->teacher,
                        'slug' => $this->generateSlug($relatedCourse->name),
                        'type' => $relatedCourse->type
                    ];
                });

            // Get student data for authenticated users
            $studentData = $this->getStudentData();

            return Inertia::render('Frontend/CourseSingle', [
                'course' => $courseData,
                'relatedCourses' => $relatedCourses,
                'isEnrolled' => $isEnrolled,
                'pageTitle' => $courseData['name'] . ' - Pathshala',
                'metaDescription' => $courseData['description'],
                'currentLanguage' => $language,
                'availableLanguages' => ['en', 'bn'],
                'auth' => [
                    'user' => Auth::check() ? [
                        'id' => Auth::user()->id,
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                        'role' => Auth::user()->role,
                        'student' => $studentData,
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Course single page error: ' . $e->getMessage());
            
            return $this->renderNotFound('Course not found');
        }
    }

    /**
     * Instructors page
     */
    public function instructors(): Response
    {
        $language = $this->getCurrentLanguage();

        // Get all teachers with their classes and student counts
        $teachers = User::where('role', 'teacher')
            ->where('status', 'active')
            ->select([
                'id', 
                'name', 
                'username', 
                'email', 
                'dob',
                'education_qualification', 
                'institute', 
                'experience', 
                'bio',
                'profile_picture',
                'order_column',
                'created_at'
            ])
            ->withCount(['classes as courses_count'])
            ->with(['classes' => function($query) {
                $query->select('id', 'teacher_id')
                    ->withCount('students');
            }])
            ->orderBy('order_column')
            ->get()
            ->map(function ($teacher) use ($language) {
                // Calculate total students across all classes
                $totalStudents = $teacher->classes->sum('students_count');
                
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'education_qualification' => $this->getTranslatedQualification($teacher->education_qualification, $language),
                    'institute' => $teacher->institute,
                    'experience' => $this->getTranslatedExperience($teacher->experience, $language),
                    'profile_picture' => $teacher->profile_picture,
                    'order_column' => $teacher->order_column,
                    'courses_count' => $teacher->courses_count,
                    'students_count' => $totalStudents,
                    'rating' => $this->getTeacherRating($teacher->id),
                    'created_at' => $teacher->created_at,
                ];
            });

        $specializations = User::where('role', 'teacher')
            ->whereNotNull('education_qualification')
            ->where('education_qualification', '!=', '')
            ->distinct()
            ->pluck('education_qualification')
            ->toArray();

        // Get student data for authenticated users
        $studentData = $this->getStudentData();

        return Inertia::render('Frontend/Instructors', [
            'instructors' => $teachers,
            'specializations' => $specializations,
            'filters' => request()->only(['search', 'specialization']),
            'currentLanguage' => $language,
            'availableLanguages' => ['en', 'bn'],
            'pageTitle' => $language === 'bn' ? 'ইন্সট্রাক্টর - পাঠশালা' : 'Instructors - Pathshala',
            'metaDescription' => $language === 'bn' 
                ? 'আমাদের বিশেষজ্ঞ ইন্সট্রাক্টরদের সাথে পরিচিত হোন' 
                : 'Meet our expert instructors',
            'auth' => [
                'user' => Auth::check() ? [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role,
                    'student' => $studentData,
                ] : null
            ]
        ]);
    }

    /**
     * Instructor details page
     */
    public function instructorDetails($id): Response
    {
        try {
            $language = $this->getCurrentLanguage();
            Log::info("🎯 Loading instructor details for ID: {$id} with language: {$language}");

            // Get instructor with profile_picture
            $instructor = User::where('role', 'teacher')
                ->where('id', $id)
                ->select([
                    'id', 'name', 'username', 'email',
                    'education_qualification', 'institute', 'experience',
                    'profile_picture',
                    'created_at'
                ])
                ->first();

            if (!$instructor) {
                return $this->renderNotFound('Instructor not found');
            }

            Log::info("✅ Found instructor: {$instructor->name}");
            Log::info("🖼️ Instructor profile picture: {$instructor->profile_picture}");

            // Get instructor's classes
            $classes = ClassModel::where('teacher_id', $id)
                ->where('status', 'active')
                ->with(['students'])
                ->select(['id', 'name', 'description', 'category', 'created_at', 'type', 'grade', 'subject', 'code', 'capacity'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($course) use ($language) {
                    return [
                        'id' => $course->id,
                        'name' => $this->getTranslatedCourseName($course, $language),
                        'description' => $this->getTranslatedDescription($course, $language),
                        'category' => $this->getTranslatedCategory($course->category, $language),
                        'thumbnail' => $this->getCourseThumbnail($course),
                        'student_count' => $course->students->count(),
                        'created_at' => $course->created_at->format('M d, Y'),
                        'type' => $course->type,
                        'grade' => $course->grade,
                        'subject' => $this->getTranslatedSubject($course->subject, $language),
                        'code' => $course->code,
                        'capacity' => $course->capacity,
                        'status' => 'active'
                    ];
                });

            // Get instructor's demo videos from resources table
            Log::info("📹 Fetching videos for teacher_id: {$id}");
            
            $videos = Resource::where('teacher_id', $id)
                ->where('type', 'video')
                ->where('status', 'active')
                ->with(['teacher:id,name', 'class:id,name'])
                ->select([
                    'id', 
                    'teacher_id', 
                    'class_id',
                    'title', 
                    'description', 
                    'content', 
                    'thumbnail_path', 
                    'file_path', 
                    'created_at'
                ])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($video) {
                    Log::info("🎥 Processing video: {$video->title} (ID: {$video->id})");
                    
                    // Create a Resource model instance to access the accessors
                    $resource = new Resource();
                    $resource->forceFill([
                        'id' => $video->id,
                        'title' => $video->title,
                        'description' => $video->description,
                        'content' => $video->content,
                        'thumbnail_path' => $video->thumbnail_path,
                        'file_path' => $video->file_path,
                        'created_at' => $video->created_at,
                    ]);

                    // Determine video type and category
                    $isYoutube = $this->isYouTubeVideo($video->content);
                    $youtubeVideoId = $isYoutube ? $this->extractYouTubeId($video->content) : null;
                    $category = $this->determineVideoCategory($video->title, $video->description);

                    return [
                        'id' => $video->id,
                        'title' => $video->title,
                        'description' => $video->description ?? 'Demo class video showcasing teaching methodology',
                        'thumbnail' => $this->getVideoThumbnail($resource),
                        'videoUrl' => $video->content,
                        'duration' => $this->generateVideoDuration($video->id),
                        'class_id' => $video->class_id,
                        'class_name' => $video->class ? $video->class->name : 'General Education',
                        'created_at' => $video->created_at->format('Y-m-d\TH:i:s\Z'),
                        'views' => $this->generateVideoViews($video->id),
                        'likes' => $this->generateVideoLikes($video->id),
                        'category' => $category,
                        'is_youtube' => $isYoutube,
                        'youtube_video_id' => $youtubeVideoId,
                        'youtube_embed_url' => $isYoutube ? "https://www.youtube.com/embed/{$youtubeVideoId}" : null,
                        'file_url' => $video->file_path ? asset('storage/' . $video->file_path) : null,
                        'access_level' => 'demo'
                    ];
                });

            Log::info("✅ Found {$videos->count()} videos for instructor {$instructor->name}");

            // Calculate stats
            $coursesCount = $classes->count();
            $totalStudents = DB::table('class_student')
                ->join('classes', 'class_student.class_id', '=', 'classes.id')
                ->where('classes.teacher_id', $instructor->id)
                ->distinct('class_student.student_id')
                ->count();

            $totalVideos = Resource::where('teacher_id', $id)
                ->where('type', 'video')
                ->where('status', 'active')
                ->count();

            // Format instructor data
            $instructorData = [
                'id' => $instructor->id,
                'name' => $instructor->name,
                'username' => $instructor->username,
                'email' => $instructor->email,
                'education_qualification' => $this->getTranslatedQualification($instructor->education_qualification, $language),
                'institute' => $instructor->institute,
                'experience' => $this->getTranslatedExperience($instructor->experience, $language),
                'profile_picture' => $instructor->profile_picture,
                'bio' => $instructor->bio, // ✅ Use actual bio from database
                'teaching_philosophy' => $this->generateTeachingPhilosophy($instructor),
                'expertise' => $this->getExpertiseFromClasses($classes),
                'languages' => 'English, Spanish',
                'response_time' => 'Within 24 hours',
                'rating' => '4.8',
                'reviews' => '128',
                'total_classes' => $coursesCount,
                'total_students' => $totalStudents,
                'courses_count' => $coursesCount,
                'students_count' => $totalStudents,
                'created_at' => $instructor->created_at->format('M d, Y'),    
            ];    

            Log::info("📤 Sending instructor data to frontend:", [
                'has_profile_picture' => !empty($instructorData['profile_picture']),
                'profile_picture_path' => $instructorData['profile_picture']
            ]);

            // Get student data for authenticated users
            $studentData = $this->getStudentData();

            return Inertia::render('Frontend/InstructorDetails', [
                'instructor' => $instructorData,
                'classes' => $classes->toArray(),
                'videos' => $videos->toArray(),
                'stats' => [
                    'totalClasses' => $coursesCount,
                    'totalStudents' => $totalStudents,
                    'totalVideos' => $totalVideos,
                    'rating' => 4.8,
                    'experience_years' => $this->extractExperienceYears($instructor->experience)
                ],
                'pageTitle' => $instructor->name . ' - Instructor - Pathshala',
                'metaDescription' => $this->generateBio($instructor),
                'currentLanguage' => $language,
                'availableLanguages' => ['en', 'bn'],
                'auth' => [
                    'user' => Auth::check() ? [
                        'id' => Auth::user()->id,
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                        'role' => Auth::user()->role,
                        'student' => $studentData,
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Instructor details page error: ' . $e->getMessage());
            return $this->renderNotFound('Error loading instructor details: ' . $e->getMessage());
        }
    }

    /**
     * Contact page
     */
    public function contact(): Response
    {
        $language = $this->getCurrentLanguage();

        $contactInfo = [
            'address' => '123 Education Street, Learning City, 12345',
            'phone' => '+1 (555) 123-4567',
            'email' => 'info@pathshala.com',
            'working_hours' => 'Monday - Friday: 9:00 AM - 6:00 PM'
        ];

        // Get student data for authenticated users
        $studentData = $this->getStudentData();

        return Inertia::render('Frontend/Contact', [
            'contactInfo' => $contactInfo,
            'pageTitle' => $language === 'bn' ? 'যোগাযোগ - পাঠশালা' : 'Contact Us - Pathshala',
            'metaDescription' => $language === 'bn' 
                ? 'পাঠশালার সাথে যোগাযোগ করুন। আমরা আপনার প্রশ্নের উত্তর দিতে এবং আপনার শেখার যাত্রা শুরু করতে এখানে আছি।'
                : 'Get in touch with Pathshala. We\'re here to answer your questions and help you start your learning journey.',
            'currentLanguage' => $language,
            'availableLanguages' => ['en', 'bn'],
            'auth' => [
                'user' => Auth::check() ? [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role,
                    'student' => $studentData,
                ] : null
            ]
        ]);
    }

    /**
     * Simple language switch API
     */
    public function switchLanguageApi(Request $request)
    {
        $request->validate([
            'language' => 'required|in:en,bn'
        ]);
        
        $language = $request->language;
        
        // Update session
        session(['lang' => $language]);
        app()->setLocale($language);
        session()->save();
        
        Log::info("🔄 Language switched to: {$language}");

        // Return updated content for the new language
        $content = Content::getHomeContent($language);
        
        return response()->json([
            'success' => true,
            'language' => $language,
            'message' => "Language switched to " . ($language === 'en' ? 'English' : 'Bengali'),
            'content' => $content, // Make sure this returns the correct language content
            'session_verified' => session('lang') === $language
        ]);
    }

    /**
     * Switch language for about page - returns about content
     */
    public function switchLanguageAbout(Request $request)
    {
        $request->validate([
            'language' => 'required|in:en,bn'
        ]);
        
        $language = $request->language;
        
        // Update session
        session(['lang' => $language]);
        app()->setLocale($language);
        session()->save();
        
        Log::info("🔄 About page: Language switched to: {$language}");

        // Return updated ABOUT content for the new language
        $content = Content::getAboutContent($language);
        
        return response()->json([
            'success' => true,
            'language' => $language,
            'message' => "Language switched to " . ($language === 'en' ? 'English' : 'Bengali'),
            'content' => $content, // Returns about content in correct language
            'session_verified' => session('lang') === $language
        ]);
    }

    /**
     * Reorder instructors
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:users,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->order as $position => $instructorId) {
                    User::where('id', $instructorId)
                        ->where('role', 'teacher')
                        ->update(['order_column' => $position + 1]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Instructor order updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get student data for authenticated users
     */
    private function getStudentData()
    {
        if (Auth::check() && Auth::user()->role === 'student') {
            $student = Student::where('user_id', Auth::id())->first();
            
            Log::info('🔍 Student data debug:', [
                'user_id' => Auth::id(),
                'student_found' => !!$student,
                'student_id' => $student?->id,
                'profile_picture' => $student?->profile_picture,
                'profile_picture_url' => $student?->profile_picture_url,
            ]);
            
            if ($student) {
                return [
                    'id' => $student->id,
                    'user_id' => $student->user_id,
                    'profile_picture' => $student->profile_picture,
                    'profile_picture_url' => $student->profile_picture_url,
                    'roll_number' => $student->roll_number,
                    'academic_class_id' => $student->academic_class_id,
                    'father_name' => $student->father_name,
                    'mother_name' => $student->mother_name,
                    'parent_contact' => $student->parent_contact,
                    'country_code' => $student->country_code,
                    'full_parent_contact' => $student->full_parent_contact,
                    'address' => $student->address,
                    'admission_date' => $student->admission_date?->format('M d, Y'),
                    'phone' => $student->phone,
                    'bio' => $student->bio,
                    'location' => $student->location,
                    'status' => $student->status,
                ];
            }
        }
        return null;
    }

    /**
     * Get testimonials based on language
     */
    private function getTestimonials($language)
    {
        return [
            [
                'id' => 1,
                'name' => $language === 'bn' ? 'সারা জনসন' : 'Sarah Johnson',
                'role' => $language === 'bn' ? 'শিক্ষার্থী' : 'Student',
                'content' => $language === 'bn' 
                    ? 'পাঠশালা আমার শেখার অভিজ্ঞতা পরিবর্তন করেছে। কোর্সগুলো ভালোভাবে সাজানো এবং ইন্সট্রাক্টররা অসাধারণ!' 
                    : 'Pathshala transformed my learning experience. The courses are well-structured and the instructors are amazing!',
                'avatar' => '/assets/img/testimonials/1.jpg',
                'rating' => 5
            ],
            [
                'id' => 2,
                'name' => $language === 'bn' ? 'মাইক চেন' : 'Mike Chen',
                'role' => $language === 'bn' ? 'পেশাদার' : 'Professional',
                'content' => $language === 'bn'
                    ? 'অনলাইন লার্নিংয়ের নমনীয়তা বিশেষজ্ঞ নির্দেশনার সাথে মিলে আমার ক্যারিয়ার এগিয়ে নিতে সাহায্য করেছে।'
                    : 'The flexibility of online learning combined with expert instruction helped me advance my career.',
                'avatar' => '/assets/img/testimonials/2.jpg',
                'rating' => 5
            ],
            [
                'id' => 3,
                'name' => $language === 'bn' ? 'এমিলি ডেভিস' : 'Emily Davis',
                'role' => $language === 'bn' ? 'অভিভাবক' : 'Parent',
                'content' => $language === 'bn'
                    ? 'আমার সন্তানরা ইন্টারেক্টিভ ক্লাসগুলো পছন্দ করে। শিক্ষকরা ধৈর্যশীল এবং জ্ঞানী।'
                    : 'My children love the interactive classes. The teachers are patient and knowledgeable.',
                'avatar' => '/assets/img/testimonials/3.jpg',
                'rating' => 4
            ]
        ];
    }

    /**
     * Get translated course name based on language
     */
    private function getTranslatedCourseName($course, $language)
    {
        if ($language === 'bn') {
            $translations = [
                'Mathematics' => 'গণিত',
                'Science' => 'বিজ্ঞান',
                'English' => 'ইংরেজি',
                'Bangla' => 'বাংলা',
                'Physics' => 'পদার্থবিজ্ঞান',
                'Chemistry' => 'রসায়ন',
                'Biology' => 'জীববিজ্ঞান',
                'Computer Science' => 'কম্পিউটার বিজ্ঞান',
                'History' => 'ইতিহাস',
                'Geography' => 'ভূগোল',
                'Class' => 'ক্লাস',
                'Grade' => 'গ্রেড',
            ];
            
            $name = $course->name;
            foreach ($translations as $en => $bn) {
                $name = str_ireplace($en, $bn, $name);
            }
            
            return $name;
        }
        
        return $course->name;
    }

    /**
     * Get translated subject based on language
     */
    private function getTranslatedSubject($subject, $language)
    {
        if ($language === 'bn') {
            $translations = [
                'Mathematics' => 'গণিত',
                'Science' => 'বিজ্ঞান',
                'English' => 'ইংরেজি',
                'Bangla' => 'বাংলা',
                'Physics' => 'পদার্থবিজ্ঞান',
                'Chemistry' => 'রসায়ন',
                'Biology' => 'জীববিজ্ঞান',
                'Computer' => 'কম্পিউটার',
                'History' => 'ইতিহাস',
                'Geography' => 'ভূগোল',
                'General' => 'সাধারণ',
                'Social Science' => 'সামাজিক বিজ্ঞান',
                'Religion' => 'ধর্ম',
                'Arts' => 'শিল্প',
                'Music' => 'সঙ্গীত',
            ];
            
            return $translations[$subject] ?? $subject;
        }
        
        return $subject;
    }

    /**
     * Get translated category based on language
     */
    private function getTranslatedCategory($category, $language)
    {
        if ($language === 'bn') {
            $translations = [
                'Primary' => 'প্রাথমিক',
                'Junior' => 'জুনিয়র',
                'Secondary' => 'সেকেন্ডারি',
                'Higher Secondary' => 'উচ্চ মাধ্যমিক',
                'Skill Course' => 'স্কিল কোর্স',
                'Language' => 'ভাষা',
                'Technology' => 'প্রযুক্তি',
                'Science' => 'বিজ্ঞান',
                'Mathematics' => 'গণিত',
                'Arts' => 'শিল্প',
                'Business' => 'ব্যবসা',
                'Professional' => 'পেশাদার',
            ];
            
            return $translations[$category] ?? $category;
        }
        
        return $category;
    }

    /**
     * Get translated description based on language
     */
    private function getTranslatedDescription($course, $language)
    {
        if ($language === 'bn' && $course->description) {
            $translations = [
                'comprehensive' => 'ব্যাপক',
                'curriculum' => 'কারিকুলাম',
                'students' => 'শিক্ষার্থী',
                'essential' => 'অপরিহার্য',
                'subjects' => 'বিষয়',
                'academic success' => 'একাডেমিক সাফল্য',
                'expert instructors' => 'বিশেষজ্ঞ ইন্সট্রাক্টর',
                'skills' => 'দক্ষতা',
                'knowledge' => 'জ্ঞান',
                'learn' => 'শিখুন',
                'explore' => 'অন্বেষণ করুন',
                'course' => 'কোর্স',
                'class' => 'ক্লাস',
                'teaching' => 'শিক্ষাদান',
                'learning' => 'শেখা',
                'education' => 'শিক্ষা',
                'study' => 'অধ্যায়ন',
                'practice' => 'অনুশীলন',
                'understand' => 'বুঝুন',
                'develop' => 'বিকাশ করুন',
                'improve' => 'উন্নত করুন',
                'master' => 'আয়ত্ত করুন',
            ];
            
            $description = $course->description;
            foreach ($translations as $en => $bn) {
                $description = str_ireplace($en, $bn, $description);
            }
            
            return $description;
        }
        
        return $course->description;
    }

    /**
     * Get translated qualification based on language
     */
    private function getTranslatedQualification($qualification, $language)
    {
        if ($language === 'bn') {
            $translations = [
                'Higher Secondary Certificate (HSC)' => 'উচ্চ মাধ্যমিক সার্টিফিকেট (এইচএসসি)',
                'Bachelor of Science (BSC)' => 'বিজ্ঞানে স্নাতক (বিএসসি)',
                'Bachelor of Arts (BA)' => 'কলায় স্নাতক (বিএ)',
                'Master of Arts (MA)' => 'কলায় স্নাতকোত্তর (এমএ)',
                'Master of Science (MSC)' => 'বিজ্ঞানে স্নাতকোত্তর (এমএসসি)',
                'Doctor of Philosophy (PhD)' => 'পিএইচডি',
                'Other' => 'অন্যান্য',
                'Not specified' => 'নির্দিষ্ট করা হয়নি',
            ];
            
            return $translations[$qualification] ?? $qualification;
        }
        
        return $qualification;
    }

    /**
     * Get translated experience based on language
     */
    private function getTranslatedExperience($experience, $language)
    {
        if ($language === 'bn' && $experience) {
            $translations = [
                'years' => 'বছর',
                'year' => 'বছর',
                'teaching' => 'শিক্ষণ',
                'experience' => 'অভিজ্ঞতা',
                'in' => 'এ',
                'Mathematics' => 'গণিত',
                'Science' => 'বিজ্ঞান',
                'English' => 'ইংরেজি',
                'Bangla' => 'বাংলা',
                'Physics' => 'পদার্থবিজ্ঞান',
                'Chemistry' => 'রসায়ন',
                'Biology' => 'জীববিজ্ঞান',
                'Computer' => 'কম্পিউটার',
                'over' => 'এর বেশি',
                'more than' => 'এর চেয়ে বেশি',
                'plus' => 'প্লাস',
            ];
            
            $translated = $experience;
            foreach ($translations as $en => $bn) {
                $translated = str_ireplace($en, $bn, $translated);
            }
            
            return $translated;
        }
        
        return $experience;
    }

    /**
     * Get current language from session only - NO URL PARAMETERS
     */
    private function getCurrentLanguage()
    {
        $language = session('lang', 'bn');
        app()->setLocale($language);
        return $language;
    }

    /**
     * Render courses with fallback data
     */
    private function renderCoursesWithFallback(Request $request): Response
    {
        $language = $this->getCurrentLanguage();
        Log::info('🔄 Using fallback courses data with language: ' . $language);
        
        $fallbackCourses = [
            [
                'id' => 1,
                'name' => $language === 'bn' ? 'গণিত গ্রেড ১' : 'Mathematics Grade 1',
                'subject' => $language === 'bn' ? 'গণিত' : 'Mathematics',
                'grade' => 1,
                'type' => 'regular',
                'category' => $language === 'bn' ? 'প্রাথমিক' : 'Primary',
                'description' => $language === 'bn' 
                    ? 'প্রথম গ্রেডের শিক্ষার্থীদের জন্য মৌলিক গণিত সংখ্যা, গণনা এবং সাধারণ গাণিতিক বিষয় covering।'
                    : 'Basic mathematics for first grade students covering numbers, counting, and simple arithmetic.',
                'thumbnail' => '/assets/img/courses/course_thumb01.png',
                'fee' => 0,
                'capacity' => 30,
                'student_count' => 25,
                'teacher' => null,
                'created_at' => 'Jan 01, 2024',
                'slug' => 'mathematics-grade-1',
                'status' => 'active'
            ]
        ];

        // Get student data for authenticated users
        $studentData = $this->getStudentData();

        return Inertia::render('Frontend/Courses', [
            'courses' => $fallbackCourses,
            'filters' => $request->only(['search', 'category', 'type', 'grade', 'sort']),
            'categories' => $language === 'bn' 
                ? ['প্রাথমিক', 'জুনিয়র', 'সেকেন্ডারি', 'ভাষা', 'প্রযুক্তি'] 
                : ['Primary', 'Junior', 'Secondary', 'Language', 'Technology'],
            'types' => ['regular', 'other'],
            'grades' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            'pageTitle' => $language === 'bn' ? 'আমাদের কোর্সসমূহ - পাঠশালা' : 'Our Courses - Pathshala',
            'metaDescription' => $language === 'bn'
                ? 'আমাদের ব্যাপক কোর্স এবং ক্লাস ক্যাটালগ ব্রাউজ করুন।'
                : 'Browse our comprehensive catalog of courses and classes.',
            'currentLanguage' => $language,
            'availableLanguages' => ['en', 'bn'],
            'auth' => [
                'user' => Auth::check() ? [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role,
                    'student' => $studentData,
                ] : null
            ]
        ]);
    }

    /**
     * Get teacher students count
     */
    private function getTeacherStudentsCount($teacherId)
    {
        return Student::whereHas('class', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->count();
    }

    /**
     * Get teacher rating
     */
    private function getTeacherRating($teacherId)
    {
        return '4.8';
    }

    /**
     * Get instructor avatar
     */
    private function getInstructorAvatar($instructor)
    {
        $defaultAvatars = [
            '/assets/img/instructor/instructor01.png',
            '/assets/img/instructor/instructor02.png',
            '/assets/img/instructor/instructor03.png',
            '/assets/img/instructor/instructor04.png',
        ];

        $nameHash = crc32($instructor->name ?? 'default');
        $index = $nameHash % count($defaultAvatars);

        return $defaultAvatars[$index];
    }

    /**
     * Get course thumbnail
     */
    private function getCourseThumbnail($course)
    {
        // First priority - Use database images with proper URL formatting
        if ($course->thumbnail && $course->thumbnail !== 'null' && $course->thumbnail !== 'NULL') {
            $thumbnailUrl = $this->formatImageUrl($course->thumbnail);
            Log::info("✅ Using database thumbnail for course {$course->id}: {$thumbnailUrl}");
            return $thumbnailUrl;
        }

        if ($course->image && $course->image !== 'null' && $course->image !== 'NULL') {
            $imageUrl = $this->formatImageUrl($course->image);
            Log::info("✅ Using database image for course {$course->id}: {$imageUrl}");
            return $imageUrl;
        }

        // Fallback to demo thumbnails only if no database image exists
        Log::info("📸 No database image found for course {$course->id}, using fallback");
        
        $courseType = $course->type ?? 'regular';
        
        if ($courseType === 'regular') {
            $grade = $course->grade ?? 1;
            $thumbnails = [
                '/assets/img/courses/h5_course_thumb1.jpg',
                '/assets/img/courses/h5_course_thumb02.jpg',
                '/assets/img/courses/h5_course_thumb03.jpg',
                '/assets/img/courses/h5_course_thumb04.jpg'
            ];
            return $thumbnails[($grade - 1) % count($thumbnails)];
        } else {
            $thumbnails = [
                'Language' => '/assets/img/courses/h5_course_thumb05.jpg',
                'Technology' => '/assets/img/courses/h5_course_thumb06.jpg',
                'Personal Development' => '/assets/img/courses/h5_course_thumb07.jpg',
                'default' => '/assets/img/courses/h5_course_thumb08.jpg'
            ];
            return $thumbnails[$course->category] ?? $thumbnails['default'];
        }
    }

    /**
     * Format image URL
     */
    private function formatImageUrl($imagePath)
    {
        if (!$imagePath) return null;
        
        Log::info("🔄 Formatting image path: {$imagePath}");
        
        // If it's already a full URL, return as is
        if (str_starts_with($imagePath, 'http')) {
            return $imagePath;
        }
        
        // If it starts with storage/, remove the storage/ prefix for public access
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

    // Helper methods for videos
    private function getVideoThumbnail($resource)
    {
        // Use the thumbnail_path if available
        if ($resource->thumbnail_path) {
            // Check if it's a YouTube thumbnail reference
            if (str_starts_with($resource->thumbnail_path, 'youtube_')) {
                $videoId = str_replace('youtube_', '', $resource->thumbnail_path);
                return "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
            }
            
            // Check if it's a stored file
            if (file_exists(storage_path('app/public/' . $resource->thumbnail_path))) {
                return asset('storage/' . $resource->thumbnail_path);
            }
        }
        
        // For YouTube videos without thumbnail_path, generate from content
        if ($this->isYouTubeVideo($resource->content)) {
            $videoId = $this->extractYouTubeId($resource->content);
            if ($videoId) {
                return "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
            }
        }
        
        // Default thumbnail
        return '/assets/img/courses/video_thumb01.jpg';
    }

    private function isYouTubeVideo($content)
    {
        if (!$content) return false;
        
        $patterns = [
            '/youtube\.com\/watch\?v=/',
            '/youtu\.be\//',
            '/youtube\.com\/embed\//',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }

    private function extractYouTubeId($url)
    {
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }

    private function determineVideoCategory($title, $description)
    {
        $text = strtolower($title . ' ' . ($description ?? ''));
        
        if (str_contains($text, 'math') || str_contains($text, 'calculus') || str_contains($text, 'algebra')) {
            return 'mathematics';
        } elseif (str_contains($text, 'physics')) {
            return 'physics';
        } elseif (str_contains($text, 'programming') || str_contains($text, 'python') || str_contains($text, 'javascript') || str_contains($text, 'coding')) {
            return 'programming';
        } elseif (str_contains($text, 'chemistry') || str_contains($text, 'biology') || str_contains($text, 'science')) {
            return 'science';
        } else {
            return 'all';
        }
    }

    private function generateVideoDuration($videoId)
    {
        $durations = ['15:30', '22:45', '35:20', '28:15', '45:30', '52:10', '1:05:15'];
        return $durations[$videoId % count($durations)];
    }

    private function generateVideoViews($videoId)
    {
        $views = ['250', '480', '750', '890', '1.2K', '1.5K', '2.1K', '3.2K'];
        return $views[$videoId % count($views)];
    }

    private function generateVideoLikes($videoId)
    {
        $likes = ['45', '78', '98', '156', '210', '245', '312', '450'];
        return $likes[$videoId % count($likes)];
    }

    private function getExpertiseFromClasses($classes)
    {
        if ($classes->count() > 0) {
            $subjects = array_unique(array_column($classes->toArray(), 'subject'));
            return implode(', ', $subjects);
        }
        
        return 'Mathematics, Physics, Computer Science';
    }

    private function generateTeachingPhilosophy($instructor)
    {
        return "{$instructor->name} believes in creating an engaging learning environment where students feel empowered to explore and discover knowledge. With a focus on practical applications and real-world examples, {$instructor->name} ensures that every student can grasp complex concepts and develop a lifelong love for learning.";
    }

    private function extractExperienceYears($experience)
    {
        if (!$experience) return 5;
        
        preg_match('/(\d+)/', $experience, $matches);
        return $matches ? (int)$matches[1] : 5;
    }

    private function generateBio($instructor)
    {
        $qualification = $instructor->education_qualification ?? 'qualified';
        $institute = $instructor->institute ?? 'reputable institution';
        $experience = $instructor->experience ?? 'experienced';
        
        return "{$instructor->name} is a {$qualification} educator from {$institute} with {$experience}. Dedicated to providing quality education and helping students achieve their academic goals.";
    }

    private function generateSlug($name)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    private function renderNotFound(string $message = ''): Response
    {
        $language = $this->getCurrentLanguage();
        
        // Get student data for authenticated users
        $studentData = $this->getStudentData();

        return Inertia::render('Frontend/Errors/404', [
            'message' => $message,
            'pageTitle' => $language === 'bn' ? 'পৃষ্ঠা পাওয়া যায়নি - পাঠশালা' : 'Page Not Found - Pathshala',
            'currentLanguage' => $language,
            'availableLanguages' => ['en', 'bn'],
            'auth' => [
                'user' => Auth::check() ? [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role,
                    'student' => $studentData,
                ] : null
            ]
        ]);
    }
}