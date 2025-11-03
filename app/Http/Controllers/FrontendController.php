<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use App\Models\Student;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Content;

class FrontendController extends Controller
{
    // Home page
    public function home(): Response
    {
        try {
            // Get home page content from database
            $content = Content::getHomeContent();
            
            $featuredCourses = ClassModel::where('status', 'active')
                ->with(['teacher:id,name', 'students'])
                ->select(['id', 'name', 'subject', 'type', 'category', 'description', 'grade', 'created_at'])
                ->inRandomOrder()
                ->limit(6)
                ->get()
                ->map(function($course) {
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                        'subject' => $course->subject,
                        'type' => $course->type,
                        'category' => $course->category,
                        'description' => $course->description,
                        'thumbnail' => $this->getCourseThumbnail($course),
                        'fee' => 0,
                        'student_count' => $course->students->count(),
                        'teacher' => $course->teacher,
                        'slug' => $this->generateSlug($course->name),
                        'grade' => $course->grade
                    ];
                });

            $instructors = User::where('role', 'teacher')
                ->select(['id', 'name', 'username', 'education_qualification', 'institute', 'experience'])
                ->limit(8)
                ->get()
                ->map(function($instructor) {
                    return [
                        'id' => $instructor->id,
                        'name' => $instructor->name,
                        'username' => $instructor->username,
                        'education_qualification' => $instructor->education_qualification,
                        'institute' => $instructor->institute,
                        'experience' => $instructor->experience,
                        'avatar' => $this->getInstructorAvatar($instructor),
                        'courses_count' => ClassModel::where('teacher_id', $instructor->id)->count(),
                        'rating' => 4.8
                    ];
                });

            $stats = [
                'total_students' => Student::count() ?: 1200,
                'total_instructors' => User::where('role', 'teacher')->count() ?: 45,
                'total_courses' => ClassModel::where('status', 'active')->count() ?: 85,
                'total_enrollments' => DB::table('class_student')->count() ?: 2500,
            ];

            $testimonials = [
                [
                    'id' => 1,
                    'name' => 'Sarah Johnson',
                    'role' => 'Student',
                    'content' => 'SkillGro transformed my learning experience. The courses are well-structured and the instructors are amazing!',
                    'avatar' => '/assets/img/testimonials/1.jpg',
                    'rating' => 5
                ],
                [
                    'id' => 2,
                    'name' => 'Mike Chen',
                    'role' => 'Professional',
                    'content' => 'The flexibility of online learning combined with expert instruction helped me advance my career.',
                    'avatar' => '/assets/img/testimonials/2.jpg',
                    'rating' => 5
                ],
                [
                    'id' => 3,
                    'name' => 'Emily Davis',
                    'role' => 'Parent',
                    'content' => 'My children love the interactive classes. The teachers are patient and knowledgeable.',
                    'avatar' => '/assets/img/testimonials/3.jpg',
                    'rating' => 4
                ]
            ];

            return Inertia::render('Frontend/Home', [
                'content' => $content, // Add content to the props
                'featuredCourses' => $featuredCourses,
                'instructors' => $instructors,
                'stats' => $stats,
                'testimonials' => $testimonials,
                'pageTitle' => 'SkillGro - Learn with Expert Teachers',
                'metaDescription' => 'Discover quality education with SkillGro. Learn from expert teachers, explore diverse courses, and transform your learning journey.',
                'auth' => [
                    'user' => Auth::check() ? [
                        'id' => Auth::user()->id,
                        'name' => Auth::user()->name,
                        'role' => Auth::user()->role,
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Home page error: ' . $e->getMessage());
            
            // Get default content as fallback
            $defaultContent = Content::getDefaultContent();
            $homeContent = array_filter($defaultContent, function($key) {
                return strpos($key, 'home_') === 0;
            }, ARRAY_FILTER_USE_KEY);
            
            return Inertia::render('Frontend/Home', [
                'content' => $homeContent,
                'featuredCourses' => [],
                'instructors' => [],
                'stats' => [
                    'total_students' => 1200,
                    'total_instructors' => 45,
                    'total_courses' => 85,
                    'total_enrollments' => 2500
                ],
                'testimonials' => [],
                'pageTitle' => 'SkillGro - Learn with Expert Teachers'
            ]);
        }
    }

    // About page
    public function about()
    {
        // Get about page content from database
        $content = Content::getAboutContent();

        return Inertia::render('Frontend/About', [
            'content' => $content
        ]);
    }

    public function courses(Request $request): Response
    {
        try {
            Log::info('📚 Loading courses page');

            $query = ClassModel::with(['teacher:id,name', 'students'])
                ->select('id', 'name', 'subject', 'grade', 'type', 'category', 'description', 'capacity', 'status', 'created_at')
                ->where('status', 'active');

            // Filter by category
            if ($request->has('category') && $request->category) {
                $query->where('category', $request->category);
            }

            // Filter by type
            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }

            // Filter by grade
            if ($request->has('grade') && $request->grade) {
                $query->where('grade', $request->grade);
            }

            // Search
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('subject', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%')
                      ->orWhere('category', 'like', '%' . $request->search . '%');
                });
            }

            // Sort
            $sort = $request->get('sort', 'latest');
            switch ($sort) {
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                case 'grade':
                    $query->orderBy('grade', 'asc');
                    break;
                case 'latest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $courses = $query->paginate(12)->withQueryString();

            // Transform courses for frontend
            $courses->getCollection()->transform(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'subject' => $course->subject,
                    'grade' => $course->grade,
                    'type' => $course->type,
                    'category' => $course->category,
                    'description' => $course->description,
                    'thumbnail' => $this->getCourseThumbnail($course),
                    'fee' => 0,
                    'capacity' => $course->capacity,
                    'student_count' => $course->students->count(),
                    'teacher' => $course->teacher,
                    'created_at' => $course->created_at->format('M d, Y'),
                    'slug' => $this->generateSlug($course->name),
                    'status' => $course->status
                ];
            });

            // Handle popular sort after transformation
            if ($sort === 'popular') {
                $sortedCollection = $courses->getCollection()->sortByDesc('student_count');
                $courses->setCollection($sortedCollection);
            }

            $categories = ClassModel::where('status', 'active')
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->filter()
                ->values();

            $types = ClassModel::where('status', 'active')
                ->distinct()
                ->pluck('type')
                ->filter()
                ->values();

            $grades = ClassModel::where('status', 'active')
                ->whereNotNull('grade')
                ->distinct()
                ->pluck('grade')
                ->filter()
                ->values()
                ->sort();

            Log::info("✅ Successfully loaded {$courses->count()} courses");

            return Inertia::render('Frontend/Courses', [
                'courses' => $courses->items(),
                'pagination' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                    'links' => $courses->linkCollection()->toArray()
                ],
                'filters' => [
                    'search' => $request->search,
                    'category' => $request->category,
                    'type' => $request->type,
                    'grade' => $request->grade,
                    'sort' => $sort,
                ],
                'categories' => $categories,
                'types' => $types,
                'grades' => $grades,
                'pageTitle' => 'Our Courses - SkillGro',
                'metaDescription' => 'Browse our comprehensive catalog of courses and classes. Find the perfect learning path for your educational journey.'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Courses page error: ' . $e->getMessage());
            Log::error('📝 Stack trace: ' . $e->getTraceAsString());
            
            return $this->renderCoursesWithFallback($request);
        }
    }

    private function renderCoursesWithFallback(Request $request): Response
    {
        Log::info('🔄 Using fallback courses data');
        
        $fallbackCourses = [
            [
                'id' => 1,
                'name' => 'Mathematics Grade 1',
                'subject' => 'Mathematics',
                'grade' => 1,
                'type' => 'regular',
                'category' => 'Primary',
                'description' => 'Basic mathematics for first grade students covering numbers, counting, and simple arithmetic.',
                'thumbnail' => '/assets/img/courses/course_thumb01.png',
                'fee' => 0,
                'capacity' => 30,
                'student_count' => 25,
                'teacher' => null,
                'created_at' => 'Jan 01, 2024',
                'slug' => 'mathematics-grade-1',
                'status' => 'active'
            ],
            [
                'id' => 2,
                'name' => 'English Language',
                'subject' => 'English',
                'grade' => null,
                'type' => 'other',
                'category' => 'Language',
                'description' => 'Improve your English language skills with practical conversation practice.',
                'thumbnail' => '/assets/img/courses/course_thumb02.png',
                'fee' => 0,
                'capacity' => 25,
                'student_count' => 18,
                'teacher' => null,
                'created_at' => 'Jan 01, 2024',
                'slug' => 'english-language',
                'status' => 'active'
            ]
        ];

        return Inertia::render('Frontend/Courses', [
            'courses' => $fallbackCourses,
            'filters' => $request->only(['search', 'category', 'type', 'grade', 'sort']),
            'categories' => ['Primary', 'Junior', 'Secondary', 'Language', 'Technology'],
            'types' => ['regular', 'other'],
            'grades' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            'pageTitle' => 'Our Courses - SkillGro',
            'metaDescription' => 'Browse our comprehensive catalog of courses and classes.'
        ]);
    }

    public function courseSingle($id): Response
    {
        try {
            $course = ClassModel::where('status', 'active')
                ->with(['teacher:id,name,email,experience,education_qualification,institute', 'students'])
                ->find($id);

            if (!$course) {
                return $this->renderNotFound('Course not found');
            }

            $courseData = [
                'id' => $course->id,
                'name' => $course->name,
                'subject' => $course->subject,
                'grade' => $course->grade,
                'type' => $course->type,
                'category' => $course->category,
                'description' => $course->description,
                'full_description' => $course->description,
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
                ->map(function($relatedCourse) {
                    return [
                        'id' => $relatedCourse->id,
                        'name' => $relatedCourse->name,
                        'subject' => $relatedCourse->subject,
                        'description' => $relatedCourse->description,
                        'thumbnail' => $this->getCourseThumbnail($relatedCourse),
                        'fee' => 0,
                        'student_count' => $relatedCourse->students->count(),
                        'teacher' => $relatedCourse->teacher,
                        'slug' => $this->generateSlug($relatedCourse->name),
                        'type' => $relatedCourse->type
                    ];
                });

            return Inertia::render('Frontend/CourseSingle', [
                'course' => $courseData,
                'relatedCourses' => $relatedCourses,
                'isEnrolled' => $isEnrolled,
                'pageTitle' => $course->name . ' - SkillGro',
                'metaDescription' => $course->description
            ]);

        } catch (\Exception $e) {
            Log::error('Course single page error: ' . $e->getMessage());
            
            return $this->renderNotFound('Course not found');
        }
    }

    public function instructors(Request $request): Response
    {
        try {
            Log::info('🎯 Loading instructors page from database');

            // Get all teachers from database
            $teachers = User::where('role', 'teacher')
                ->select([
                    'id', 'name', 'username', 'email',
                    'education_qualification', 'institute', 'experience',
                    'bio', 'created_at'
                ])
                ->orderBy('name')
                ->get();

            Log::info("📊 Found {$teachers->count()} teachers in database");

            // Debug: Check if teachers exist and have IDs
            $teachers->each(function($teacher) {
                Log::info("👨‍🏫 Teacher: {$teacher->name} (ID: {$teacher->id})");
            });

            // Transform teachers data
            $instructors = $teachers->map(function ($teacher) {
                try {
                    // DEBUG: Count courses for this teacher
                    $coursesCount = ClassModel::where('teacher_id', $teacher->id)->count();
                    
                    // DEBUG: Check if any classes exist for this teacher
                    $teacherClasses = ClassModel::where('teacher_id', $teacher->id)->get();
                    Log::info("📚 Teacher {$teacher->name} (ID: {$teacher->id}): {$coursesCount} courses found");
                    
                    if ($coursesCount > 0) {
                        Log::info("   Classes: " . $teacherClasses->pluck('name')->implode(', '));
                    }

                    // Count unique students
                    $totalStudents = DB::table('class_student')
                        ->join('classes', 'class_student.class_id', '=', 'classes.id')
                        ->where('classes.teacher_id', $teacher->id)
                        ->distinct('class_student.student_id')
                        ->count();

                    Log::info("   Students: {$totalStudents} students");

                    return [
                        'id' => $teacher->id,
                        'name' => $teacher->name ?? 'Unknown Instructor',
                        'username' => $teacher->username ?? '',
                        'email' => $teacher->email ?? '',
                        'avatar' => $this->getInstructorAvatar($teacher),
                        'education_qualification' => $teacher->education_qualification ?? 'Not specified',
                        'institute' => $teacher->institute ?? 'Not specified',
                        'experience' => $teacher->experience ?? 'Experienced educator',
                        'bio' => $teacher->bio ?? 'Professional instructor dedicated to student success.',
                        'courses_count' => $coursesCount,
                        'students_count' => $totalStudents,
                        'rating' => 4.8,
                        'created_at' => $teacher->created_at ? $teacher->created_at->format('M d, Y') : 'Unknown'
                    ];
                } catch (\Exception $e) {
                    Log::error("❌ Error processing teacher {$teacher->id}: " . $e->getMessage());
                    return [
                        'id' => $teacher->id,
                        'name' => $teacher->name ?? 'Unknown Instructor',
                        'username' => $teacher->username ?? '',
                        'email' => $teacher->email ?? '',
                        'avatar' => $this->getInstructorAvatar($teacher),
                        'education_qualification' => $teacher->education_qualification ?? 'Not specified',
                        'institute' => $teacher->institute ?? 'Not specified',
                        'experience' => $teacher->experience ?? 'Experienced educator',
                        'bio' => $teacher->bio ?? 'Professional instructor.',
                        'courses_count' => 0,
                        'students_count' => 0,
                        'rating' => 4.5,
                        'created_at' => $teacher->created_at ? $teacher->created_at->format('M d, Y') : 'Unknown'
                    ];
                }
            });

            // Get specializations for filter dropdown
            $specializations = User::where('role', 'teacher')
                ->whereNotNull('education_qualification')
                ->where('education_qualification', '!=', '')
                ->distinct()
                ->pluck('education_qualification')
                ->values();

            Log::info("✅ Successfully loaded {$instructors->count()} instructors with data");

            return Inertia::render('Frontend/Instructors', [
                'instructors' => $instructors->toArray(),
                'filters' => [
                    'search' => $request->search ?? '',
                    'specialization' => $request->specialization ?? '',
                ],
                'specializations' => $specializations->toArray(),
                'pageTitle' => 'Our Instructors - SkillGro',
                'metaDescription' => 'Meet our team of expert instructors and teachers. Learn from experienced professionals dedicated to your success.'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Instructors page error: ' . $e->getMessage());
            Log::error('📝 Stack trace: ' . $e->getTraceAsString());
            
            // Only use fallback if there's a serious error
            $teachers = User::where('role', 'teacher')->get();
            
            if ($teachers->count() > 0) {
                // If we have teachers but there was an error in processing, return basic data
                $basicInstructors = $teachers->map(function ($teacher) {
                    return [
                        'id' => $teacher->id,
                        'name' => $teacher->name,
                        'username' => $teacher->username,
                        'email' => $teacher->email,
                        'avatar' => $this->getInstructorAvatar($teacher),
                        'education_qualification' => $teacher->education_qualification,
                        'institute' => $teacher->institute,
                        'experience' => $teacher->experience,
                        'bio' => $teacher->bio,
                        'courses_count' => 0,
                        'students_count' => 0,
                        'rating' => 4.5,
                        'created_at' => $teacher->created_at->format('M d, Y')
                    ];
                });
                
                return Inertia::render('Frontend/Instructors', [
                    'instructors' => $basicInstructors->toArray(),
                    'filters' => [
                        'search' => $request->search ?? '',
                        'specialization' => $request->specialization ?? '',
                    ],
                    'specializations' => [],
                    'pageTitle' => 'Our Instructors - SkillGro',
                    'metaDescription' => 'Meet our team of expert instructors.'
                ]);
            }
            
            // Only use mock data as last resort
            $fallbackInstructors = $this->getFallbackInstructors();
            
            return Inertia::render('Frontend/Instructors', [
                'instructors' => $fallbackInstructors,
                'filters' => [
                    'search' => $request->search ?? '',
                    'specialization' => $request->specialization ?? '',
                ],
                'specializations' => ['HSC', 'BSC', 'BA', 'MA', 'MSC', 'PhD', 'Other'],
                'pageTitle' => 'Our Instructors - SkillGro',
                'metaDescription' => 'Meet our team of expert instructors.'
            ]);
        }
    }

    public function instructorDetails($id): Response
    {
        try {
            Log::info("🎯 Loading instructor details for ID: {$id}");

            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                return $this->renderNotFound('Invalid instructor ID');
            }

            // Get instructor
            $instructor = User::where('role', 'teacher')
                ->where('id', $id)
                ->select([
                    'id', 'name', 'username', 'email',
                    'education_qualification', 'institute', 'experience',
                    'created_at'
                ])
                ->first();

            if (!$instructor) {
                return $this->renderNotFound('Instructor not found');
            }

            Log::info("✅ Found instructor: {$instructor->name}");

            // Get instructor's classes
            $classes = ClassModel::where('teacher_id', $id)
                ->where('status', 'active')
                ->with(['students'])
                ->select(['id', 'name', 'description', 'category', 'created_at', 'type', 'grade', 'subject', 'code', 'capacity'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($course) {
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                        'description' => $course->description,
                        'category' => $course->category,
                        'thumbnail' => $this->getCourseThumbnail($course),
                        'student_count' => $course->students->count(),
                        'created_at' => $course->created_at->format('M d, Y'),
                        'type' => $course->type,
                        'grade' => $course->grade,
                        'subject' => $course->subject,
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
                'avatar' => $this->getInstructorAvatar($instructor),
                'education_qualification' => $instructor->education_qualification,
                'institute' => $instructor->institute,
                'experience' => $instructor->experience,
                'bio' => $this->generateBio($instructor),
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
                'pageTitle' => $instructor->name . ' - Instructor - SkillGro',
                'metaDescription' => $this->generateBio($instructor)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Instructor details page error: ' . $e->getMessage());
            Log::error('📝 Stack trace: ' . $e->getTraceAsString());
            
            return $this->renderNotFound('Error loading instructor details: ' . $e->getMessage());
        }
    }

    // Contact page
    public function contact(): Response
    {
        $contactInfo = [
            'address' => '123 Education Street, Learning City, 12345',
            'phone' => '+1 (555) 123-4567',
            'email' => 'info@skillgro.com',
            'working_hours' => 'Monday - Friday: 9:00 AM - 6:00 PM'
        ];

        return Inertia::render('Frontend/Contact', [
            'contactInfo' => $contactInfo,
            'pageTitle' => 'Contact Us - SkillGro',
            'metaDescription' => 'Get in touch with SkillGro. We\'re here to answer your questions and help you start your learning journey.'
        ]);
    }

    // REMOVED BLOG METHODS - They are now handled by BlogController
    // Blog page - REMOVED
    // Blog post page - REMOVED

    // Helper methods
    private function getFallbackInstructors()
    {
        return [
            [
                'id' => 1,
                'name' => 'Dr. Sarah Johnson',
                'username' => 'sarahj',
                'email' => 'sarah@example.com',
                'avatar' => '/assets/img/instructor/instructor01.png',
                'education_qualification' => 'PhD',
                'institute' => 'Harvard University',
                'experience' => '15 years teaching experience',
                'bio' => 'Expert educator with extensive experience in curriculum development and student mentoring.',
                'courses_count' => 8,
                'students_count' => 245,
                'rating' => 4.9,
                'created_at' => 'Jan 15, 2023'
            ],
            [
                'id' => 2,
                'name' => 'Prof. Michael Chen',
                'username' => 'michaelc',
                'email' => 'michael@example.com',
                'avatar' => '/assets/img/instructor/instructor02.png',
                'education_qualification' => 'MSC',
                'institute' => 'Stanford University',
                'experience' => '12 years in education',
                'bio' => 'Passionate about making complex concepts accessible to all students.',
                'courses_count' => 6,
                'students_count' => 189,
                'rating' => 4.8,
                'created_at' => 'Mar 22, 2023'
            ]
        ];
    }

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

    private function getCourseThumbnail($course)
    {
        $defaultThumbnails = [
            '/assets/img/courses/h5_course_thumb01.jpg',
            '/assets/img/courses/h5_course_thumb02.jpg',
            '/assets/img/courses/h5_course_thumb03.jpg',
            '/assets/img/courses/h5_course_thumb04.jpg'
        ];

        $index = 0;
        if ($course->type === 'regular') {
            $index = (($course->grade ?? 1) - 1) % 4;
        } else {
            $categoryHash = crc32($course->category ?? 'default');
            $index = $categoryHash % 4;
        }

        return $defaultThumbnails[$index] ?? $defaultThumbnails[0];
    }

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
        return Inertia::render('Frontend/Errors/404', [
            'message' => $message,
            'pageTitle' => 'Page Not Found - SkillGro'
        ]);
    }
}