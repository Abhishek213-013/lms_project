<?php
namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    // ============ INERTIA PAGE RENDERING METHODS ============

    public function allCourses(): Response
    {
        return Inertia::render('Admin/Courses/AllCourses', [
            'pageTitle' => 'All Courses',
            'breadcrumbs' => [
                ['name' => 'Courses', 'url' => '/admin/courses'],
                ['name' => 'All Courses', 'url' => '/admin/courses/all-courses']
            ]
        ]);
    }

    public function regularCourses(): Response
    {
        return Inertia::render('Admin/Courses/RegularCourses', [
            'pageTitle' => 'Regular Courses',
            'breadcrumbs' => [
                ['name' => 'Courses', 'url' => '/admin/courses'],
                ['name' => 'Regular Courses', 'url' => '/admin/courses/regular-courses']
            ]
        ]);
    }

    public function skillCourses(): Response
    {
        return Inertia::render('Admin/Courses/SkillCourses', [
            'pageTitle' => 'Skill Courses',
            'breadcrumbs' => [
                ['name' => 'Courses', 'url' => '/admin/courses'],
                ['name' => 'Skill Courses', 'url' => '/admin/courses/skill-courses']
            ]
        ]);
    }

    public function categories(): Response
    {
        try {
            // Get categories data
            $categoriesResponse = $this->getCourseCategories();
            $categoriesData = json_decode($categoriesResponse->getContent(), true);
            
            // Get other courses data
            $otherCoursesResponse = $this->getOtherCourses();
            $otherCoursesData = json_decode($otherCoursesResponse->getContent(), true);

            return Inertia::render('Admin/Courses/CourseCategories', [
                'pageTitle' => 'Course Categories',
                'breadcrumbs' => [
                    ['name' => 'Courses', 'url' => '/admin/courses'],
                    ['name' => 'Categories', 'url' => '/admin/courses/categories']
                ],
                'categories' => $categoriesData['data'] ?? $this->getMockCategories(),
                'otherCourses' => $otherCoursesData['data'] ?? $this->getMockOtherCourses(),
                'dataSource' => $categoriesData['source'] ?? 'mock',
                'error' => $categoriesData['message'] ?? ''
            ]);
        } catch (\Exception $e) {
            Log::error('Error in categories page: ' . $e->getMessage());
            
            return Inertia::render('Admin/Courses/CourseCategories', [
                'pageTitle' => 'Course Categories',
                'breadcrumbs' => [
                    ['name' => 'Courses', 'url' => '/admin/courses'],
                    ['name' => 'Categories', 'url' => '/admin/courses/categories']
                ],
                'categories' => $this->getMockCategories(),
                'otherCourses' => $this->getMockOtherCourses(),
                'dataSource' => 'mock',
                'error' => 'Failed to load categories data'
            ]);
        }
    }

    public function enrollments(): Response
    {
        try {
            Log::info("🎯 Rendering enrollments page");

            // Get academic classes with student counts
            $response = $this->getAcademicClasses();
            $data = json_decode($response->getContent(), true);

            $classes = $data['success'] ? $data['data'] : $this->getMockEnrollmentClasses();
            $source = $data['source'] ?? 'mock';

            Log::info("📊 Enrollments data loaded: " . count($classes) . " classes, source: {$source}");

            return Inertia::render('Admin/Courses/Enrollments', [
                'pageTitle' => 'Course Enrollments',
                'breadcrumbs' => [
                    ['name' => 'Courses', 'url' => '/admin/courses'],
                    ['name' => 'Enrollments', 'url' => '/admin/courses/enrollments']
                ],
                'classes' => $classes,
                'dataSource' => $source,
                'error' => $data['message'] ?? '',
                'loading' => false
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error in enrollments page: ' . $e->getMessage());
            
            return Inertia::render('Admin/Courses/Enrollments', [
                'pageTitle' => 'Course Enrollments',
                'breadcrumbs' => [
                    ['name' => 'Courses', 'url' => '/admin/courses'],
                    ['name' => 'Enrollments', 'url' => '/admin/courses/enrollments']
                ],
                'classes' => $this->getMockEnrollmentClasses(),
                'dataSource' => 'mock',
                'error' => 'Failed to load enrollment data: ' . $e->getMessage(),
                'loading' => false
            ]);
        }
    }

    public function reviews(): Response
    {
        return Inertia::render('Admin/Courses/CourseReviews', [
            'pageTitle' => 'Course Reviews',
            'breadcrumbs' => [
                ['name' => 'Courses', 'url' => '/admin/courses'],
                ['name' => 'Reviews', 'url' => '/admin/courses/reviews']
            ]
        ]);
    }

    public function classSubjects($grade): Response
    {
        return Inertia::render('Admin/Courses/ClassSubjects', [
            'pageTitle' => "Class {$grade} Subjects",
            'grade' => $grade,
            'breadcrumbs' => [
                ['name' => 'Courses', 'url' => '/admin/courses'],
                ['name' => 'Class Subjects', 'url' => '/admin/courses/class-subjects'],
                ['name' => "Class {$grade}", 'url' => "/admin/courses/class/{$grade}/subjects"]
            ]
        ]);
    }

    public function subjectTeachers($grade, $subjectId): Response
    {
        return Inertia::render('Admin/Courses/SubjectTeachers', [
            'pageTitle' => 'Subject Teachers',
            'grade' => $grade,
            'subjectId' => $subjectId,
            'breadcrumbs' => [
                ['name' => 'Courses', 'url' => '/admin/courses'],
                ['name' => 'Class Subjects', 'url' => '/admin/courses/class-subjects'],
                ['name' => "Class {$grade}", 'url' => "/admin/courses/class/{$grade}/subjects"],
                ['name' => 'Teachers', 'url' => "/admin/courses/class/{$grade}/subject/{$subjectId}/teachers"]
            ]
        ]);
    }

    public function categoryClasses($category): Response
    {
        try {
            Log::info("🎯 Rendering category classes page for: {$category}");

            if ($category === 'other-courses') {
                $response = $this->getOtherCourses();
                $data = json_decode($response->getContent(), true);
                
                return Inertia::render('Admin/Courses/CategoryClasses', [
                    'pageTitle' => 'Skill Based Courses',
                    'category' => $category,
                    'currentCategory' => $category,
                    'otherCourses' => $data['success'] ? $data['data'] : $this->getMockOtherCourses(),
                    'categoryClasses' => [],
                    'dataSource' => $data['source'] ?? 'mock',
                    'error' => $data['message'] ?? '',
                    'loading' => false,
                    'breadcrumbs' => [
                        ['name' => 'Courses', 'url' => '/admin/courses'],
                        ['name' => 'Categories', 'url' => '/admin/courses/categories'],
                        ['name' => 'Skill Courses', 'url' => "/admin/courses/category/{$category}"]
                    ]
                ]);
            } else {
                // Get real data from database
                $response = $this->getCategoryClasses($category);
                $data = json_decode($response->getContent(), true);
                
                Log::info("📦 Category classes data:", $data);

                return Inertia::render('Admin/Courses/CategoryClasses', [
                    'pageTitle' => ucfirst($category) . ' Classes',
                    'category' => $category,
                    'currentCategory' => $category,
                    'categoryClasses' => $data['success'] ? $data['data'] : $this->getMockCategoryClasses($category),
                    'otherCourses' => [],
                    'dataSource' => $data['source'] ?? 'mock',
                    'error' => $data['message'] ?? '',
                    'loading' => false,
                    'breadcrumbs' => [
                        ['name' => 'Courses', 'url' => '/admin/courses'],
                        ['name' => 'Categories', 'url' => '/admin/courses/categories'],
                        ['name' => ucfirst($category), 'url' => "/admin/courses/category/{$category}"]
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Error in categoryClasses page: ' . $e->getMessage());
            
            return Inertia::render('Admin/Courses/CategoryClasses', [
                'pageTitle' => ucfirst($category) . ' Classes',
                'category' => $category,
                'currentCategory' => $category,
                'categoryClasses' => $this->getMockCategoryClasses($category),
                'otherCourses' => $this->getMockOtherCourses(),
                'dataSource' => 'mock',
                'error' => 'Failed to load category data: ' . $e->getMessage(),
                'loading' => false,
                'breadcrumbs' => [
                    ['name' => 'Courses', 'url' => '/admin/courses'],
                    ['name' => 'Categories', 'url' => '/admin/courses/categories'],
                    ['name' => ucfirst($category), 'url' => "/admin/courses/category/{$category}"]
                ]
            ]);
        }
    }

    public function classEnrollments($grade): Response
    {
        try {
            Log::info("🎯 Rendering class enrollments page for grade: {$grade}");

            // Get enrollment data for the specific grade
            $response = $this->getClassEnrollments($grade);
            $data = json_decode($response->getContent(), true);

            $enrollmentData = $data['success'] ? $data['data'] : $this->getMockClassEnrollments($grade);
            $source = $data['source'] ?? 'mock';

            Log::info("📊 Class enrollments data loaded for grade {$grade}, source: {$source}");

            return Inertia::render('Admin/Courses/ClassEnrollments', [
                'pageTitle' => "Class {$grade} Enrollments",
                'grade' => $grade,
                'enrollmentData' => $enrollmentData,
                'dataSource' => $source,
                'error' => $data['message'] ?? '',
                'loading' => false,
                'breadcrumbs' => [
                    ['name' => 'Courses', 'url' => '/admin/courses'],
                    ['name' => 'Enrollments', 'url' => '/admin/courses/enrollments'],
                    ['name' => "Class {$grade}", 'url' => "/admin/courses/enrollments/class/{$grade}"]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error in class enrollments page: ' . $e->getMessage());
            
            return Inertia::render('Admin/Courses/ClassEnrollments', [
                'pageTitle' => "Class {$grade} Enrollments",
                'grade' => $grade,
                'enrollmentData' => $this->getMockClassEnrollments($grade),
                'dataSource' => 'mock',
                'error' => 'Failed to load class enrollment data: ' . $e->getMessage(),
                'loading' => false,
                'breadcrumbs' => [
                    ['name' => 'Courses', 'url' => '/admin/courses'],
                    ['name' => 'Enrollments', 'url' => '/admin/courses/enrollments'],
                    ['name' => "Class {$grade}", 'url' => "/admin/courses/enrollments/class/{$grade}"]
                ]
            ]);
        }
    }

    public function courseDetails($courseId): Response
    {
        return Inertia::render('Admin/Courses/CourseDetails', [
            'pageTitle' => 'Course Details',
            'courseId' => $courseId,
            'breadcrumbs' => [
                ['name' => 'Courses', 'url' => '/admin/courses'],
                ['name' => 'All Courses', 'url' => '/admin/courses/all-courses'],
                ['name' => 'Details', 'url' => "/admin/courses/course/{$courseId}/details"]
            ]
        ]);
    }

    public function courseTeachers($courseId): Response
    {
        return Inertia::render('Admin/Courses/CourseTeachers', [
            'pageTitle' => 'Course Teachers',
            'courseId' => $courseId,
            'breadcrumbs' => [
                ['name' => 'Courses', 'url' => '/admin/courses'],
                ['name' => 'All Courses', 'url' => '/admin/courses/all-courses'],
                ['name' => 'Teachers', 'url' => "/admin/courses/course/{$courseId}/teachers"]
            ]
        ]);
    }

    // ============ API METHODS ============

    /**
     * Get all classes (grades 1-12 and other courses)
     */
    public function getClasses(Request $request)
    {
        try {
            // Check if classes table exists and has data
            if (!Schema::hasTable('classes')) {
                $response = $this->getMockClasses();
            } else {
                // Get all classes from database
                $databaseClasses = ClassModel::select('id', 'name', 'subject', 'grade', 'teacher_id', 'status', 'type', 'category', 'capacity', 'image', 'thumbnail')
                    ->with(['teacher:id,name,email', 'students'])
                    ->get();

                // If no classes in database, return mock data
                if ($databaseClasses->isEmpty()) {
                    $response = $this->getMockClasses();
                } else {
                    $formattedClasses = [];

                    // Separate regular classes and other courses
                    $regularClasses = $databaseClasses->where('type', 'regular');
                    $otherCourses = $databaseClasses->where('type', 'other');

                    // Process regular classes - group by name to handle multiple subjects
                    $groupedRegularClasses = $regularClasses->groupBy('name');

                    foreach ($groupedRegularClasses as $className => $classes) {
                        $firstClass = $classes->first();
                        
                        $formattedClasses[] = [
                            'id' => $firstClass->id,
                            'grade' => $firstClass->grade,
                            'name' => $className,
                            'subjectCount' => $classes->unique('subject')->count(),
                            'studentCount' => $classes->sum(function($class) {
                                return $class->students->count();
                            }),
                            'teachers' => $classes->pluck('teacher')->filter()->unique()->values()->toArray(),
                            'status' => $firstClass->status,
                            'type' => 'regular',
                            'capacity' => $firstClass->capacity,
                            'description' => $firstClass->description,
                            'image' => $firstClass->image_url, // Use model accessor
                            'thumbnail' => $firstClass->thumbnail_url, // Use model accessor
                            'raw_image' => $firstClass->image, // Keep raw path for debugging
                            'raw_thumbnail' => $firstClass->thumbnail // Keep raw path for debugging
                        ];
                    }

                    // Process other courses
                    foreach ($otherCourses as $course) {
                        $formattedClasses[] = [
                            'id' => $course->id,
                            'name' => $course->name,
                            'category' => $course->category,
                            'studentCount' => $course->students->count(),
                            'teachers' => $course->teacher ? [[
                                'id' => $course->teacher->id,
                                'name' => $course->teacher->name,
                                'email' => $course->teacher->email
                            ]] : [],
                            'status' => $course->status,
                            'type' => 'other',
                            'capacity' => $course->capacity,
                            'description' => $course->description,
                            'image' => $course->image_url, // Use model accessor
                            'thumbnail' => $course->thumbnail_url, // Use model accessor
                            'raw_image' => $course->image, // Keep raw path for debugging
                            'raw_thumbnail' => $course->thumbnail // Keep raw path for debugging
                        ];
                    }

                    $response = response()->json([
                        'success' => true,
                        'data' => $formattedClasses,
                        'source' => 'database',
                        'total_classes' => count($formattedClasses),
                        'regular_count' => $groupedRegularClasses->count(),
                        'other_count' => $otherCourses->count()
                    ]);
                }
            }

            // If this is an Inertia request, return proper Inertia response
            if ($request->header('X-Inertia')) {
                $data = json_decode($response->getContent(), true);
                return Inertia::render('Admin/Courses/AllCourses', [
                    'classes' => $data['data'] ?? [],
                    'source' => $data['source'] ?? 'mock',
                    'total_classes' => $data['total_classes'] ?? 0,
                    'regular_count' => $data['regular_count'] ?? 0,
                    'other_count' => $data['other_count'] ?? 0
                ]);
            }

            // Otherwise return JSON response
            return $response;

        } catch (\Exception $e) {
            Log::error('Error fetching classes: ' . $e->getMessage());
            
            if ($request->header('X-Inertia')) {
                return Inertia::render('Admin/Courses/AllCourses', [
                    'classes' => [],
                    'source' => 'error',
                    'error' => 'Failed to load classes'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch classes'
            ], 500);
        }
    }

    /**
     * Search classes for header suggestions
     */
    public function searchClasses(Request $request)
    {
        try {
            $query = $request->get('query', '');
            
            $classes = ClassModel::withCount('students as student_count')
                ->where('status', 'active')
                ->when($query, function($q) use ($query) {
                    $q->where(function($queryBuilder) use ($query) {
                        $queryBuilder->where('name', 'like', "%{$query}%")
                            ->orWhere('subject', 'like', "%{$query}%")
                            ->orWhere('category', 'like', "%{$query}%")
                            ->orWhere('description', 'like', "%{$query}%");
                    });
                })
                ->when(empty($query), function($q) {
                    // When no query, return popular classes (most students)
                    $q->orderBy('student_count', 'desc');
                })
                ->when($query, function($q) {
                    // When there's a query, prioritize by relevance
                    $q->orderBy('student_count', 'desc');
                })
                ->limit(10)
                ->get(['id', 'name', 'subject', 'grade', 'type', 'category', 'description', 'image', 'thumbnail'])
                ->map(function($class) {
                    return [
                        'id' => $class->id,
                        'name' => $class->name,
                        'subject' => $class->subject,
                        'grade' => $class->grade,
                        'type' => $class->type,
                        'category' => $class->category,
                        'description' => $class->description,
                        'student_count' => $class->student_count,
                        'image' => $class->image_url, // Use model accessor
                        'thumbnail' => $class->thumbnail_url, // Use model accessor
                        'raw_image' => $class->image, // Debug info
                        'raw_thumbnail' => $class->thumbnail // Debug info
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $classes,
                'total' => $classes->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching classes: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to search classes',
                'data' => []
            ], 500);
        }
    }

    /**
     * Mock data for development
     */
    private function getMockClasses()
    {
        $mockClasses = [];
        $subjects = ['Mathematics', 'Science', 'English', 'Social Studies', 'Computer Science', 'Physical Education'];
        
        for ($grade = 1; $grade <= 12; $grade++) {
            $mockClasses[] = [
                'id' => $grade,
                'grade' => $grade,
                'name' => "Class {$grade}",
                'subjectCount' => count($subjects),
                'studentCount' => rand(20, 50),
                'teachers' => [],
                'status' => 'active',
                'type' => 'regular',
                'capacity' => 30,
                'image' => null,
                'thumbnail' => null
            ];
        }

        $otherCourses = [
            [
                'id' => 13,
                'name' => 'Life Skills',
                'category' => 'Life Skills',
                'studentCount' => 25,
                'teachers' => [],
                'status' => 'active',
                'type' => 'other',
                'capacity' => 30,
                'image' => null,
                'thumbnail' => null
            ],
            [
                'id' => 14,
                'name' => 'Spoken English',
                'category' => 'Spoken English',
                'studentCount' => 30,
                'teachers' => [],
                'status' => 'active',
                'type' => 'other',
                'capacity' => 25,
                'image' => null,
                'thumbnail' => null
            ]
        ];

        $mockClasses = array_merge($mockClasses, $otherCourses);

        return response()->json([
            'success' => true,
            'data' => $mockClasses,
            'source' => 'mock',
            'message' => 'Using mock data - no classes found in database'
        ]);
    }


    /**
     * Get subjects for a specific class/grade
     */
    public function getClassSubjects($grade)
    {
        try {
            $grade = (int)$grade;
            
            Log::info("Fetching subjects for grade: {$grade}");
            
            if (!Schema::hasTable('classes') || ClassModel::count() === 0) {
                return $this->getMockSubjects($grade);
            }

            // Handle different class naming patterns
            $possibleClassNames = [
                "Class {$grade}",      // "Class 1", "Class 2", etc.
                "Class {$grade}A",     // "Class 7A", "Class 10A"
                "{$grade}A",           // "7A"
                "Grade {$grade}",      // Alternative pattern
            ];

            Log::info("Searching for class names: " . implode(', ', $possibleClassNames));

            $subjects = ClassModel::where(function($query) use ($possibleClassNames) {
                    foreach ($possibleClassNames as $name) {
                        $query->orWhere('name', 'like', $name . '%');
                    }
                })
                ->orWhere('grade', $grade) // Also search by grade column
                ->select('id', 'subject', 'teacher_id', 'description', 'name', 'grade', 'type', 'image', 'thumbnail')
                ->with(['teacher:id,name,email,experience', 'students'])
                ->get();

            Log::info("Found {$subjects->count()} total records for grade {$grade}");

            // Filter out "other" type courses (like Career Counseling, Dance Class)
            $filteredSubjects = $subjects->filter(function($class) use ($grade) {
                // Skip if it's an "other" type course (not a regular class)
                if ($class->type === 'other') {
                    Log::info("Skipping other type course: {$class->name} - {$class->subject}");
                    return false;
                }
                
                // Additional filtering for grade-specific classes
                if ($class->grade !== null && $class->grade != $grade) {
                    Log::info("Skipping class with mismatched grade: {$class->name} - Grade {$class->grade}");
                    return false;
                }
                
                return true;
            });

            Log::info("After filtering, {$filteredSubjects->count()} subjects remain");

            if ($filteredSubjects->isEmpty()) {
                Log::info('No regular class subjects found after filtering, returning mock data');
                return $this->getMockSubjects($grade);
            }

            $formattedSubjects = $filteredSubjects->map(function($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->subject,
                    'code' => $this->generateSubjectCode($class->subject),
                    'studentCount' => $class->students->count(),
                    'className' => $class->name, // Include class name for debugging
                    'classGrade' => $class->grade, // Include grade for debugging
                    'description' => $class->description,
                    'image' => $class->image_url, // Use model accessor
                    'thumbnail' => $class->thumbnail_url, // Use model accessor
                    'raw_image' => $class->image, // Debug info
                    'raw_thumbnail' => $class->thumbnail, // Debug info
                    'assignedTeachers' => $class->teacher ? [[
                        'id' => $class->teacher->id,
                        'name' => $class->teacher->name,
                        'email' => $class->teacher->email,
                        'experience' => $class->teacher->experience
                    ]] : []
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedSubjects->values(), // Reset keys
                'source' => 'database',
                'grade' => $grade,
                'total_subjects' => $formattedSubjects->count(),
                'debug' => [
                    'searched_names' => $possibleClassNames,
                    'found_classes' => $filteredSubjects->pluck('name', 'subject')->toArray()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching class subjects: ' . $e->getMessage());
            return $this->getMockSubjects($grade);
        }
    }

    /**
     * Helper method to generate subject codes
     */
    private function generateSubjectCode($subjectName)
    {
        $commonCodes = [
            'mathematics' => 'MATH',
            'math' => 'MATH',
            'science' => 'SCI',
            'physics' => 'PHY',
            'chemistry' => 'CHEM',
            'biology' => 'BIO',
            'english' => 'ENG',
            'bangla' => 'BAN',
            'social studies' => 'SST',
            'computer science' => 'CS',
            'physical education' => 'PE',
            'history' => 'HIST',
            'geography' => 'GEO'
        ];
        
        $lowerSubject = strtolower($subjectName);
        
        foreach ($commonCodes as $key => $code) {
            if (str_contains($lowerSubject, $key)) {
                return $code;
            }
        }
        
        // Fallback: use first 3-4 letters
        return strtoupper(substr($subjectName, 0, 4));
    }

    /**
     * Mock subjects data
     */
    private function getMockSubjects($grade)
    {
        // Convert grade to integer to ensure proper arithmetic
        $grade = (int)$grade;
        
        $subjects = [
            ['id' => 1, 'name' => 'Mathematics', 'code' => 'MATH'],
            ['id' => 2, 'name' => 'Science', 'code' => 'SCI'],
            ['id' => 3, 'name' => 'English', 'code' => 'ENG'],
            ['id' => 4, 'name' => 'Social Studies', 'code' => 'SST'],
            ['id' => 5, 'name' => 'Computer Science', 'code' => 'CS'],
            ['id' => 6, 'name' => 'Physical Education', 'code' => 'PE']
        ];

        $mockSubjects = array_map(function($subject) use ($grade) {
            return [
                'id' => $subject['id'] + ($grade * 10), // Now grade is integer
                'name' => $subject['name'],
                'code' => $subject['code'],
                'studentCount' => rand(25, 45),
                'description' => $this->getSubjectDescription($subject['name']),
                'image' => null,
                'thumbnail' => null,
                'assignedTeachers' => []
            ];
        }, $subjects);

        return response()->json([
            'success' => true,
            'data' => $mockSubjects,
            'source' => 'mock'
        ]);
    }

    private function getMockOtherCourses()
    {
        return [
            [ 
                'id' => 1, 
                'name' => 'Life Skills', 
                'category' => 'Life Skills',
                'studentCount' => 45, 
                'capacity' => 50,
                'description' => 'Essential life skills for personal development',
                'status' => 'active',
                'image' => null,
                'thumbnail' => null
            ],
            [ 
                'id' => 2, 
                'name' => 'Spoken English', 
                'category' => 'Language',
                'studentCount' => 38, 
                'capacity' => 40,
                'description' => 'Improve English speaking and communication skills',
                'status' => 'active',
                'image' => null,
                'thumbnail' => null
            ]
        ];
    }


    /**
     * Get teachers for a specific subject
     */
    public function getSubjectTeachers($subjectId)
    {
        try {
            Log::info("📡 [DEBUG] getSubjectTeachers for subject ID: {$subjectId}");

            $class = ClassModel::with(['teacher'])->find($subjectId);
            
            if (!$class) {
                Log::error("❌ [DEBUG] Class not found with ID: {$subjectId}");
                return response()->json([
                    'success' => false,
                    'message' => 'Subject not found'
                ], 404);
            }

            Log::info("📡 [DEBUG] Found class: " . $class->name . " - " . $class->subject);
            Log::info("📡 [DEBUG] Current teacher_id: " . $class->teacher_id);

            $assignedTeachers = $class->teacher ? [[
                'id' => $class->teacher->id,
                'name' => $class->teacher->name,
                'email' => $class->teacher->email,
                'experience' => $class->teacher->experience,
                'qualification' => $class->teacher->education_qualification
            ]] : [];

            Log::info("📡 [DEBUG] Assigned teachers count: " . count($assignedTeachers));

            // Get available teachers (all teachers not assigned to this class)
            $availableTeachers = User::where('role', 'teacher')
                ->where(function($query) use ($class) {
                    $query->whereDoesntHave('taughtClasses', function($q) use ($class) {
                        $q->where('classes.id', $class->id);
                    })
                    ->orWhereNull('id');
                })
                ->select('id', 'name', 'email', 'education_qualification as qualification', 'experience')
                ->get();

            Log::info("📡 [DEBUG] Available teachers count: " . $availableTeachers->count());

            return response()->json([
                'success' => true,
                'data' => [
                    'subject' => [
                        'id' => $class->id,
                        'name' => $class->subject,
                        'code' => $this->generateSubjectCode($class->subject),
                        'className' => $class->name,
                        'image' => $class->image ? Storage::url($class->image) : null
                    ],
                    'assignedTeachers' => $assignedTeachers,
                    'availableTeachers' => $availableTeachers->map(function($teacher) {
                        return [
                            'id' => $teacher->id,
                            'name' => $teacher->name,
                            'qualification' => $teacher->qualification,
                            'experience' => $teacher->experience,
                            'email' => $teacher->email
                        ];
                    })->toArray()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('❌ [DEBUG] Error fetching subject teachers: ' . $e->getMessage());
            Log::error('❌ [DEBUG] Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teachers'
            ], 500);
        }
    }

    /**
     * Assign teacher to a subject/class
     */
    public function assignTeacher(Request $request, $subjectId)
    {
        DB::beginTransaction();
        try {
            Log::info("📡 [DEBUG] Starting assignTeacher for subject ID: {$subjectId}");
            Log::info("📡 [DEBUG] Request data: " . json_encode($request->all()));

            $validated = $request->validate([
                'teacher_id' => 'required|exists:users,id'
            ]);

            Log::info("📡 [DEBUG] Validation passed");

            // Use ClassModel instead of Subject
            $class = ClassModel::find($subjectId);
            
            if (!$class) {
                Log::error("❌ [DEBUG] Class not found with ID: {$subjectId}");
                return response()->json([
                    'success' => false,
                    'message' => 'Subject not found'
                ], 404);
            }

            Log::info("📡 [DEBUG] Found class: " . $class->name . " - " . $class->subject);
            Log::info("📡 [DEBUG] Current teacher_id before update: " . $class->teacher_id);

            $teacher = User::find($validated['teacher_id']);
            
            if (!$teacher) {
                Log::error("❌ [DEBUG] Teacher not found with ID: {$validated['teacher_id']}");
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not found'
                ], 404);
            }

            Log::info("📡 [DEBUG] Found teacher: " . $teacher->name);
            Log::info("📡 [DEBUG] Teacher role: " . $teacher->role);

            // Check if teacher has the right role
            if ($teacher->role !== 'teacher') {
                Log::error("❌ [DEBUG] User is not a teacher. Role: " . $teacher->role);
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user is not a teacher'
                ], 422);
            }

            // Check if already assigned
            if ($class->teacher_id == $teacher->id) {
                Log::info("📡 [DEBUG] Teacher already assigned to this subject");
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher is already assigned to this subject'
                ], 422);
            }

            Log::info("📡 [DEBUG] Current teacher_id: " . $class->teacher_id);
            Log::info("📡 [DEBUG] New teacher_id: " . $teacher->id);

            // Assign teacher
            $class->teacher_id = $teacher->id;
            $saved = $class->save();

            Log::info("📡 [DEBUG] Save operation result: " . ($saved ? 'SUCCESS' : 'FAILED'));
            
            // Verify the update
            $updatedClass = ClassModel::find($subjectId);
            Log::info("📡 [DEBUG] Teacher_id after update: " . $updatedClass->teacher_id);

            DB::commit();

            Log::info("✅ [DEBUG] Teacher assigned successfully");

            // Reload the class with teacher relationship
            $class->load('teacher');

            // Get updated available teachers (all teachers except the assigned one)
            $availableTeachers = User::where('role', 'teacher')
                ->where('id', '!=', $teacher->id)
                ->select('id', 'name', 'email', 'education_qualification as qualification', 'experience')
                ->get();

            Log::info("📡 [DEBUG] Available teachers count: " . $availableTeachers->count());

            $response = [
                'success' => true,
                'message' => 'Teacher assigned successfully',
                'data' => [
                    'subject' => [
                        'id' => $class->id,
                        'name' => $class->subject,
                        'code' => $this->generateSubjectCode($class->subject),
                        'className' => $class->name,
                        'image' => $class->image ? Storage::url($class->image) : null
                    ],
                    'assignedTeachers' => $class->teacher ? [[
                        'id' => $class->teacher->id,
                        'name' => $class->teacher->name,
                        'email' => $class->teacher->email,
                        'experience' => $class->teacher->experience,
                        'qualification' => $class->teacher->education_qualification
                    ]] : [],
                    'availableTeachers' => $availableTeachers->map(function($teacher) {
                        return [
                            'id' => $teacher->id,
                            'name' => $teacher->name,
                            'qualification' => $teacher->qualification,
                            'experience' => $teacher->experience,
                            'email' => $teacher->email
                        ];
                    })->toArray()
                ]
            ];

            Log::info("✅ [DEBUG] Response prepared successfully");

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ [DEBUG] Error assigning teacher: ' . $e->getMessage());
            Log::error('❌ [DEBUG] Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign teacher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove teacher from subject
     */
    public function removeTeacher($subjectId, $teacherId)
    {
        try {
            $class = ClassModel::find($subjectId);
            
            if (!$class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject not found'
                ], 404);
            }

            // Check if the teacher is actually assigned to this subject
            if ($class->teacher_id != $teacherId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not assigned to this subject'
                ], 400);
            }

            $class->update([
                'teacher_id' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Teacher removed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing teacher: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove teacher'
            ], 500);
        }
    }

    /**
     * Get enrollments for a class
     */
    public function getClassEnrollments($grade)
    {
        try {
            Log::info("📚 Fetching class enrollments for grade: {$grade}");

            if (!Schema::hasTable('classes')) {
                return response()->json([
                    'success' => true,
                    'data' => $this->getMockClassEnrollments($grade),
                    'source' => 'mock',
                    'message' => 'Classes table not found'
                ]);
            }

            $className = "Class {$grade}";
            
            $subjects = ClassModel::where('name', $className)
                ->where('type', 'regular')
                ->with([
                    'teacher:id,name,email,education_qualification,experience',
                    'students.user:id,name,email'
                ])
                ->get()
                ->map(function($class) {
                    return [
                        'id' => $class->id,
                        'subject' => $class->subject,
                        'code' => $class->code,
                        'teacher' => $class->teacher ? [
                            'id' => $class->teacher->id,
                            'name' => $class->teacher->name,
                            'email' => $class->teacher->email,
                            'qualification' => $class->teacher->education_qualification,
                            'experience' => $class->teacher->experience
                        ] : null,
                        'students' => $class->students->map(function($student) {
                            return [
                                'id' => $student->id,
                                'name' => $student->user->name,
                                'email' => $student->user->email,
                                'roll_number' => $student->roll_number,
                                'parent_name' => $student->parent_name,
                                'parent_contact' => $student->parent_contact
                            ];
                        })->toArray(),
                        'totalStudents' => $class->students->count()
                    ];
                });

            $enrollmentData = [
                'grade' => $grade,
                'className' => $className,
                'subjects' => $subjects->toArray()
            ];

            $source = ClassModel::where('name', $className)->exists() ? 'database' : 'mock';

            Log::info("✅ Class enrollments fetched for grade {$grade}: " . $subjects->count() . " subjects from {$source}");

            return response()->json([
                'success' => true,
                'data' => $enrollmentData,
                'source' => $source,
                'message' => $source === 'database' ? 'Data loaded successfully' : 'Using mock data'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error fetching class enrollments: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'data' => $this->getMockClassEnrollments($grade),
                'source' => 'mock',
                'message' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get course categories
     */
    public function getCourseCategories()
    {
        try {
            // Check if classes table exists
            if (!Schema::hasTable('classes')) {
                return response()->json([
                    'success' => true,
                    'data' => $this->getMockCategories(),
                    'message' => 'Using demonstration data - classes table not found'
                ]);
            }

            // Define categories with their grade ranges
            $categories = [
                [
                    'id' => 'primary',
                    'name' => 'Primary',
                    'grades' => 'Class 1-5',
                    'description' => 'Elementary education foundation',
                    'grades_range' => [1, 5]
                ],
                [
                    'id' => 'junior',
                    'name' => 'Junior',
                    'grades' => 'Class 6-8',
                    'description' => 'Middle school education',
                    'grades_range' => [6, 8]
                ],
                [
                    'id' => 'secondary',
                    'name' => 'Secondary',
                    'grades' => 'Class 9-10',
                    'description' => 'High school preparation',
                    'grades_range' => [9, 10]
                ],
                [
                    'id' => 'higher-secondary',
                    'name' => 'Higher Secondary',
                    'grades' => 'Class 11-12',
                    'description' => 'College preparation',
                    'grades_range' => [11, 12]
                ]
            ];

            // Calculate class count and student count for each category
            foreach ($categories as &$category) {
                [$start, $end] = $category['grades_range'];
                
                // Count classes in this grade range
                $classCount = ClassModel::where('type', 'regular')
                    ->whereBetween('grade', [$start, $end])
                    ->distinct('name')
                    ->count('name');
                
                // Count students in this grade range
                $studentCount = 0;
                for ($grade = $start; $grade <= $end; $grade++) {
                    $classes = ClassModel::where('type', 'regular')
                        ->where('grade', $grade)
                        ->get();
                    
                    foreach ($classes as $class) {
                        $studentCount += $class->students()->count();
                    }
                }
                
                $category['classCount'] = $classCount;
                $category['studentCount'] = $studentCount;
            }

            return response()->json([
                'success' => true,
                'data' => $categories,
                'source' => 'database'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'data' => $this->getMockCategories(),
                'message' => 'Using demonstration data - database query failed'
            ]);
        }
    }

    /**
     * Helper method to get student count for grades
     */
    private function getStudentCountForGrades(array $grades)
    {
        try {
            if (!Schema::hasTable('classes') || !Schema::hasTable('students')) {
                return 0;
            }

            $totalStudents = 0;
            foreach ($grades as $grade) {
                $class = ClassModel::where('grade', $grade)->first();
                if ($class) {
                    $totalStudents += $class->students()->count();
                }
            }
            
            return $totalStudents;
        } catch (\Exception $e) {
            Log::error('Error counting students: ' . $e->getMessage());
            return 0;
        }
    }

    private function getMockCategoryClasses($category)
    {
        $categoryRanges = [
            'primary' => [1, 5],
            'junior' => [6, 8],
            'secondary' => [9, 10],
            'higher-secondary' => [11, 12]
        ];
        
        $range = $categoryRanges[$category] ?? [1, 12];
        $classes = [];
        
        for ($grade = $range[0]; $grade <= $range[1]; $grade++) {
            $classes[] = [
                'id' => $grade,
                'grade' => $grade,
                'name' => "Class {$grade}",
                'subjectCount' => rand(5, 8),
                'studentCount' => rand(20, 40),
                'capacity' => 30,
                'image' => null,
                'thumbnail' => null
            ];
        }
        
        return $classes;
    }


    /**
     * Mock categories data
     */
    private function getMockCategories()
    {
        return [
            [
                'id' => 'primary',
                'name' => 'Primary',
                'grades' => 'Class 1-5',
                'description' => 'Elementary education foundation',
                'classCount' => 5,
                'studentCount' => 125
            ],
            [
                'id' => 'junior',
                'name' => 'Junior',
                'grades' => 'Class 6-8',
                'description' => 'Middle school education',
                'classCount' => 3,
                'studentCount' => 90
            ],
            [
                'id' => 'secondary',
                'name' => 'Secondary',
                'grades' => 'Class 9-10',
                'description' => 'High school preparation',
                'classCount' => 2,
                'studentCount' => 60
            ],
            [
                'id' => 'higher-secondary',
                'name' => 'Higher Secondary',
                'grades' => 'Class 11-12',
                'description' => 'College preparation',
                'classCount' => 2,
                'studentCount' => 50
            ]
        ];
    }

    /**
     * Get classes by category
     */
    public function getCategoryClasses($category)
    {
        try {
            Log::info("📡 Fetching category classes for: {$category}");

            $gradeRanges = [
                'primary' => [1, 5],
                'junior' => [6, 8],
                'secondary' => [9, 10],
                'higher-secondary' => [11, 12]
            ];

            if (!isset($gradeRanges[$category])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category'
                ], 404);
            }

            [$start, $end] = $gradeRanges[$category];
            $classes = [];

            // Check if we have any classes in the database
            $totalClasses = ClassModel::count();
            Log::info("📊 Total classes in database: {$totalClasses}");

            for ($grade = $start; $grade <= $end; $grade++) {
                Log::info("🔍 Checking grade: {$grade}");
                
                // Get all classes for this grade with student count
                $classData = ClassModel::where('grade', $grade)
                    ->where('type', 'regular')
                    ->withCount('students')
                    ->get();

                Log::info("📚 Found {$classData->count()} classes for grade {$grade}");

                // Count unique subjects for this grade
                $subjectCount = $classData->unique('subject')->count();
                
                // Count total students for this grade
                $studentCount = $classData->sum('students_count');

                Log::info("🎯 Grade {$grade}: {$subjectCount} subjects, {$studentCount} students");

                $classes[] = [
                    'id' => $grade,
                    'grade' => $grade,
                    'name' => "Class {$grade}",
                    'subjectCount' => $subjectCount,
                    'studentCount' => $studentCount,
                    'capacity' => 30,
                    'image' => $classData->first()->image ? Storage::url($classData->first()->image) : null,
                    'thumbnail' => $classData->first()->thumbnail ? Storage::url($classData->first()->thumbnail) : null
                ];
            }

            // Determine if we're using real data or mock data
            $hasData = ClassModel::whereBetween('grade', [$start, $end])->exists();
            $source = $hasData ? 'database' : 'mock';

            Log::info("🏷️ Data source: {$source}, Has data: " . ($hasData ? 'YES' : 'NO'));

            return response()->json([
                'success' => true,
                'data' => $classes,
                'source' => $source,
                'message' => $source === 'database' ? 'Data loaded from database' : 'No classes found in database for this category'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error fetching category classes: ' . $e->getMessage());
            Log::error('📝 Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch classes: ' . $e->getMessage(),
                'source' => 'error'
            ], 500);
        }
    }

    /**
     * For regular academic classes (Class 1-12)
     */
    public function getAcademicClasses()
    {
        try {
            Log::info("📚 Fetching academic classes");

            if (!Schema::hasTable('classes')) {
                return response()->json([
                    'success' => true,
                    'data' => $this->getMockEnrollmentClasses(),
                    'source' => 'mock',
                    'message' => 'Classes table not found'
                ]);
            }

            $classes = ClassModel::where('type', 'regular')
                ->whereNotNull('grade')
                ->select('id', 'name', 'grade', 'subject', 'teacher_id', 'status', 'image', 'thumbnail')
                ->with(['teacher:id,name', 'students'])
                ->get()
                ->groupBy('grade')
                ->map(function($gradeClasses, $grade) {
                    $firstClass = $gradeClasses->first();
                    
                    // Count unique subjects for this grade
                    $subjectCount = $gradeClasses->unique('subject')->count();
                    
                    // Count total students for this grade
                    $studentCount = $gradeClasses->sum(function($class) {
                        return $class->students->count();
                    });

                    return [
                        'id' => $grade,
                        'grade' => $grade,
                        'name' => "Class {$grade}",
                        'subjectCount' => $subjectCount,
                        'studentCount' => $studentCount,
                        'type' => 'academic',
                        'image' => $firstClass->image_url, // Use model accessor
                        'thumbnail' => $firstClass->thumbnail_url, // Use model accessor
                        'raw_image' => $firstClass->image, // Debug info
                        'raw_thumbnail' => $firstClass->thumbnail // Debug info
                    ];
                })
                ->values();

            $source = ClassModel::count() > 0 ? 'database' : 'mock';

            Log::info("✅ Academic classes fetched: " . $classes->count() . " classes from {$source}");

            return response()->json([
                'success' => true,
                'data' => $classes->toArray(),
                'source' => $source,
                'message' => $source === 'database' ? 'Data loaded successfully' : 'Using mock data'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error fetching academic classes: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'data' => $this->getMockEnrollmentClasses(),
                'source' => 'mock',
                'message' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * For other courses (Career Counseling, Dance Class, etc.)
     */
    public function getOtherCourses()
    {
        try {
            if (!Schema::hasTable('classes')) {
                return response()->json([
                    'success' => true,
                    'data' => $this->getMockOtherCourses(),
                    'source' => 'mock'
                ]);
            }

            $courses = ClassModel::where('type', 'other')
                ->select('id', 'name', 'category', 'capacity', 'status', 'description', 'image', 'thumbnail')
                ->withCount('students as studentCount')
                ->get()
                ->map(function($course) {
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                        'category' => $course->category,
                        'studentCount' => $course->studentCount,
                        'capacity' => $course->capacity,
                        'status' => $course->status,
                        'description' => $course->description,
                        'image' => $course->image_url, // Use model accessor
                        'thumbnail' => $course->thumbnail_url, // Use model accessor
                        'raw_image' => $course->image, // Debug info
                        'raw_thumbnail' => $course->thumbnail // Debug info
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $courses,
                'source' => 'database'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching other courses: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'data' => $this->getMockOtherCourses(),
                'source' => 'mock'
            ]);
        }
    }

    /**
     * Get all classes combined (academic + other)
     */
    public function getAllClasses()
    {
        try {
            $academicClasses = $this->getAcademicClasses()->getData()->data ?? [];
            $otherCourses = $this->getOtherCourses()->getData()->data ?? [];

            return response()->json([
                'success' => true,
                'data' => [
                    'academic' => $academicClasses,
                    'other' => $otherCourses
                ],
                'total_academic' => count($academicClasses),
                'total_other' => count($otherCourses)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching all classes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch all classes'
            ], 500);
        }
    }

    public function getCourse($courseId)
    {
        try {
            Log::info("📡 [DEBUG] Fetching course with ID: {$courseId}");

            // Use ClassModel to find the course with proper relationships
            $course = ClassModel::with([
                'teacher:id,name,email,experience,education_qualification', 
                'students:id,name,email'
            ])->find($courseId);

            if (!$course) {
                Log::warning("❌ [DEBUG] Course not found with ID: {$courseId}");
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            Log::info("✅ [DEBUG] Found course: {$course->name} (ID: {$course->id}, Type: {$course->type})");

            // Format response based on course type
            if ($course->type === 'other') {
                $formattedCourse = [
                    'id' => $course->id,
                    'name' => $course->name,
                    'category' => $course->category,
                    'description' => $course->description,
                    'capacity' => $course->capacity,
                    'status' => $course->status,
                    'code' => $course->code,
                    'studentCount' => $course->students->count(),
                    'teachers' => $course->teacher ? [[
                        'id' => $course->teacher->id,
                        'name' => $course->teacher->name,
                        'email' => $course->teacher->email,
                        'experience' => $course->teacher->experience,
                        'qualification' => $course->teacher->education_qualification
                    ]] : [],
                    'type' => 'other',
                    'image' => $course->image_url, // Use model accessor
                    'thumbnail' => $course->thumbnail_url, // Use model accessor
                    'raw_image' => $course->image, // Debug info
                    'raw_thumbnail' => $course->thumbnail, // Debug info
                    'created_at' => $course->created_at,
                    'updated_at' => $course->updated_at
                ];
            } else {
                $formattedCourse = [
                    'id' => $course->id,
                    'name' => $course->name,
                    'subject' => $course->subject,
                    'grade' => $course->grade,
                    'description' => $course->description,
                    'capacity' => $course->capacity,
                    'status' => $course->status,
                    'code' => $course->code,
                    'studentCount' => $course->students->count(),
                    'teachers' => $course->teacher ? [[
                        'id' => $course->teacher->id,
                        'name' => $course->teacher->name,
                        'email' => $course->teacher->email,
                        'experience' => $course->teacher->experience,
                        'qualification' => $course->teacher->education_qualification
                    ]] : [],
                    'type' => 'regular',
                    'image' => $course->image_url, // Use model accessor
                    'thumbnail' => $course->thumbnail_url, // Use model accessor
                    'raw_image' => $course->image, // Debug info
                    'raw_thumbnail' => $course->thumbnail, // Debug info
                    'created_at' => $course->created_at,
                    'updated_at' => $course->updated_at
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedCourse,
                'message' => 'Course retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ [DEBUG] Error fetching course: ' . $e->getMessage());
            Log::error('❌ [DEBUG] Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch course: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateCourse(Request $request, $courseId)
    {
        DB::beginTransaction();
        try {
            Log::info("🔄 Starting course/subject update for ID: {$courseId}");
            Log::info("📦 Request data:", $request->except(['image']));
            Log::info("📸 Has image file:", ['has_image' => $request->hasFile('image')]);
            
            $course = ClassModel::find($courseId);

            if (!$course) {
                Log::error("❌ Course/Subject not found with ID: {$courseId}");
                return response()->json([
                    'success' => false,
                    'message' => 'Course/Subject not found'
                ], 404);
            }

            Log::info("📊 Current course data:", [
                'id' => $course->id,
                'subject' => $course->subject,
                'image' => $course->image,
                'fillable' => $course->getFillable()
            ]);

            $validator = Validator::make($request->all(), [
                'subject' => 'sometimes|string|max:255',
                'code' => 'sometimes|string|max:50',
                'student_count' => 'sometimes|integer|min:0',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            if ($validator->fails()) {
                Log::error("❌ Validation failed:", $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [];

            // Handle subject name update
            if ($request->has('subject')) {
                $updateData['subject'] = $request->subject;
                Log::info("📝 Updating subject to: {$request->subject}");
            }

            // Handle code update
            if ($request->has('code')) {
                $updateData['code'] = $request->code;
                Log::info("📝 Updating code to: {$request->code}");
            }

            // Handle description update
            if ($request->has('description')) {
                $updateData['description'] = $request->description;
                Log::info("📝 Updating description");
            }

            // Handle new image upload
            if ($request->hasFile('image')) {
                Log::info("📸 Processing new image upload");
                
                // Delete old images if they exist
                if ($course->image) {
                    if (Storage::disk('public')->exists($course->image)) {
                        Storage::disk('public')->delete($course->image);
                        Log::info("✅ Deleted old image: {$course->image}");
                    }
                }

                // Store new image
                $imageFile = $request->file('image');
                $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $imageFile->getClientOriginalExtension();
                $filename = 'subject_' . $originalName . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
                $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
                
                // Store image
                $imagePath = $imageFile->storeAs('courses/images', $filename, 'public');
                Log::info("✅ New image stored at: {$imagePath}");
                
                $updateData['image'] = $imagePath;
                $updateData['thumbnail'] = null;
                $updateData['image_alt'] = $course->name . ' image';
                $updateData['image_caption'] = $course->name . ' - ' . ($course->subject ?? 'Subject');
                
                Log::info("🖼️ Image data to update:", [
                    'image' => $imagePath,
                    'thumbnail' => null,
                    'image_alt' => $updateData['image_alt'],
                    'image_caption' => $updateData['image_caption']
                ]);
            }

            Log::info("📝 Final update data to save:", $updateData);

            // Update the course/subject
            if (!empty($updateData)) {
                $updated = $course->update($updateData);
                Log::info("💾 Database update result:", ['success' => $updated]);
                
                // Verify the update
                $course->refresh();
                Log::info("🔄 Refreshed course data:", [
                    'id' => $course->id,
                    'subject' => $course->subject,
                    'image' => $course->image,
                    'updated_at' => $course->updated_at
                ]);
            } else {
                Log::info("ℹ️ No data to update");
            }

            DB::commit();

            Log::info("✅ Course/Subject updated successfully", [
                'id' => $course->id,
                'name' => $course->name,
                'subject' => $course->subject,
                'image' => $course->image,
                'image_url' => $course->image_url
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course/Subject updated successfully',
                'data' => [
                    'id' => $course->id,
                    'name' => $course->name,
                    'subject' => $course->subject,
                    'code' => $course->code,
                    'grade' => $course->grade,
                    'studentCount' => $course->students->count(),
                    'description' => $course->description,
                    'image' => $course->image_url,
                    'thumbnail' => $course->thumbnail_url,
                    'raw_image' => $course->image, // This should show the actual database value
                    'raw_thumbnail' => $course->thumbnail
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('💥 Error updating course/subject: ' . $e->getMessage());
            Log::error('💥 Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update course/subject: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Get public courses for frontend display
 */
    public function getPublicCourses()
    {
        try {
            Log::info("🎯 Fetching public courses for frontend");

            $courses = ClassModel::where('status', 'active')
                ->select([
                    'id', 
                    'name', 
                    'subject', 
                    'grade', 
                    'code', 
                    'type', 
                    'category', 
                    'description', 
                    'status',
                    'image',           // Include image field
                    'thumbnail',       // Include thumbnail field
                    'image_alt',
                    'image_caption',
                    'teacher_id',
                    'capacity',
                    'created_at',
                    'updated_at'
                ])
                ->withCount('students as student_count')
                ->with(['teacher:id,name']) // Include teacher info if needed
                ->get()
                ->map(function($course) {
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                        'subject' => $course->subject,
                        'grade' => $course->grade,
                        'code' => $course->code,
                        'type' => $course->type,
                        'category' => $course->category,
                        'description' => $course->description,
                        'status' => $course->status,
                        'student_count' => $course->student_count,
                        'image' => $course->image,           // Raw image path from database
                        'thumbnail' => $course->thumbnail,   // Raw thumbnail path from database
                        'image_url' => $course->image_url,   // Full URL from accessor
                        'thumbnail_url' => $course->thumbnail_url, // Full URL from accessor
                        'raw_image' => $course->image,       // Raw path for debugging
                        'teacher' => $course->teacher,
                        'capacity' => $course->capacity,
                        'created_at' => $course->created_at,
                        'updated_at' => $course->updated_at
                    ];
                });

            Log::info("📊 Public courses API response count: " . $courses->count());
            
            // Log sample data for debugging
            if ($courses->count() > 0) {
                Log::info("🖼️ Sample course image data:", [
                    'first_course' => [
                        'id' => $courses->first()['id'],
                        'name' => $courses->first()['name'],
                        'image' => $courses->first()['image'],
                        'image_url' => $courses->first()['image_url'],
                        'thumbnail' => $courses->first()['thumbnail'],
                        'thumbnail_url' => $courses->first()['thumbnail_url']
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $courses,
                'total' => $courses->count(),
                'message' => 'Courses fetched successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching public courses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Delete a course
     */
    public function deleteCourse($courseId)
    {
        try {
            $course = ClassModel::find($courseId);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            // Check if there are students enrolled
            if ($course->students()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete course with enrolled students'
                ], 400);
            }

            // Delete associated images
            $this->deleteImageFiles($course->image);

            $course->delete();

            return response()->json([
                'success' => true,
                'message' => 'Course deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting course: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete course'
            ], 500);
        }
    }

    public function createClass(Request $request)
    {
        DB::beginTransaction();
        try {
            Log::info("🎯 ============ STARTING COURSE CREATION ============");
            Log::info("📦 Request type: " . $request->type);
            Log::info("📦 All request data:", $request->all());

            // Basic validation first
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:regular,other',
                'grade' => 'required_if:type,regular|integer|between:1,12',
                'name' => 'required|string|max:255',
                'category' => 'required_if:type,other|string|max:255',
                'capacity' => 'nullable|integer|min:1|max:100',
                'status' => 'required|in:active,inactive,upcoming',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                Log::error("❌ Validation failed:", $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // For regular classes
            if ($request->type === 'regular') {
                Log::info("📚 Creating regular class with subjects");
                
                if (!$request->has('subjects') || !is_array($request->subjects)) {
                    throw new \Exception('Subjects data is required for regular classes');
                }

                Log::info("📝 Subjects count: " . count($request->subjects));
                Log::info("📝 Subjects data:", $request->subjects);

                $createdClasses = [];
                
                foreach ($request->subjects as $index => $subjectData) {
                    Log::info("🔍 ============ PROCESSING SUBJECT {$index} ============");
                    Log::info("📝 Subject data:", $subjectData);
                    
                    // Handle subject image upload WITHOUT Intervention Image
                    $subjectImageData = $this->processSubjectImageSimple($request, $index);
                    
                    $classData = [
                        'name' => $request->name,
                        'subject' => $subjectData['name'] ?? '',
                        'grade' => (int) $request->grade,
                        'code' => $subjectData['code'] ?? '',
                        'description' => $request->description ?? '',
                        'capacity' => $request->capacity ?? 30,
                        'status' => $request->status,
                        'type' => 'regular',
                        'category' => null,
                    ];

                    // Add image data if available
                    if ($subjectImageData['image']) {
                        $classData['image'] = $subjectImageData['image'];
                        $classData['thumbnail'] = null; // No thumbnail without Intervention Image
                        $classData['image_alt'] = ($subjectData['name'] ?? 'Subject') . ' image';
                        $classData['image_caption'] = ($subjectData['name'] ?? 'Subject') . ' - ' . $request->name;
                        Log::info("🖼️ SUCCESS: Added image for subject {$index}: {$subjectImageData['image']}");
                    } else {
                        Log::info("📸 No image found for subject {$index}");
                    }

                    Log::info("💾 Saving class data to database:", $classData);
                    
                    try {
                        $class = ClassModel::create($classData);
                        
                        if (!$class) {
                            throw new \Exception("Database create failed for subject: " . ($subjectData['name'] ?? 'Unknown'));
                        }
                        
                        Log::info("✅ SUCCESS: Created class for subject: " . ($subjectData['name'] ?? 'Unknown') . " (ID: {$class->id})");
                        $createdClasses[] = $class;
                        
                    } catch (\Exception $dbError) {
                        Log::error("❌ DATABASE ERROR for subject {$index}: " . $dbError->getMessage());
                        throw $dbError;
                    }
                }

                DB::commit();
                Log::info("🎉 SUCCESS: Created class with " . count($createdClasses) . " subjects");

                // Return the first class as representative
                $firstClass = $createdClasses[0];
                return response()->json([
                    'success' => true,
                    'message' => 'Class created successfully with ' . count($createdClasses) . ' subjects',
                    'data' => [
                        'id' => $firstClass->id,
                        'grade' => $firstClass->grade,
                        'name' => $firstClass->name,
                        'subjectCount' => count($createdClasses),
                        'studentCount' => 0,
                        'teachers' => [],
                        'status' => $firstClass->status,
                        'type' => 'regular',
                        'image' => $firstClass->image_url,
                        'thumbnail' => $firstClass->thumbnail_url
                    ]
                ], 201);

            } else {
                // For other courses
                Log::info("🎓 Creating other course");
                
                // Handle main course image WITHOUT Intervention Image
                $imageData = $this->processMainImageSimple($request);

                $classData = [
                    'name' => $request->name,
                    'subject' => $request->category,
                    'code' => $request->courseCode ?? '',
                    'description' => $request->description ?? '',
                    'capacity' => $request->capacity ?? 30,
                    'status' => $request->status,
                    'type' => 'other',
                    'category' => $request->category,
                    'grade' => null,
                ];

                // Add image data if available
                if ($imageData['image']) {
                    $classData['image'] = $imageData['image'];
                    $classData['thumbnail'] = null; // No thumbnail without Intervention Image
                    $classData['image_alt'] = $request->name . ' course image';
                    $classData['image_caption'] = $request->name . ' - ' . $request->category;
                    Log::info("🖼️ Added image for course: {$imageData['image']}");
                }

                Log::info("💾 Saving course data to database:", $classData);
                
                $class = ClassModel::create($classData);
                
                if (!$class) {
                    throw new \Exception("Failed to create other course");
                }

                DB::commit();
                Log::info("✅ SUCCESS: Created other course: " . $class->name . " (ID: {$class->id})");

                return response()->json([
                    'success' => true,
                    'message' => 'Course created successfully',
                    'data' => [
                        'id' => $class->id,
                        'name' => $class->name,
                        'category' => $class->category,
                        'studentCount' => 0,
                        'teachers' => [],
                        'status' => $class->status,
                        'type' => 'other',
                        'image' => $class->image_url,
                        'thumbnail' => $class->thumbnail_url
                    ]
                ], 201);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ ============ COURSE CREATION FAILED ============');
            Log::error('❌ Error message: ' . $e->getMessage());
            Log::error('❌ Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create course: ' . $e->getMessage(),
                'error' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ], 500);
        }
    }

    private function processSubjectImageSimple($request, $subjectIndex)
    {
        try {
            Log::info("📸 Looking for subject {$subjectIndex} image");
            
            $allFiles = $request->allFiles();
            $subjectImage = null;
            
            // Try multiple key patterns
            $possibleKeys = [
                "subject_image_{$subjectIndex}",
                "subjects[{$subjectIndex}][image]",
                "subjects.{$subjectIndex}.image",
            ];

            foreach ($possibleKeys as $key) {
                Log::info("🔍 Checking key: {$key}");
                
                if ($request->hasFile($key)) {
                    $subjectImage = $request->file($key);
                    Log::info("✅ FOUND FILE with key: {$key}");
                    break;
                }
            }

            if (!$subjectImage) {
                Log::info("📸 No image found for subject {$subjectIndex}");
                return ['image' => null, 'thumbnail' => null];
            }

            // Validate image
            $validator = Validator::make(['image' => $subjectImage], [
                'image' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120'
            ]);

            if ($validator->fails()) {
                Log::error("❌ Subject image validation failed: " . $validator->errors()->first());
                throw new \Exception('Invalid subject image file: ' . $validator->errors()->first());
            }

            // Generate unique filename
            $originalName = pathinfo($subjectImage->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $subjectImage->getClientOriginalExtension();
            $filename = 'subject_' . $originalName . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
            $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            
            // Store original image
            $imagePath = $subjectImage->storeAs('courses/subject-images', $filename, 'public');
            Log::info("✅ Subject image stored at: {$imagePath}");
            
            // No thumbnail creation - return null for thumbnail
            Log::info("📸 Thumbnail creation skipped");

            return [
                'image' => $imagePath,
                'thumbnail' => null
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error handling subject image upload: ' . $e->getMessage());
            return ['image' => null, 'thumbnail' => null];
        }
    }

    private function processMainImageSimple($request)
    {
        try {
            Log::info("📸 Checking for main course image");
            
            if (!$request->hasFile('image')) {
                Log::info("📸 No main course image found");
                return ['image' => null, 'thumbnail' => null];
            }

            $imageFile = $request->file('image');
            Log::info("📸 Found main image file: " . $imageFile->getClientOriginalName());

            // Validate image
            $validator = Validator::make(['image' => $imageFile], [
                'image' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120'
            ]);

            if ($validator->fails()) {
                Log::error("❌ Main image validation failed: " . $validator->errors()->first());
                throw new \Exception('Invalid image file: ' . $validator->errors()->first());
            }

            // Generate unique filename
            $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $imageFile->getClientOriginalExtension();
            $filename = 'course_' . $originalName . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
            $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            
            // Store original image
            $imagePath = $imageFile->storeAs('courses/images', $filename, 'public');
            Log::info("✅ Main course image stored at: {$imagePath}");
            
            // No thumbnail creation
            Log::info("📸 Thumbnail creation skipped for main image");

            return [
                'image' => $imagePath,
                'thumbnail' => null
            ];
            
        } catch (\Exception $e) {
            Log::error('❌ Error handling main image upload: ' . $e->getMessage());
            return ['image' => null, 'thumbnail' => null];
        }
    }

    public function debugFileUpload(Request $request)
    {
        try {
            Log::info("🔍 ============ DEBUG FILE UPLOAD ============");
            
            // Log all request data
            Log::info("📦 All POST data:", $request->except(['subjects']));
            
            // Log all files
            $allFiles = $request->allFiles();
            Log::info("📁 Total files: " . count($allFiles));
            
            foreach ($allFiles as $key => $file) {
                Log::info("📄 File - Key: '{$key}', Name: '{$file->getClientOriginalName()}', Size: {$file->getSize()}");
            }
            
            // Log subjects if present
            if ($request->has('subjects')) {
                Log::info("📚 Subjects data:", $request->subjects);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Debug information logged',
                'data' => [
                    'total_files' => count($allFiles),
                    'file_keys' => array_keys($allFiles),
                    'subjects_count' => $request->has('subjects') ? count($request->subjects) : 0,
                    'request_data_keys' => array_keys($request->all())
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Debug error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Debug failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function deleteImageFiles($imagePath)
    {
        try {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
                Log::info("🗑️ Deleted image: {$imagePath}");
            }

            // Also delete thumbnail if exists
            $thumbnailPath = $this->getThumbnailPath($imagePath);
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
                Log::info("🗑️ Deleted thumbnail: {$thumbnailPath}");
            }
        } catch (\Exception $e) {
            Log::error('❌ Error deleting image files: ' . $e->getMessage());
        }
    }

    private function getThumbnailPath($imagePath)
    {
        if (!$imagePath) return null;
        
        $filename = pathinfo($imagePath, PATHINFO_FILENAME);
        return 'courses/thumbnails/' . $filename . '_thumb.jpg';
    }

    public function getCourseTeachers($courseId)
    {
        try {
            $course = ClassModel::with(['teacher'])->find($courseId);
            
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            $assignedTeachers = $course->teacher ? [[
                'id' => $course->teacher->id,
                'name' => $course->teacher->name,
                'email' => $course->teacher->email,
                'experience' => $course->teacher->experience,
                'qualification' => $course->teacher->education_qualification
            ]] : [];

            // Get available teachers
            $availableTeachers = User::where('role', 'teacher')
                ->where(function($query) use ($course) {
                    $query->whereDoesntHave('taughtClasses', function($q) use ($course) {
                        $q->where('classes.id', $course->id);
                    })
                    ->orWhereNull('id');
                })
                ->select('id', 'name', 'email', 'education_qualification as qualification', 'experience')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'course' => [
                        'id' => $course->id,
                        'name' => $course->name,
                        'type' => $course->type,
                        'image' => $course->image ? Storage::url($course->image) : null
                    ],
                    'assignedTeachers' => $assignedTeachers,
                    'availableTeachers' => $availableTeachers
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching course teachers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch course teachers'
            ], 500);
        }
    }

    public function assignTeacherToCourse(Request $request, $courseId)
    {
        try {
            $course = ClassModel::find($courseId);
            
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            $validated = $request->validate([
                'teacher_id' => 'required|exists:users,id'
            ]);

            $teacher = User::find($validated['teacher_id']);
            
            if (!$teacher || $teacher->role !== 'teacher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user is not a teacher'
                ], 422);
            }

            // Assign teacher to course
            $course->teacher_id = $teacher->id;
            $course->save();

            return response()->json([
                'success' => true,
                'message' => 'Teacher assigned to course successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning teacher to course: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign teacher to course'
            ], 500);
        }
    }

    public function removeTeacherFromCourse($courseId, $teacherId)
    {
        try {
            $course = ClassModel::find($courseId);
            
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            if ($course->teacher_id != $teacherId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not assigned to this course'
                ], 400);
            }

            $course->teacher_id = null;
            $course->save();

            return response()->json([
                'success' => true,
                'message' => 'Teacher removed from course successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing teacher from course: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove teacher from course'
            ], 500);
        }
    }

    // ============ HELPER METHODS ============

    private function getMockEnrollmentClasses()
    {
        Log::info("🎭 Using mock enrollment classes");
        
        $classes = [];
        for ($grade = 1; $grade <= 12; $grade++) {
            $classes[] = [
                'id' => $grade,
                'grade' => $grade,
                'name' => "Class {$grade}",
                'studentCount' => rand(15, 35),
                'subjectCount' => 6,
                'capacity' => 40,
                'type' => 'academic',
                'image' => null,
                'thumbnail' => null
            ];
        }
        
        return $classes;
    }

    private function getMockClassEnrollments($grade)
    {
        Log::info("🎭 Using mock class enrollments for grade: {$grade}");
        
        $subjects = ['Mathematics', 'Science', 'English', 'Social Studies', 'Computer Science', 'Physical Education'];
        $mockSubjects = [];

        foreach ($subjects as $index => $subject) {
            $studentCount = rand(5, 25);
            $students = [];

            for ($i = 1; $i <= $studentCount; $i++) {
                $students[] = [
                    'id' => $i,
                    'name' => "Student {$i}",
                    'email' => "student{$i}@school.edu",
                    'roll_number' => "R{$grade}00{$i}",
                    'parent_name' => "Parent {$i}",
                    'parent_contact' => "+123456789{$i}"
                ];
            }

            $mockSubjects[] = [
                'id' => $index + 1,
                'subject' => $subject,
                'code' => strtoupper(substr($subject, 0, 3)),
                'teacher' => $index % 2 === 0 ? [
                    'id' => 1,
                    'name' => "Teacher {$subject}",
                    'email' => "teacher.{$subject}@school.edu",
                    'qualification' => "Masters in {$subject}",
                    'experience' => rand(5, 15) . " years"
                ] : null,
                'students' => $students,
                'totalStudents' => $studentCount
            ];
        }

        return [
            'grade' => $grade,
            'className' => "Class {$grade}",
            'subjects' => $mockSubjects
        ];
    }

    private function getSubjectDescription($subjectName)
    {
        $descriptions = [
            'Mathematics' => 'Develop problem-solving skills and mathematical thinking',
            'English' => 'Improve language proficiency and communication skills',
            'Science' => 'Explore scientific concepts and experimental methods',
            'Social Studies' => 'Understand society, culture, and human interactions',
            'Bengali' => 'Master Bengali language and literature',
            'Computer Science' => 'Learn programming and computational thinking',
            'Physical Education' => 'Develop physical fitness and sports skills',
            'HTML & CSS' => 'Build responsive websites with modern web technologies',
            'JavaScript' => 'Create interactive web applications and dynamic content',
            'React.js' => 'Build modern user interfaces with React framework',
            'Node.js' => 'Create server-side applications with JavaScript',
            'Database Management' => 'Learn SQL and database design principles',
            'UI/UX Design' => 'Design user-friendly interfaces and experiences'
        ];

        return $descriptions[$subjectName] ?? 'Comprehensive learning materials and expert instruction';
    }
}