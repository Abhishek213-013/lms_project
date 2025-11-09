<?php
// app/Http/Controllers/TeacherController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClassModel;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class TeacherController extends Controller
{
    // ============ INERTIA PAGE METHODS ============

    /**
     * Display instructors page
     */
    public function index(): Response
    {
        $teachers = User::where('role', 'teacher')
            ->select([
                'id', 'name', 'username', 'email', 'dob',
                'education_qualification', 'institute', 'experience', 'bio',
                'created_at'
            ])
            ->orderBy('name')
            ->get();

        return Inertia::render('Frontend/Instructors', [
            'initialInstructors' => $teachers,
            'meta' => [
                'title' => 'Our Instructors - SkillGro',
                'description' => 'Meet our qualified and experienced instructors dedicated to your learning journey.'
            ]
        ]);
    }

    /**
     * Display teacher dashboard
     */
    public function dashboard(): Response
    {
        $teacher = Auth::user();
        
        return Inertia::render('Teacher/Portal', [
            'user' => $teacher,
            'initialData' => [
                'stats' => $this->getTeacherStatsData($teacher->id),
                'recentClasses' => $this->getRecentClassesData($teacher->id),
                'recentResources' => $this->getRecentResourcesData($teacher->id),
            ]
        ]);
    }

    /**
     * Display teacher portal - NEW METHOD
     */
    public function portal(Request $request): Response
    {
        $teacher = Auth::user();
        
        // If admin is viewing teacher portal with specific ID
        if ($request->has('id') && in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            $teacher = User::findOrFail($request->id);
        }

        // Get teacher's classes and resources
        $teacherClasses = $teacher->classes ?? [];
        $recentResources = $teacher->resources()->latest()->take(5)->get();

        return Inertia::render('Teacher/TeacherPortal', [
            'teacher' => $teacher,
            'teacherClasses' => $teacherClasses,
            'recentResources' => $recentResources,
        ]);
    }

    /**
     * Display class dashboard
     */
    public function classDashboard($classId): Response
    {
        $class = ClassModel::with([
                'teacher:id,name', 
                'students.user:id,name,email'
            ])
            ->withCount('students')
            ->findOrFail($classId);

        // Check if the current user has access to this class
        $user = Auth::user();
        if ($user->role === 'teacher' && $class->teacher_id !== $user->id) {
            abort(403, 'You do not have access to this class.');
        }

        // Get class stats
        $resourcesCount = Resource::where('class_id', $classId)->count();
        $recentActivities = $this->getRecentClassActivities($classId);

        // Format class data for the frontend
        $classData = [
            'id' => $class->id,
            'name' => $class->name,
            'subject' => $class->subject,
            'grade' => $class->grade,
            'studentCount' => $class->students_count,
            'description' => $class->description,
            'teacher_name' => $class->teacher->name ?? 'Unknown Teacher',
            'students' => $class->students->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->name ?? 'Unknown Student',
                    'email' => $student->user->email ?? 'No email',
                    'roll_number' => $student->roll_number,
                ];
            })
        ];

        return Inertia::render('Teacher/Class/Dashboard', [
            'user' => $user,
            'classData' => $classData,
            'stats' => [
                'totalResources' => $resourcesCount,
                'totalStudents' => $class->students_count,
                'totalAssignments' => 0, // You can implement this if you have assignments
                'totalSchedules' => 0, // You can implement this if you have a schedule system
            ],
            'recentActivities' => $recentActivities,
        ]);
    }

    /**
     * Display teacher public profile
     */
    public function publicProfile($id): Response
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        
        return Inertia::render('Frontend/InstructorDetails', [
            'teacher' => $teacher,
            'teacherCourses' => $this->getTeacherPublicCoursesData($id),
            'teacherStats' => $this->getTeacherPublicStatsData($id),
            'meta' => [
                'title' => $teacher->name . ' - Instructor - SkillGro',
                'description' => 'Learn from ' . $teacher->name . ', ' . ($teacher->education_qualification ?? 'qualified instructor') . ' at SkillGro.'
            ]
        ]);
    }

    /**
     * Display class assignments page
     */
    public function classAssignments($classId): Response
    {
        $class = ClassModel::findOrFail($classId);
        
        return Inertia::render('Teacher/Class/Assignments', [
            'user' => Auth::user(),
            'classId' => $classId,
            'initialData' => [
                'classInfo' => $this->getClassInfo($classId),
                'assignments' => $this->getClassAssignmentsData($classId),
                'assignmentStats' => $this->getAssignmentStatsData($classId),
            ]
        ]);
    }

    /**
     * Display class resources page
     */
    public function classResources($classId): Response
    {
        $class = ClassModel::findOrFail($classId);
        
        // Get resources for the class
        $resources = Resource::where('class_id', $classId)
            ->orWhereNull('class_id')
            ->with(['teacher:id,name,email', 'class:id,name'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($resource) {
                $fileInfo = [];
                
                if ($resource->file_path) {
                    try {
                        $fileSize = Storage::disk('public')->size($resource->file_path);
                        $fileInfo['size'] = $this->formatFileSize($fileSize);
                    } catch (\Exception $e) {
                        $fileInfo['size'] = 'Unknown';
                    }
                }

                return [
                    'id' => $resource->id,
                    'title' => $resource->title,
                    'type' => $resource->type,
                    'description' => $resource->description,
                    'file_info' => $fileInfo,
                    'file_path' => $resource->file_path,
                    'thumbnail_path' => $resource->thumbnail_path,
                    'download_count' => $resource->download_count ?? 0,
                    'created_at' => $resource->created_at->toISOString(),
                    'teacher' => $resource->teacher,
                    'class' => $resource->class ? $resource->class->name : 'General'
                ];
            });

        // Always return Inertia response for page requests
        return Inertia::render('Teacher/Class/Resources', [
            'user' => Auth::user(),
            'classId' => $classId,
            'classData' => [
                'id' => $class->id,
                'name' => $class->name,
                'subject' => $class->subject,
                'grade' => $class->grade,
                'teacher_name' => $class->teacher->name ?? 'Unknown Teacher',
                'student_count' => $class->students_count ?? 0,
            ],
            'resources' => $resources,
        ]);
    }

    /**
     * Display class schedule page
     */
    public function classSchedule($classId): Response
    {
        $class = ClassModel::findOrFail($classId);
        
        return Inertia::render('Teacher/Class/Schedule', [
            'user' => Auth::user(),
            'classId' => $classId,
            'initialData' => [
                'classInfo' => $this->getClassInfo($classId),
                'schedule' => $this->getClassScheduleData($classId),
            ]
        ]);
    }

    /**
     * Display create resource page
     */
    public function createResource($classId = null): Response
    {
        $teacherId = Auth::id();
        $classes = ClassModel::where('teacher_id', $teacherId)->get(['id', 'name']);
        
        return Inertia::render('Teacher/Resources/Create', [
            'user' => Auth::user(),
            'classId' => $classId,
            'initialData' => [
                'classes' => $classes,
                'resourceTypes' => [
                    ['value' => 'video', 'label' => 'Video Link'],
                ]
            ]
        ]);
    }

    /**
     * Display teacher analytics
     */
    public function analytics(): Response
    {
        $teacher = Auth::user();
        
        return Inertia::render('Teacher/Analytics', [
            'user' => $teacher,
            'initialData' => [
                'analytics' => $this->getTeacherAnalyticsData($teacher->id),
            ]
        ]);
    }

    /**
     * Display teacher settings
     */
    public function settings(): Response
    {
        $teacher = Auth::user();
        
        return Inertia::render('Teacher/Settings', [
            'user' => $teacher,
            'initialData' => [
                'profile' => $teacher,
                'preferences' => $this->getTeacherPreferencesData($teacher->id),
            ]
        ]);
    }

    // ============ API METHODS ============

    /**
     * Get all teachers
     */
    public function getAllTeachers(Request $request)
    {
        try {
            Log::info('Fetching all teachers');
            
            $teachers = User::where('role', 'teacher')
                ->select([
                    'id', 'name', 'username', 'email', 'dob',
                    'education_qualification', 'institute', 'experience', 'bio',
                    'created_at'
                ])
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $teachers,
                'count' => $teachers->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching all teachers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teachers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get public teachers (active only)
     */
    public function getPublicTeachers(Request $request)
    {
        try {
            Log::info('Fetching public teachers');
            
            $teachers = User::where('role', 'teacher')
                ->select([
                    'id', 'name', 'username', 'email', 'dob',
                    'education_qualification', 'institute', 'experience', 'bio',
                    'created_at'
                ])
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $teachers,
                'count' => $teachers->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching public teachers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teachers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active teachers
     */
    public function getActiveTeachers(Request $request)
    {
        try {
            Log::info('Fetching active teachers');
            
            $teachers = User::where('role', 'teacher')
                ->select([
                    'id', 'name', 'username', 'email', 'dob',
                    'education_qualification', 'institute', 'experience', 'bio',
                    'created_at'
                ])
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $teachers,
                'count' => $teachers->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching active teachers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teachers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured teachers
     */
    public function getFeaturedTeachers(Request $request)
    {
        try {
            Log::info('Fetching featured teachers');
            
            $teachers = User::where('role', 'teacher')
                ->select([
                    'id', 'name', 'username', 'email', 'dob',
                    'education_qualification', 'institute', 'experience', 'bio',
                    'created_at'
                ])
                ->orderBy('name')
                ->limit(6)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $teachers,
                'count' => $teachers->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching featured teachers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured teachers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get teacher public profile
     */
    public function getTeacherPublicProfile($id)
    {
        try {
            Log::info("Fetching teacher public profile with ID: {$id}");
            
            $teacher = User::where('role', 'teacher')
                ->where('id', $id)
                ->select([
                    'id', 'name', 'username', 'email', 'dob',
                    'education_qualification', 'institute', 'experience', 'bio',
                    'created_at'
                ])
                ->first();

            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $teacher
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching teacher public profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teacher profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get teacher public courses
     */
    public function getTeacherPublicCourses($id)
    {
        try {
            Log::info("Fetching public courses for teacher ID: {$id}");

            $courses = $this->getTeacherPublicCoursesData($id);

            return response()->json([
                'success' => true,
                'data' => $courses,
                'count' => count($courses)
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching teacher public courses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teacher courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update teacher profile
     */
    public function updateTeacherProfile(Request $request, $id)
    {
        try {
            $teacher = User::where('role', 'teacher')->find($id);
            
            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'username' => 'sometimes|required|string|unique:users,username,' . $id . '|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $id . '|max:255',
                'dob' => 'sometimes|required|date',
                'education_qualification' => 'sometimes|required|in:HSC,BSC,BA,MA,MSC,PhD,Other',
                'institute' => 'sometimes|required|string|max:255',
                'experience' => 'sometimes|nullable|string|max:255',
                'bio' => 'sometimes|nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $teacher->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Teacher profile updated successfully',
                'data' => $teacher
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating teacher profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update teacher profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload resource - SIMPLIFIED FOR YOUTUBE LINKS ONLY
     */
    public function uploadResource(Request $request, $id)
    {
        try {
            Log::info("📡 [UPLOAD] Starting upload for teacher ID: {$id}");
            Log::info("📡 [UPLOAD] Request data:", $request->all());

            // Simplified validation - only YouTube links are supported
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'type' => 'required|in:video', // Only video type for links
                'description' => 'nullable|string|max:1000',
                'assigned_class' => 'nullable|exists:classes,id',
                'content' => 'required|string|url|max:1000', // URL is required
                'thumbnail' => 'nullable|image|max:2048'
            ], [
                'title.required' => 'The title field is required.',
                'type.required' => 'Please select a resource type.',
                'type.in' => 'Only video links are supported.',
                'content.required' => 'Please provide a YouTube URL.',
                'content.url' => 'Please provide a valid YouTube URL.',
                'assigned_class.exists' => 'The selected class does not exist.',
                'thumbnail.image' => 'The thumbnail must be an image.',
                'thumbnail.max' => 'The thumbnail size must not exceed 2MB.'
            ]);

            if ($validator->fails()) {
                Log::error("❌ [UPLOAD] Validation failed:", $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Please check your input and try again.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            Log::info("✅ [UPLOAD] Validation passed");

            $thumbnailPath = null;
            $content = $request->content;

            Log::info("🔗 [UPLOAD] Storing video URL: {$content}");
            
            // If it's a YouTube URL, generate thumbnail path
            if ($this->isYouTubeUrl($content)) {
                $videoId = $this->getYouTubeVideoId($content);
                if ($videoId) {
                    $thumbnailPath = "youtube_{$videoId}";
                    Log::info("✅ [UPLOAD] YouTube video detected, thumbnail: {$thumbnailPath}");
                }
            } else {
                Log::warning("⚠️ [UPLOAD] Non-YouTube URL provided: {$content}");
            }

            // Handle custom thumbnail upload (optional)
            if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                try {
                    $thumbnail = $request->file('thumbnail');
                    $thumbnailName = 'thumb_' . time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $thumbnail->getClientOriginalName());
                    $thumbnailPath = $thumbnail->storeAs('thumbnails', $thumbnailName, 'public');
                    Log::info("✅ [UPLOAD] Custom thumbnail stored at: {$thumbnailPath}");
                } catch (\Exception $thumbError) {
                    Log::error("❌ [UPLOAD] Thumbnail storage error: " . $thumbError->getMessage());
                }
            }

            try {
                $resource = Resource::create([
                    'title' => $request->title,
                    'type' => 'video', // Always video for links
                    'description' => $request->description,
                    'content' => $content, // Store the YouTube URL
                    'file_path' => null, // No file path for links
                    'thumbnail_path' => $thumbnailPath,
                    'teacher_id' => $id,
                    'class_id' => $request->assigned_class,
                    'status' => 'active',
                    'download_count' => 0
                ]);

                Log::info("✅ [UPLOAD] Resource created successfully: ID {$resource->id}");

                return response()->json([
                    'success' => true,
                    'message' => 'Video link uploaded successfully',
                    'data' => [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'description' => $resource->description,
                        'file_path' => $resource->file_path,
                        'thumbnail_path' => $resource->thumbnail_path,
                        'content' => $resource->content,
                        'download_count' => $resource->download_count,
                        'created_at' => $resource->created_at
                    ]
                ]);

            } catch (\Exception $dbError) {
                Log::error("❌ [UPLOAD] Database error: " . $dbError->getMessage());
                
                // Clean up uploaded thumbnail if any
                if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save resource: ' . $dbError->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('❌ [UPLOAD] Unexpected error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get class resources
     */
    public function getClassResources($classId)
    {
        try {
            Log::info("Fetching resources for class ID: {$classId}");

            $resources = Resource::where('class_id', $classId)
                ->orWhereNull('class_id')
                ->with(['teacher:id,name,email', 'class:id,name'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($resource) {
                    $fileInfo = [];
                    
                    if ($resource->file_path) {
                        try {
                            $fileSize = Storage::disk('public')->size($resource->file_path);
                            $fileInfo['size'] = $this->formatFileSize($fileSize);
                        } catch (\Exception $e) {
                            $fileInfo['size'] = 'Unknown';
                        }
                    }

                    return [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'description' => $resource->description,
                        'file_info' => $fileInfo,
                        'file_path' => $resource->file_path,
                        'thumbnail_path' => $resource->thumbnail_path,
                        'download_count' => $resource->download_count ?? 0,
                        'created_at' => $resource->created_at->toISOString(),
                        'teacher' => $resource->teacher,
                        'class' => $resource->class ? $resource->class->name : 'General'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $resources
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching class resources: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch resources'
            ], 500);
        }
    }

    /**
     * Delete resource
     */
    public function deleteResource($resourceId)
    {
        try {
            Log::info("🗑️ [DELETE] Attempting to delete resource ID: {$resourceId}");

            $teacherId = Auth::id();
            Log::info("👨‍🏫 [DELETE] Current teacher ID: {$teacherId}");
            
            // Find the resource with teacher relationship
            $resource = Resource::with('teacher')
                ->where('id', $resourceId)
                ->first();

            if (!$resource) {
                Log::warning("❌ [DELETE] Resource not found with ID: {$resourceId}");
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found'
                ], 404);
            }

            Log::info("🔍 [DELETE] Found resource - Title: {$resource->title}, Teacher ID: {$resource->teacher_id}");

            // Check if the current teacher owns this resource
            if ($resource->teacher_id != $teacherId) {
                Log::warning("🚫 [DELETE] Access denied - Teacher {$teacherId} tried to delete resource owned by teacher {$resource->teacher_id}");
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this resource'
                ], 403);
            }

            Log::info("✅ [DELETE] Permission granted, proceeding with deletion");

            // Delete associated files if they exist
            if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
                Storage::disk('public')->delete($resource->file_path);
                Log::info("🗂️ [DELETE] Deleted file: {$resource->file_path}");
            }

            // Delete custom thumbnail (not YouTube thumbnails)
            if ($resource->thumbnail_path && 
                !str_starts_with($resource->thumbnail_path, 'youtube_') && 
                Storage::disk('public')->exists($resource->thumbnail_path)) {
                Storage::disk('public')->delete($resource->thumbnail_path);
                Log::info("🖼️ [DELETE] Deleted custom thumbnail: {$resource->thumbnail_path}");
            }

            // Delete the resource from database
            $resource->delete();
            Log::info("✅ [DELETE] Resource deleted successfully from database");

            return response()->json([
                'success' => true,
                'message' => 'Resource deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ [DELETE] Error deleting resource: ' . $e->getMessage());
            Log::error('❌ [DELETE] Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete resource: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update download count
     */
    public function updateDownloadCount($resourceId)
    {
        try {
            Log::info("Updating download count for resource ID: {$resourceId}");

            $resource = Resource::find($resourceId);
            
            if (!$resource) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found'
                ], 404);
            }

            // Increment download count
            $resource->increment('download_count');

            return response()->json([
                'success' => true,
                'message' => 'Download count updated',
                'download_count' => $resource->download_count
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating download count: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update download count'
            ], 500);
        }
    }

    /**
     * Get all resources for teacher
     */
    public function getAllResources()
    {
        try {
            Log::info('Fetching all resources');

            $teacherId = Auth::id();
            
            $resources = Resource::where('teacher_id', $teacherId)
                ->with(['teacher:id,name,email', 'class:id,name'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($resource) {
                    $fileInfo = [];
                    
                    if ($resource->file_path) {
                        try {
                            $fileSize = Storage::disk('public')->size($resource->file_path);
                            $fileInfo['size'] = $this->formatFileSize($fileSize);
                        } catch (\Exception $e) {
                            $fileInfo['size'] = 'Unknown';
                        }
                    }

                    return [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'description' => $resource->description,
                        'file_info' => $fileInfo,
                        'file_path' => $resource->file_path,
                        'thumbnail_path' => $resource->thumbnail_path,
                        'download_count' => $resource->download_count ?? 0,
                        'created_at' => $resource->created_at->toISOString(),
                        'teacher' => $resource->teacher,
                        'class' => $resource->class ? $resource->class->name : 'General'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $resources
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching all resources: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch resources'
            ], 500);
        }
    }

    /**
     * Get teacher resources
     */
    public function getTeacherResources($id)
    {
        try {
            Log::info("Fetching resources for teacher ID: {$id}");
            
            $teacher = User::find($id);
            if (!$teacher || $teacher->role !== 'teacher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not found',
                    'data' => []
                ], 404);
            }

            $resources = Resource::where('teacher_id', $id)
                ->with('class')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($resource) {
                    return [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'description' => $resource->description,
                        'class' => $resource->class ? $resource->class->name : 'General',
                        'uploaded_at' => $resource->created_at->format('Y-m-d'),
                        'status' => $resource->status
                    ];
                });

            Log::info("Found {$resources->count()} resources for teacher ID: {$id}");
            
            return response()->json([
                'success' => true,
                'data' => $resources
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getTeacherResources: ' . $e->getMessage());
            
            $mockResources = $this->getMockResources();
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch resources, using mock data',
                'data' => $mockResources
            ]);
        }
    }

    /**
     * Get teacher classes
     */
    public function getTeacherClasses($id)
    {
        try {
            Log::info("Fetching classes for teacher ID: {$id}");
            
            $teacher = User::find($id);
            if (!$teacher || $teacher->role !== 'teacher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not found',
                    'data' => []
                ], 404);
            }

            $classes = ClassModel::where('teacher_id', $id)
                ->withCount('students')
                ->get()
                ->map(function ($class) {
                    return [
                        'id' => $class->id,
                        'name' => $class->name,
                        'subject' => $class->subject,
                        'grade' => $class->grade,
                        'code' => $class->code,
                        'studentCount' => $class->students_count,
                        'capacity' => $class->capacity,
                        'status' => $class->status,
                        'description' => $class->description,
                        'schedule' => $class->schedule ? 'Custom Schedule' : 'Not Scheduled',
                        'type' => $class->type,
                        'category' => $class->category
                    ];
                });

            Log::info("Found {$classes->count()} classes for teacher ID: {$id}");
            
            return response()->json([
                'success' => true,
                'data' => $classes
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getTeacherClasses: ' . $e->getMessage());
            
            $mockClasses = $this->getMockClasses();
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch classes, using mock data',
                'data' => $mockClasses
            ]);
        }
    }

    /**
     * Get teacher
     */
    public function getTeacher($id)
    {
        try {
            Log::info("Fetching teacher with ID: {$id}");
            
            $teacher = User::select([
                'id', 'name', 'username', 'email', 'dob', 
                'education_qualification', 'institute', 'experience', 'bio',
                'role', 'created_at'
            ])->find($id);
            
            if (!$teacher) {
                Log::warning("Teacher not found with ID: {$id}");
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            
            if ($teacher->role !== 'teacher') {
                Log::warning("User with ID {$id} is not a teacher. Role: {$teacher->role}");
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a teacher'
                ], 404);
            }

            Log::info("Successfully fetched teacher: {$teacher->name}");
            return response()->json([
                'success' => true,
                'data' => $teacher
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getTeacher: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload resource files
     */
    public function uploadResourceFiles(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:512000', // 500MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'file_path' => $filePath,
                'file_url' => asset('storage/' . $filePath)
            ]);

        } catch (\Exception $e) {
            Log::error('Error uploading resource file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file'
            ], 500);
        }
    }

    /**
     * Delete file
     */
    public function deleteFile($filename)
    {
        try {
            if (Storage::disk('public')->exists($filename)) {
                Storage::disk('public')->delete($filename);
                
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error deleting file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file'
            ], 500);
        }
    }

    // ============ NEW API METHODS FOR TEACHER PORTAL ============

    /**
     * Get teacher portal data for API
     */
    public function getTeacherPortalData($id)
    {
        try {
            $teacher = User::findOrFail($id);
            
            // Ensure the authenticated user can access this data
            if (Auth::id() != $id && !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
                abort(403);
            }

            $teacherClasses = $teacher->classes ?? [];
            $recentResources = $teacher->resources()->latest()->take(5)->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'teacher' => $teacher,
                    'teacherClasses' => $teacherClasses,
                    'recentResources' => $recentResources,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching teacher portal data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teacher portal data'
            ], 500);
        }
    }

    /**
     * Get teacher stats for dashboard
     */
    public function getTeacherStats(Request $request)
    {
        try {
            $teacherId = Auth::id();
            
            $stats = [
                'total_classes' => ClassModel::where('teacher_id', $teacherId)->count(),
                'total_resources' => Resource::where('teacher_id', $teacherId)->count(),
                'total_students' => 0, // You'll need to implement this based on your student relationships
                'upcoming_schedules' => 0, // You'll need to implement this
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching teacher stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teacher stats'
            ], 500);
        }
    }

    /**
     * Get teacher dashboard data
     */
    public function getTeacherDashboardData($id)
    {
        try {
            $teacher = User::findOrFail($id);
            
            $stats = $this->getTeacherStatsData($id);
            $recentClasses = $this->getRecentClassesData($id);
            $recentResources = $this->getRecentResourcesData($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'teacher' => $teacher,
                    'stats' => $stats,
                    'recentClasses' => $recentClasses,
                    'recentResources' => $recentResources
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching teacher dashboard data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data'
            ], 500);
        }
    }

    // ============ HELPER METHODS ============

    /**
     * Get recent activities for a class
     */
    private function getRecentClassActivities($classId)
    {
        try {
            $activities = [];

            // Get recent resources
            $recentResources = Resource::where('class_id', $classId)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($resource) {
                    return [
                        'id' => 'resource_' . $resource->id,
                        'type' => 'resource',
                        'title' => $resource->title,
                        'description' => 'New resource uploaded',
                        'status' => 'active',
                        'created_at' => $resource->created_at->toISOString(),
                    ];
                });

            // You can add assignments and other activities here
            // For now, we'll just return resources as activities
            return $recentResources->toArray();

        } catch (\Exception $e) {
            Log::error('Error in getRecentClassActivities: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get teacher analytics data
     */
    private function getTeacherAnalyticsData($teacherId)
    {
        try {
            $coursesCount = ClassModel::where('teacher_id', $teacherId)->count();
            $resourcesCount = Resource::where('teacher_id', $teacherId)->count();
            $studentsCount = 0; // You would need to implement student counting logic

            // Get resource type distribution
            $resourceTypes = Resource::where('teacher_id', $teacherId)
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray();

            // Get monthly uploads for the last 6 months
            $monthlyUploads = Resource::where('teacher_id', $teacherId)
                ->where('created_at', '>=', now()->subMonths(6))
                ->select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'month' => date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year)),
                        'count' => $item->count
                    ];
                })
                ->reverse()
                ->values();

            return [
                'overview' => [
                    'total_courses' => $coursesCount,
                    'total_resources' => $resourcesCount,
                    'total_students' => $studentsCount,
                    'completion_rate' => 85, // Mock data
                ],
                'resource_distribution' => $resourceTypes,
                'monthly_uploads' => $monthlyUploads,
                'popular_resources' => Resource::where('teacher_id', $teacherId)
                    ->orderBy('download_count', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($resource) {
                        return [
                            'id' => $resource->id,
                            'title' => $resource->title,
                            'type' => $resource->type,
                            'downloads' => $resource->download_count ?? 0,
                            'created_at' => $resource->created_at->format('M d, Y'),
                        ];
                    })
            ];

        } catch (\Exception $e) {
            Log::error('Error in getTeacherAnalyticsData: ' . $e->getMessage());
            return [
                'overview' => [
                    'total_courses' => 0,
                    'total_resources' => 0,
                    'total_students' => 0,
                    'completion_rate' => 0,
                ],
                'resource_distribution' => [],
                'monthly_uploads' => [],
                'popular_resources' => []
            ];
        }
    }

    /**
     * Get teacher preferences data
     */
    private function getTeacherPreferencesData($teacherId)
    {
        try {
            $teacher = User::find($teacherId);
            
            return [
                'notifications' => [
                    'email_notifications' => true,
                    'push_notifications' => true,
                    'assignment_alerts' => true,
                    'resource_uploads' => true,
                    'student_messages' => true,
                ],
                'privacy' => [
                    'profile_visibility' => 'public',
                    'show_email' => false,
                    'show_phone' => false,
                    'allow_messages' => true,
                ],
                'appearance' => [
                    'theme' => 'light',
                    'language' => 'en',
                    'timezone' => 'UTC',
                ],
                'course_defaults' => [
                    'auto_approve_students' => false,
                    'default_resource_visibility' => 'published',
                    'allow_downloads' => true,
                    'enable_comments' => true,
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error in getTeacherPreferencesData: ' . $e->getMessage());
            return [
                'notifications' => [
                    'email_notifications' => true,
                    'push_notifications' => true,
                    'assignment_alerts' => true,
                    'resource_uploads' => true,
                    'student_messages' => true,
                ],
                'privacy' => [
                    'profile_visibility' => 'public',
                    'show_email' => false,
                    'show_phone' => false,
                    'allow_messages' => true,
                ],
                'appearance' => [
                    'theme' => 'light',
                    'language' => 'en',
                    'timezone' => 'UTC',
                ],
                'course_defaults' => [
                    'auto_approve_students' => false,
                    'default_resource_visibility' => 'published',
                    'allow_downloads' => true,
                    'enable_comments' => true,
                ]
            ];
        }
    }

    public function isYouTubeUrl($url)
    {
        if (!is_string($url)) return false;
        
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/.*[?&]v=([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/live\/([a-zA-Z0-9_-]+)'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get YouTube video ID from URL
     */
    public function getYouTubeVideoId($url)
    {
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/.*[?&]v=([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/live\/([a-zA-Z0-9_-]{11})/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }

    /**
     * Format file size in bytes to human readable format
     */
    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    // ============ DATA METHODS FOR INERTIA ============

    private function getTeacherStatsData($teacherId)
    {
        try {
            $coursesCount = ClassModel::where('teacher_id', $teacherId)->count();
            $resourcesCount = Resource::where('teacher_id', $teacherId)->count();

            return [
                'courses_count' => $coursesCount,
                'classes_count' => $coursesCount,
                'resources_count' => $resourcesCount,
                'students_count' => 0,
                'rating' => 4.8,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getTeacherStatsData: ' . $e->getMessage());
            return [
                'courses_count' => 0,
                'classes_count' => 0,
                'resources_count' => 0,
                'students_count' => 0,
                'rating' => 0,
            ];
        }
    }

    private function getRecentClassesData($teacherId)
    {
        try {
            $classes = ClassModel::where('teacher_id', $teacherId)
                ->withCount('students')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($class) {
                    return [
                        'id' => $class->id,
                        'name' => $class->name,
                        'subject' => $class->subject,
                        'studentCount' => $class->students_count,
                        'status' => $class->status,
                    ];
                });

            return $classes;
        } catch (\Exception $e) {
            Log::error('Error in getRecentClassesData: ' . $e->getMessage());
            return $this->getMockClasses();
        }
    }

    private function getRecentResourcesData($teacherId)
    {
        try {
            $resources = Resource::where('teacher_id', $teacherId)
                ->with('class')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($resource) {
                    return [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'class' => $resource->class ? $resource->class->name : 'General',
                        'uploaded_at' => $resource->created_at->format('Y-m-d'),
                    ];
                });

            return $resources;
        } catch (\Exception $e) {
            Log::error('Error in getRecentResourcesData: ' . $e->getMessage());
            return $this->getMockResources();
        }
    }

    private function getTeacherPublicCoursesData($teacherId)
    {
        try {
            $courses = ClassModel::where('teacher_id', $teacherId)
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

            return $courses;
        } catch (\Exception $e) {
            Log::error('Error in getTeacherPublicCoursesData: ' . $e->getMessage());
            return [];
        }
    }

    private function getTeacherPublicStatsData($teacherId)
    {
        try {
            $coursesCount = ClassModel::where('teacher_id', $teacherId)->count();
            $resourcesCount = Resource::where('teacher_id', $teacherId)->count();

            return [
                'courses_count' => $coursesCount,
                'students_count' => 0,
                'rating' => 4.8,
                'experience' => '5+ years',
            ];
        } catch (\Exception $e) {
            Log::error('Error in getTeacherPublicStatsData: ' . $e->getMessage());
            return [
                'courses_count' => 0,
                'students_count' => 0,
                'rating' => 0,
                'experience' => 'N/A',
            ];
        }
    }

    private function getClassInfo($classId)
    {
        try {
            $class = ClassModel::with(['teacher:id,name'])->find($classId);
            
            if ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'subject' => $class->subject,
                    'grade' => $class->grade,
                    'teacher_name' => $class->teacher->name ?? 'Unknown Teacher',
                    'student_count' => $class->students_count ?? 0,
                ];
            }

            return [
                'id' => $classId,
                'name' => 'Unknown Class',
                'subject' => 'N/A',
                'grade' => 'N/A',
                'teacher_name' => 'Unknown Teacher',
                'student_count' => 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getClassInfo: ' . $e->getMessage());
            return [
                'id' => $classId,
                'name' => 'Class Information Unavailable',
                'subject' => 'N/A',
                'grade' => 'N/A',
                'teacher_name' => 'Unknown Teacher',
                'student_count' => 0,
            ];
        }
    }

    private function getClassAssignmentsData($classId)
    {
        try {
            // This would be replaced with actual Assignment model data
            return $this->getMockAssignments();
        } catch (\Exception $e) {
            Log::error('Error in getClassAssignmentsData: ' . $e->getMessage());
            return $this->getMockAssignments();
        }
    }

    private function getAssignmentStatsData($classId)
    {
        return [
            'total_assignments' => 5,
            'active_assignments' => 3,
            'completed_assignments' => 2,
            'average_score' => 85,
        ];
    }

    private function getClassResourcesData($classId)
    {
        try {
            $resources = Resource::where('class_id', $classId)
                ->orWhereNull('class_id')
                ->with(['teacher:id,name,email', 'class:id,name'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($resource) {
                    $fileInfo = [];
                    
                    if ($resource->file_path) {
                        try {
                            $fileSize = Storage::disk('public')->size($resource->file_path);
                            $fileInfo['size'] = $this->formatFileSize($fileSize);
                        } catch (\Exception $e) {
                            $fileInfo['size'] = 'Unknown';
                        }
                    }

                    return [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'description' => $resource->description,
                        'file_info' => $fileInfo,
                        'download_count' => 0,
                        'created_at' => $resource->created_at->toISOString(),
                        'teacher' => $resource->teacher,
                        'class' => $resource->class ? $resource->class->name : 'General'
                    ];
                });

            return $resources;
        } catch (\Exception $e) {
            Log::error('Error in getClassResourcesData: ' . $e->getMessage());
            return $this->getMockResources();
        }
    }

    private function getResourceStatsData($classId)
    {
        return [
            'total_resources' => 8,
            'pdf_count' => 3,
            'video_count' => 2,
            'document_count' => 2,
            'link_count' => 1,
        ];
    }

    private function getClassScheduleData($classId)
    {
        return [
            'monday' => ['10:00 AM - 11:00 AM', 'Mathematics'],
            'tuesday' => ['02:00 PM - 03:00 PM', 'Science'],
            'wednesday' => ['10:00 AM - 11:00 AM', 'Mathematics'],
            'thursday' => ['02:00 PM - 03:00 PM', 'Science'],
            'friday' => ['10:00 AM - 11:00 AM', 'Revision'],
        ];
    }

    // ============ MOCK DATA METHODS ============

    private function getMockClasses()
    {
        return [
            [
                'id' => 1,
                'name' => 'Advanced Mathematics',
                'subject' => 'Mathematics',
                'grade' => 10,
                'studentCount' => 25,
                'schedule' => 'Mon, Wed, Fri 10:00 AM',
                'status' => 'Active'
            ],
            [
                'id' => 2,
                'name' => 'Physics 101',
                'subject' => 'Physics',
                'grade' => 11,
                'studentCount' => 30,
                'schedule' => 'Tue, Thu 2:00 PM',
                'status' => 'Active'
            ]
        ];
    }

    private function getMockResources()
    {
        return [
            [
                'id' => 1,
                'title' => 'Calculus Chapter 1 Notes',
                'type' => 'pdf',
                'class' => 'Advanced Mathematics',
                'uploaded_at' => '2024-01-15'
            ],
            [
                'id' => 2,
                'title' => 'Introduction to Physics Video',
                'type' => 'video',
                'class' => 'Physics 101',
                'uploaded_at' => '2024-01-14'
            ]
        ];
    }

    private function getMockAssignments()
    {
        return [
            [
                'id' => 1,
                'title' => 'Mathematics Chapter 1 Exercises',
                'description' => 'Complete all exercises from chapter 1 of your mathematics textbook.',
                'points' => 100,
                'due_date' => now()->addDays(7)->toISOString(),
                'status' => 'active',
                'submitted_count' => 15,
                'graded_count' => 10,
            ],
            [
                'id' => 2,
                'title' => 'Science Project Proposal',
                'description' => 'Submit your science project proposal with detailed methodology.',
                'points' => 50,
                'due_date' => now()->addDays(3)->toISOString(),
                'status' => 'active',
                'submitted_count' => 8,
                'graded_count' => 5,
            ],
        ];
    }

    private function generateSlug($name)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }


    // ============ NEW METHODS FOR SIDEBAR NAVIGATION ============

    /**
     * Display teacher classes list
     */
    public function classesList(): Response
    {
        $teacher = Auth::user();
        
        $classes = ClassModel::where('teacher_id', $teacher->id)
            ->withCount('students')
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'subject' => $class->subject,
                    'grade' => $class->grade,
                    'studentCount' => $class->students_count,
                    'schedule' => $class->schedule ? 'Custom Schedule' : 'Not Scheduled',
                    'status' => $class->status,
                    'description' => $class->description,
                ];
            });

        return Inertia::render('Teacher/Classes/Index', [
            'user' => $teacher,
            'classes' => $classes,
        ]);
    }

    /**
     * Display teacher student roster
     */
    public function studentRoster(): Response
    {
        $teacher = Auth::user();
        
        // Get all classes for this teacher with their students
        $classesWithStudents = ClassModel::where('teacher_id', $teacher->id)
            ->with(['students.user:id,name,email,phone'])
            ->withCount('students')
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'subject' => $class->subject,
                    'grade' => $class->grade,
                    'studentCount' => $class->students_count,
                    'students' => $class->students->map(function ($student) {
                        return [
                            'id' => $student->id,
                            'name' => $student->user->name ?? 'Unknown Student',
                            'email' => $student->user->email ?? 'No email',
                            'phone' => $student->user->phone ?? 'No phone',
                            'roll_number' => $student->roll_number,
                            'enrollment_date' => $student->created_at->format('Y-m-d'),
                        ];
                    })
                ];
            });

        return Inertia::render('Teacher/Students/Roster', [
            'user' => $teacher,
            'classesWithStudents' => $classesWithStudents,
        ]);
    }

    /**
     * Display teacher resources list
     */
    public function teacherResources(): Response
    {
        $teacher = Auth::user();
        
        $resources = Resource::where('teacher_id', $teacher->id)
            ->with(['class:id,name'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($resource) {
                $fileInfo = [];
                
                if ($resource->file_path) {
                    try {
                        $fileSize = Storage::disk('public')->size($resource->file_path);
                        $fileInfo['size'] = $this->formatFileSize($fileSize);
                    } catch (\Exception $e) {
                        $fileInfo['size'] = 'Unknown';
                    }
                }

                return [
                    'id' => $resource->id,
                    'title' => $resource->title,
                    'type' => $resource->type,
                    'description' => $resource->description,
                    'file_info' => $fileInfo,
                    'file_path' => $resource->file_path,
                    'thumbnail_path' => $resource->thumbnail_path,
                    'content' => $resource->content,
                    'download_count' => $resource->download_count ?? 0,
                    'created_at' => $resource->created_at->toISOString(),
                    'class' => $resource->class ? $resource->class->name : 'General',
                    'status' => $resource->status,
                ];
            });

        return Inertia::render('Teacher/Resources/Index', [
            'user' => $teacher,
            'resources' => $resources,
        ]);
    }

    /**
     * Display shared resources
     */
    public function sharedResources(): Response
    {
        $teacher = Auth::user();
        
        // Get resources shared by other teachers (excluding current teacher)
        $sharedResources = Resource::where('teacher_id', '!=', $teacher->id)
            ->where('status', 'active')
            ->with(['teacher:id,name,email', 'class:id,name'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($resource) {
                $fileInfo = [];
                
                if ($resource->file_path) {
                    try {
                        $fileSize = Storage::disk('public')->size($resource->file_path);
                        $fileInfo['size'] = $this->formatFileSize($fileSize);
                    } catch (\Exception $e) {
                        $fileInfo['size'] = 'Unknown';
                    }
                }

                return [
                    'id' => $resource->id,
                    'title' => $resource->title,
                    'type' => $resource->type,
                    'description' => $resource->description,
                    'file_info' => $fileInfo,
                    'file_path' => $resource->file_path,
                    'thumbnail_path' => $resource->thumbnail_path,
                    'content' => $resource->content,
                    'download_count' => $resource->download_count ?? 0,
                    'created_at' => $resource->created_at->toISOString(),
                    'teacher' => $resource->teacher,
                    'class' => $resource->class ? $resource->class->name : 'General',
                ];
            });

        return Inertia::render('Teacher/Resources/Shared', [
            'user' => $teacher,
            'sharedResources' => $sharedResources,
        ]);
    }

    /**
     * Store a newly created resource (for the upload form)
     */
    public function storeResource(Request $request)
    {
        try {
            Log::info("📡 [RESOURCE STORE] Starting resource creation");
            Log::info("📡 [RESOURCE STORE] Request data:", $request->all());

            $teacherId = Auth::id();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'type' => 'required|in:video,pdf,document,link',
                'description' => 'nullable|string|max:1000',
                'assigned_class' => 'nullable|exists:classes,id',
                'content' => 'required_if:type,video,link|string|max:1000',
                'file' => 'required_if:type,pdf,document|file|max:512000', // 500MB
                'thumbnail' => 'nullable|image|max:2048'
            ], [
                'title.required' => 'The title field is required.',
                'type.required' => 'Please select a resource type.',
                'content.required_if' => 'Please provide a URL for video or link resources.',
                'file.required_if' => 'Please upload a file for PDF or document resources.',
                'file.max' => 'The file size must not exceed 500MB.',
                'assigned_class.exists' => 'The selected class does not exist.',
                'thumbnail.image' => 'The thumbnail must be an image.',
                'thumbnail.max' => 'The thumbnail size must not exceed 2MB.'
            ]);

            if ($validator->fails()) {
                Log::error("❌ [RESOURCE STORE] Validation failed:", $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Please check your input and try again.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            Log::info("✅ [RESOURCE STORE] Validation passed");

            $filePath = null;
            $thumbnailPath = null;
            $content = $request->content;

            // Handle file upload for PDF and document types
            if ($request->hasFile('file') && in_array($request->type, ['pdf', 'document'])) {
                try {
                    $file = $request->file('file');
                    $fileName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                    $filePath = $file->storeAs('resources', $fileName, 'public');
                    Log::info("✅ [RESOURCE STORE] File stored at: {$filePath}");
                } catch (\Exception $fileError) {
                    Log::error("❌ [RESOURCE STORE] File storage error: " . $fileError->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload file: ' . $fileError->getMessage()
                    ], 500);
                }
            }

            // Handle YouTube thumbnail for video links
            if ($request->type === 'video' && $this->isYouTubeUrl($content)) {
                $videoId = $this->getYouTubeVideoId($content);
                if ($videoId) {
                    $thumbnailPath = "youtube_{$videoId}";
                    Log::info("✅ [RESOURCE STORE] YouTube video detected, thumbnail: {$thumbnailPath}");
                }
            }

            // Handle custom thumbnail upload (optional)
            if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                try {
                    $thumbnail = $request->file('thumbnail');
                    $thumbnailName = 'thumb_' . time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $thumbnail->getClientOriginalName());
                    $thumbnailPath = $thumbnail->storeAs('thumbnails', $thumbnailName, 'public');
                    Log::info("✅ [RESOURCE STORE] Custom thumbnail stored at: {$thumbnailPath}");
                } catch (\Exception $thumbError) {
                    Log::error("❌ [RESOURCE STORE] Thumbnail storage error: " . $thumbError->getMessage());
                }
            }

            try {
                $resource = Resource::create([
                    'title' => $request->title,
                    'type' => $request->type,
                    'description' => $request->description,
                    'content' => $content,
                    'file_path' => $filePath,
                    'thumbnail_path' => $thumbnailPath,
                    'teacher_id' => $teacherId,
                    'class_id' => $request->assigned_class,
                    'status' => 'active',
                    'download_count' => 0
                ]);

                Log::info("✅ [RESOURCE STORE] Resource created successfully: ID {$resource->id}");

                return response()->json([
                    'success' => true,
                    'message' => 'Resource uploaded successfully',
                    'data' => [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'description' => $resource->description,
                        'file_path' => $resource->file_path,
                        'thumbnail_path' => $resource->thumbnail_path,
                        'content' => $resource->content,
                        'download_count' => $resource->download_count,
                        'created_at' => $resource->created_at
                    ]
                ]);

            } catch (\Exception $dbError) {
                Log::error("❌ [RESOURCE STORE] Database error: " . $dbError->getMessage());
                
                // Clean up uploaded files if any
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save resource: ' . $dbError->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('❌ [RESOURCE STORE] Unexpected error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Share a resource with other teachers
     */
    public function shareResource(Request $request, $resourceId)
    {
        try {
            $resource = Resource::where('id', $resourceId)
                ->where('teacher_id', Auth::id())
                ->firstOrFail();

            $resource->update([
                'is_shared' => true,
                'shared_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Resource shared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error sharing resource: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to share resource'
            ], 500);
        }
    }

    /**
     * Get shared resources for API
     */
    public function getSharedResources()
    {
        try {
            $teacherId = Auth::id();
            
            $sharedResources = Resource::where('teacher_id', '!=', $teacherId)
                ->where('status', 'active')
                ->where('is_shared', true)
                ->with(['teacher:id,name,email', 'class:id,name'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($resource) {
                    $fileInfo = [];
                    
                    if ($resource->file_path) {
                        try {
                            $fileSize = Storage::disk('public')->size($resource->file_path);
                            $fileInfo['size'] = $this->formatFileSize($fileSize);
                        } catch (\Exception $e) {
                            $fileInfo['size'] = 'Unknown';
                        }
                    }

                    return [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'description' => $resource->description,
                        'file_info' => $fileInfo,
                        'file_path' => $resource->file_path,
                        'thumbnail_path' => $resource->thumbnail_path,
                        'content' => $resource->content,
                        'download_count' => $resource->download_count ?? 0,
                        'created_at' => $resource->created_at->toISOString(),
                        'teacher' => $resource->teacher,
                        'class' => $resource->class ? $resource->class->name : 'General',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $sharedResources
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching shared resources: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch shared resources'
            ], 500);
        }
    }
}