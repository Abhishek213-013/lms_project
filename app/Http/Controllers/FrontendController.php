<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use App\Models\Student;
use App\Models\Resource;
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
    // Home page
    public function home(): Response
    {
        try {
            // Get the language from request or session, default to 'en'
            $language = $this->getCurrentLanguage();
            
            Log::info("Loading home page with language: " . $language);
            
            // Get home page content from database with language support
            $content = Content::getHomeContent($language);
            
            Log::info("Home content loaded for language {$language}:", $content);
            
            // Get featured courses with language support
            $featuredCourses = ClassModel::where('status', 'active')
                ->with(['teacher:id,name', 'students'])
                ->select([
                    'id', 'name', 'subject', 'type', 'category', 'description', 
                    'grade', 'created_at', 'image', 'thumbnail' // 🔥 ADD image fields here
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
                        'thumbnail' => $this->getCourseThumbnail($course), // 🔥 Use the fixed method
                        'image' => $course->image, // Include raw image data
                        'thumbnail_url' => $this->getCourseThumbnail($course), // Alias for frontend
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
                    'institute', 'experience', 'profile_picture' // 🔥 ADD profile_picture here
                ])
                ->limit(8)
                ->get()
                ->map(function($instructor) use ($language) {
                    // Calculate courses count for this instructor
                    $coursesCount = ClassModel::where('teacher_id', $instructor->id)->count();
                    
                    // Calculate total students for this instructor
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
                        'profile_picture' => $instructor->profile_picture, // 🔥 Include this
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

            $testimonials = [
                [
                    'id' => 1,
                    'name' => $language === 'bn' ? 'সারা জনসন' : 'Sarah Johnson',
                    'role' => $language === 'bn' ? 'শিক্ষার্থী' : 'Student',
                    'content' => $language === 'bn' 
                        ? 'স্কিলগ্রো আমার শেখার অভিজ্ঞতা পরিবর্তন করেছে। কোর্সগুলো ভালোভাবে সাজানো এবং ইন্সট্রাক্টররা অসাধারণ!' 
                        : 'SkillGro transformed my learning experience. The courses are well-structured and the instructors are amazing!',
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

            return Inertia::render('Frontend/Home', [
                'content' => $content,
                'featuredCourses' => $featuredCourses,
                'instructors' => $instructors,
                'stats' => $stats,
                'testimonials' => $testimonials,
                'pageTitle' => $language === 'bn' ? 'স্কিলগ্রো - বিশেষজ্ঞ শিক্ষকদের সাথে শিখুন' : 'SkillGro - Learn with Expert Teachers',
                'metaDescription' => $language === 'bn' 
                    ? 'স্কিলগ্রোর সাথে মানসম্মত শিক্ষা আবিষ্কার করুন। বিশেষজ্ঞ শিক্ষকদের থেকে শিখুন, বিভিন্ন কোর্স এক্সপ্লোর করুন এবং আপনার শেখার যাত্রা রূপান্তর করুন।'
                    : 'Discover quality education with SkillGro. Learn from expert teachers, explore diverse courses, and transform your learning journey.',
                'auth' => [
                    'user' => Auth::check() ? [
                        'id' => Auth::user()->id,
                        'name' => Auth::user()->name,
                        'role' => Auth::user()->role,
                    ] : null
                ],
                'currentLanguage' => $language,
                'availableLanguages' => ['en', 'bn']
            ]);

        } catch (\Exception $e) {
            Log::error('Home page error: ' . $e->getMessage());
            
            // Get default content as fallback with language support
            $language = $this->getCurrentLanguage();
            $defaultContent = Content::getDefaultContent($language);
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
                'pageTitle' => $language === 'bn' ? 'স্কিলগ্রো - বিশেষজ্ঞ শিক্ষকদের সাথে শিখুন' : 'SkillGro - Learn with Expert Teachers',
                'currentLanguage' => $language,
                'availableLanguages' => ['en', 'bn']
            ]);
        }
    }

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

    public function switchLanguage($language)
    {
        $validLanguages = ['en', 'bn'];
        $referer = request()->header('referer') ?? '/';
        
        if (in_array($language, $validLanguages)) {
            // Store language in session
            session(['lang' => $language]);
            
            // Also set the locale for current request
            app()->setLocale($language);
            
            Log::info("Language switched to: {$language}, Referer: {$referer}");
            
            // If it's an API request, return JSON
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Language switched to " . ($language === 'en' ? 'English' : 'Bengali'),
                    'language' => $language,
                    'redirect_url' => $referer
                ]);
            }
            
            // For web requests, redirect back
            return redirect($referer)->with('success', "Language switched to " . ($language === 'en' ? 'English' : 'Bengali'));
        }
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid language'
            ], 400);
        }
        
        return redirect($referer)->with('error', 'Invalid language');
    }

    // About page
    public function about()
    {
        $language = $this->getCurrentLanguage();
        
        Log::info("Loading about page with language: " . $language);
        
        // Get about page content from database with language support
        $content = Content::getAboutContent($language);

        Log::info("About content loaded for language {$language}:", $content);

        return Inertia::render('Frontend/About', [
            'content' => $content,
            'currentLanguage' => $language,
            'availableLanguages' => ['en', 'bn']
        ]);
    }

    private function getCurrentLanguage()
    {
        $language = request()->get('lang', session('lang', 'en'));
        
        // Validate language
        if (!in_array($language, ['en', 'bn'])) {
            $language = 'en';
        }
        
        return $language;
    }

    public function courses(Request $request): Response
    {
        try {
            $language = $this->getCurrentLanguage();
            Log::info('📚 Loading courses page with language: ' . $language);

            // 🔥 FIX: Include image and thumbnail fields in the select
            $query = ClassModel::with(['teacher:id,name', 'students'])
                ->select('id', 'name', 'subject', 'grade', 'type', 'category', 'description', 'capacity', 'status', 'created_at', 'image', 'thumbnail')
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
                case 'popular':
                    // We'll handle this after transformation
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

            // Transform courses for frontend with language support
            $courses->getCollection()->transform(function ($course) use ($language) {
                return [
                    'id' => $course->id,
                    'name' => $this->getTranslatedCourseName($course, $language),
                    'subject' => $this->getTranslatedSubject($course->subject, $language),
                    'grade' => $course->grade,
                    'type' => $course->type,
                    'category' => $this->getTranslatedCategory($course->category, $language),
                    'description' => $this->getTranslatedDescription($course, $language),
                    // Image data
                    'image' => $course->image,
                    'thumbnail' => $course->thumbnail,
                    'image_url' => $course->image_url,
                    'thumbnail_url' => $course->thumbnail_url,
                    'raw_image' => $course->image,
                    // Additional data
                    'fee' => 0,
                    'capacity' => $course->capacity,
                    'student_count' => $course->students->count(),
                    'enrollment_count' => $course->students->count(), // Alias for frontend
                    'studentCount' => $course->students->count(), // Alias for frontend
                    'teacher' => $course->teacher,
                    'created_at' => $course->created_at->format('M d, Y'),
                    'slug' => $this->generateSlug($course->name),
                    'status' => $course->status,
                    'original_name' => $course->name,
                    'original_subject' => $course->subject,
                    'original_description' => $course->description,
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

            // 🔥 ADD: Debug logging to see what image data is being sent
            if ($courses->count() > 0) {
                $firstCourse = $courses->first();
                Log::info('🖼️ First course image data:', [
                    'id' => $firstCourse['id'],
                    'name' => $firstCourse['name'],
                    'image' => $firstCourse['image'],
                    'image_url' => $firstCourse['image_url'],
                    'thumbnail' => $firstCourse['thumbnail'],
                    'thumbnail_url' => $firstCourse['thumbnail_url']
                ]);
            }

            Log::info("✅ Successfully loaded {$courses->count()} courses with image data");

            // 🔥 FIX: Return the paginated object directly instead of splitting it
            return Inertia::render('Frontend/Courses', [
                'courses' => $courses, // Return the full paginator object
                'filters' => [
                    'search' => $request->search,
                    'category' => $request->category,
                    'type' => $request->type,
                    'grade' => $request->grade,
                    'sort' => $sort,
                    'per_page' => $perPage,
                ],
                'categories' => $categories->map(function($category) use ($language) {
                    return $this->getTranslatedCategory($category, $language);
                })->toArray(),
                'types' => $types,
                'grades' => $grades,
                'pageTitle' => $language === 'bn' ? 'আমাদের কোর্সসমূহ - স্কিলগ্রো' : 'Our Courses - SkillGro',
                'metaDescription' => $language === 'bn' 
                    ? 'আমাদের ব্যাপক কোর্স এবং ক্লাস ক্যাটালগ ব্রাউজ করুন। আপনার শিক্ষাগত যাত্রার জন্য নিখুঁত লার্নিং পথ খুঁজুন।'
                    : 'Browse our comprehensive catalog of courses and classes. Find the perfect learning path for your educational journey.',
                'currentLanguage' => $language,
                'availableLanguages' => ['en', 'bn']
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Courses page error: ' . $e->getMessage());
            Log::error('📝 Stack trace: ' . $e->getTraceAsString());
            
            return $this->renderCoursesWithFallback($request);
        }
    }

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
            ],
            [
                'id' => 2,
                'name' => $language === 'bn' ? 'ইংরেজি ভাষা' : 'English Language',
                'subject' => $language === 'bn' ? 'ইংরেজি' : 'English',
                'grade' => null,
                'type' => 'other',
                'category' => $language === 'bn' ? 'ভাষা' : 'Language',
                'description' => $language === 'bn'
                    ? 'ব্যবহারিক কথোপকথন অনুশীলনের সাথে আপনার ইংরেজি ভাষার দক্ষতা উন্নত করুন।'
                    : 'Improve your English language skills with practical conversation practice.',
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
            'categories' => $language === 'bn' 
                ? ['প্রাথমিক', 'জুনিয়র', 'সেকেন্ডারি', 'ভাষা', 'প্রযুক্তি'] 
                : ['Primary', 'Junior', 'Secondary', 'Language', 'Technology'],
            'types' => ['regular', 'other'],
            'grades' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            'pageTitle' => $language === 'bn' ? 'আমাদের কোর্সসমূহ - স্কিলগ্রো' : 'Our Courses - SkillGro',
            'metaDescription' => $language === 'bn'
                ? 'আমাদের ব্যাপক কোর্স এবং ক্লাস ক্যাটালগ ব্রাউজ করুন।'
                : 'Browse our comprehensive catalog of courses and classes.',
            'currentLanguage' => $language,
            'availableLanguages' => ['en', 'bn']
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

    public function instructors(): Response
    {
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
            'order_column', // ADD THIS LINE
            'created_at'
        ])
        ->withCount(['classes as courses_count'])
        ->orderBy('order_column') // CHANGE FROM orderBy('name') to orderBy('order_column')
        ->get()
        ->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
                'education_qualification' => $teacher->education_qualification,
                'institute' => $teacher->institute,
                'experience' => $teacher->experience,
                'profile_picture' => $teacher->profile_picture,
                'order_column' => $teacher->order_column, // ADD THIS LINE
                'courses_count' => $teacher->courses_count,
                'students_count' => $this->getTeacherStudentsCount($teacher->id),
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

        return Inertia::render('Frontend/Instructors', [
            'instructors' => $teachers,
            'specializations' => $specializations,
            'filters' => request()->only(['search', 'specialization']),
            'meta' => [
                'title' => 'Our Instructors - SkillGro',
                'description' => 'Meet our qualified and experienced instructors dedicated to your learning journey.'
            ]
        ]);
    }

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
    
    // Helper methods
    private function getTeacherStudentsCount($teacherId)
    {
        return \App\Models\Student::whereHas('class', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->count();
    }

    private function getTeacherRating($teacherId)
    {
        return '4.8'; // Placeholder - implement your rating logic
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
        // 🔥 FIX: First priority - Use database images with proper URL formatting
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
    public function instructorDetails($id): Response
    {
        try {
            Log::info("🎯 Loading instructor details for ID: {$id}");

            // Get instructor with profile_picture
            $instructor = User::where('role', 'teacher')
                ->where('id', $id)
                ->select([
                    'id', 'name', 'username', 'email',
                    'education_qualification', 'institute', 'experience',
                    'profile_picture', // MAKE SURE THIS IS INCLUDED
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
                'education_qualification' => $instructor->education_qualification,
                'institute' => $instructor->institute,
                'experience' => $instructor->experience,
                'profile_picture' => $instructor->profile_picture, // THIS IS CRITICAL
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

            Log::info("📤 Sending instructor data to frontend:", [
                'has_profile_picture' => !empty($instructorData['profile_picture']),
                'profile_picture_path' => $instructorData['profile_picture']
            ]);

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