<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FrontendController extends Controller
{
    // Get public courses for frontend
    public function getPublicCourses(Request $request)
    {
        try {
            $query = ClassModel::where('status', 'active')
                ->with(['teacher:id,name,email,avatar', 'students'])
                ->select('id', 'name', 'subject', 'grade', 'type', 'category', 'description', 'thumbnail', 'capacity', 'fee', 'status');

            // Filter by category
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            // Filter by type
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Search
            if ($request->has('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('subject', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }

            $courses = $query->paginate(12);

            return response()->json([
                'success' => true,
                'data' => $courses,
                'message' => 'Courses retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get featured courses
    public function getFeaturedCourses()
    {
        try {
            $featuredCourses = ClassModel::where('status', 'active')
                ->with(['teacher:id,name,avatar', 'students'])
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
                        'thumbnail' => $course->thumbnail,
                        'fee' => $course->fee,
                        'student_count' => $course->students->count(),
                        'teacher' => $course->teacher,
                        'slug' => $this->generateSlug($course->name)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $featuredCourses,
                'message' => 'Featured courses retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve featured courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get course by slug
    public function getCourseBySlug($slug)
    {
        try {
            $course = ClassModel::where('status', 'active')
                ->with(['teacher:id,name,email,avatar,experience,education_qualification', 'students'])
                ->find($this->extractIdFromSlug($slug));

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            $courseData = [
                'id' => $course->id,
                'name' => $course->name,
                'subject' => $course->subject,
                'type' => $course->type,
                'category' => $course->category,
                'description' => $course->description,
                'full_description' => $course->full_description,
                'thumbnail' => $course->thumbnail,
                'fee' => $course->fee,
                'capacity' => $course->capacity,
                'duration' => $course->duration,
                'level' => $course->level,
                'student_count' => $course->students->count(),
                'teacher' => $course->teacher,
                'slug' => $this->generateSlug($course->name),
                'created_at' => $course->created_at,
                'updated_at' => $course->updated_at
            ];

            return response()->json([
                'success' => true,
                'data' => $courseData,
                'message' => 'Course retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get courses by category
    public function getCoursesByCategory($category)
    {
        try {
            $courses = ClassModel::where('status', 'active')
                ->where('category', $category)
                ->with(['teacher:id,name,avatar', 'students'])
                ->get()
                ->map(function($course) {
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                        'subject' => $course->subject,
                        'description' => $course->description,
                        'thumbnail' => $course->thumbnail,
                        'fee' => $course->fee,
                        'student_count' => $course->students->count(),
                        'teacher' => $course->teacher,
                        'slug' => $this->generateSlug($course->name)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $courses,
                'message' => 'Category courses retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get public instructors
    public function getPublicInstructors(Request $request)
    {
        try {
            $instructors = User::where('role', 'teacher')
                ->select([
                    'id', 'name', 'username', 'email', 'dob',
                    'education_qualification', 'institute', 'experience',
                    'created_at'
                ])
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $instructors,
                'count' => $instructors->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching public instructors: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch instructors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllInstructors(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 12);
            $page = $request->get('page', 1);

            $instructors = User::where('role', 'teacher')
                ->select([
                    'id', 'name', 'username', 'email', 'dob',
                    'education_qualification', 'institute', 'experience',
                    'created_at'
                ])
                ->orderBy('name')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $instructors->items(),
                'pagination' => [
                    'current_page' => $instructors->currentPage(),
                    'per_page' => $instructors->perPage(),
                    'total' => $instructors->total(),
                    'last_page' => $instructors->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching all instructors: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch instructors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get instructor profile
    public function getInstructorProfile($id)
    {
        try {
            $instructor = User::where('role', 'teacher')
                ->where('id', $id)
                ->select([
                    'id', 'name', 'username', 'email', 'dob',
                    'education_qualification', 'institute', 'experience',
                    'created_at'
                ])
                ->first();

            if (!$instructor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instructor not found'
                ], 404);
            }

            // Get instructor's courses count using ClassModel instead of Course
            $coursesCount = ClassModel::where('teacher_id', $id)->count();

            // Add additional data
            $instructor->courses_count = $coursesCount;
            $instructor->rating = 4.8; // You can calculate this from reviews
            $instructor->students_count = 0; // Calculate from enrollments

            return response()->json([
                'success' => true,
                'data' => $instructor
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching instructor profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch instructor profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getInstructorCourses($id)
    {
        try {
            // Use ClassModel instead of Course
            $courses = ClassModel::where('teacher_id', $id)
                ->where('status', 'active')
                ->select(['id', 'name as title', 'description', 'category', 'level', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'slug' => $this->generateSlug($course->title),
                        'description' => $course->description,
                        'category' => $course->category,
                        'level' => $course->level,
                        'created_at' => $course->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $courses,
                'count' => $courses->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching instructor courses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch instructor courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Get public stats
    public function getPublicStats()
    {
        try {
            $stats = [
                'total_students' => DB::table('students')->count(),
                'total_instructors' => User::where('role', 'teacher')->count(),
                'total_courses' => ClassModel::where('status', 'active')->count(),
                'total_enrollments' => DB::table('class_student')->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Stats retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_students' => 50000,
                    'total_instructors' => 200,
                    'total_courses' => 150,
                    'total_enrollments' => 75000
                ],
                'message' => 'Using demo stats'
            ]);
        }
    }

    // Get testimonials
    public function getTestimonials()
    {
        try {
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

            return response()->json([
                'success' => true,
                'data' => $testimonials,
                'message' => 'Testimonials retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve testimonials',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get blog posts
    public function getBlogPosts(Request $request)
    {
        try {
            // For now, return mock data. In production, you'd use a BlogPost model
            $posts = [
                [
                    'id' => 1,
                    'title' => 'The Future of Online Education',
                    'excerpt' => 'Explore how online learning is transforming education worldwide.',
                    'content' => 'Full article content...',
                    'image' => '/assets/img/blog/1.jpg',
                    'category' => 'Education',
                    'author' => 'Dr. Sarah Wilson',
                    'date' => '2024-01-15',
                    'slug' => 'future-online-education',
                    'read_time' => '5 min read'
                ],
                [
                    'id' => 2,
                    'title' => 'Effective Study Techniques for Online Learning',
                    'excerpt' => 'Discover proven strategies to maximize your online learning experience.',
                    'content' => 'Full article content...',
                    'image' => '/assets/img/blog/2.jpg',
                    'category' => 'Study Tips',
                    'author' => 'Prof. Michael Brown',
                    'date' => '2024-01-10',
                    'slug' => 'effective-study-techniques',
                    'read_time' => '4 min read'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $posts,
                'message' => 'Blog posts retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve blog posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get single blog post
    public function getBlogPost($slug)
    {
        try {
            // Mock data for single post
            $post = [
                'id' => 1,
                'title' => 'The Future of Online Education',
                'content' => '<p>Online education has revolutionized the way we learn...</p>',
                'image' => '/assets/img/blog/1.jpg',
                'category' => 'Education',
                'author' => 'Dr. Sarah Wilson',
                'date' => '2024-01-15',
                'slug' => 'future-online-education',
                'read_time' => '5 min read',
                'tags' => ['Online Learning', 'Education', 'Technology']
            ];

            return response()->json([
                'success' => true,
                'data' => $post,
                'message' => 'Blog post retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve blog post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get blog categories
    public function getBlogCategories()
    {
        try {
            $categories = [
                ['name' => 'Education', 'slug' => 'education', 'count' => 15],
                ['name' => 'Study Tips', 'slug' => 'study-tips', 'count' => 8],
                ['name' => 'Career', 'slug' => 'career', 'count' => 6],
                ['name' => 'Technology', 'slug' => 'technology', 'count' => 10]
            ];

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Blog categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve blog categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Helper methods
    private function generateSlug($name)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    private function extractIdFromSlug($slug)
    {
        // Extract ID from slug (assuming format: "course-name-123")
        $parts = explode('-', $slug);
        return end($parts);
    }
}