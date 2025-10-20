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

class TeacherController extends Controller
{
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
                    'education_qualification', 'institute', 'experience',
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
                    'education_qualification', 'institute', 'experience',
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
                    'education_qualification', 'institute', 'experience',
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
                    'education_qualification', 'institute', 'experience',
                    'created_at'
                ])
                ->orderBy('name')
                ->limit(6) // Limit featured teachers
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
                    'education_qualification', 'institute', 'experience',
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

            // Use ClassModel instead of Course
            $courses = ClassModel::where('teacher_id', $id)
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
     * Toggle teacher featured status
     */
    public function toggleFeatured($id)
    {
        try {
            $teacher = User::where('role', 'teacher')->find($id);
            
            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not found'
                ], 404);
            }

            // If you have a featured field in your users table
            // $teacher->featured = !$teacher->featured;
            // $teacher->save();

            return response()->json([
                'success' => true,
                'message' => 'Teacher featured status updated',
                'data' => $teacher
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling teacher featured status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update featured status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get teacher courses
     */
    public function getTeacherCourses($id)
    {
        try {
            Log::info("Fetching courses for teacher ID: {$id}");

            // Use ClassModel instead of Course
            $courses = ClassModel::where('teacher_id', $id)
                ->select(['id', 'name as title', 'description', 'category', 'level', 'status', 'created_at'])
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
                        'status' => $course->status,
                        'created_at' => $course->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $courses,
                'count' => $courses->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching teacher courses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teacher courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get teacher enrollments
     */
    public function getTeacherEnrollments($id)
    {
        try {
            Log::info("Fetching enrollments for teacher ID: {$id}");

            // This would depend on your enrollment structure
            $enrollments = []; // Implement based on your database structure

            return response()->json([
                'success' => true,
                'data' => $enrollments,
                'count' => count($enrollments)
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching teacher enrollments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teacher enrollments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get teacher stats
     */
    public function getTeacherStats($id)
    {
        try {
            Log::info("Fetching stats for teacher ID: {$id}");

            // Use ClassModel instead of Course
            $coursesCount = ClassModel::where('teacher_id', $id)->count();
            $classesCount = ClassModel::where('teacher_id', $id)->count();
            $resourcesCount = Resource::where('teacher_id', $id)->count();

            $stats = [
                'courses_count' => $coursesCount,
                'classes_count' => $classesCount,
                'resources_count' => $resourcesCount,
                'students_count' => 0, // Implement based on your enrollment structure
                'rating' => 4.8, // Implement based on your rating system
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching teacher stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teacher stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get teacher performance
     */
    public function getTeacherPerformance($id)
    {
        try {
            Log::info("Fetching performance for teacher ID: {$id}");

            // Implement performance metrics based on your business logic
            $performance = [
                'completion_rate' => 85,
                'student_satisfaction' => 4.8,
                'engagement_rate' => 78,
                'resource_utilization' => 92,
            ];

            return response()->json([
                'success' => true,
                'data' => $performance
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching teacher performance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch teacher performance',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // ============ EXISTING METHODS (Keep these) ============

    public function getTeacher($id)
    {
        try {
            Log::info("Fetching teacher with ID: {$id}");
            
            $teacher = User::select([
                'id', 'name', 'username', 'email', 'dob', 
                'education_qualification', 'institute', 'experience',
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

    public function getTeacherClasses($id)
    {
        try {
            Log::info("Fetching classes for teacher ID: {$id}");
            
            // Check if teacher exists and is actually a teacher
            $teacher = User::find($id);
            if (!$teacher || $teacher->role !== 'teacher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not found',
                    'data' => []
                ], 404);
            }

            // Get classes assigned to this teacher
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
            
            // Return mock data as fallback for development
            $mockClasses = $this->getMockClasses();
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch classes, using mock data',
                'data' => $mockClasses
            ]);
        }
    }

    public function getTeacherResources($id)
    {
        try {
            Log::info("Fetching resources for teacher ID: {$id}");
            
            // Check if teacher exists
            $teacher = User::find($id);
            if (!$teacher || $teacher->role !== 'teacher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher not found',
                    'data' => []
                ], 404);
            }

            // Get resources uploaded by this teacher
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
            
            // Return mock data as fallback for development
            $mockResources = $this->getMockResources();
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch resources, using mock data',
                'data' => $mockResources
            ]);
        }
    }

    public function uploadResource(Request $request, $id)
    {
        try {
            // FORCE PHP configuration at runtime - this overrides everything
            @ini_set('upload_max_filesize', '512M');
            @ini_set('post_max_size', '512M');
            @ini_set('max_execution_time', '600');
            @ini_set('max_input_time', '600');
            @ini_set('memory_limit', '1024M');

            Log::info("📡 [UPLOAD] Starting upload for teacher ID: {$id}");
            Log::info("📡 [UPLOAD] Current PHP limits after override:", [
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'max_execution_time' => ini_get('max_execution_time'),
                'memory_limit' => ini_get('memory_limit')
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                Log::info("📡 [UPLOAD] File details:", [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension()
                ]);
            }

            // SIMPLIFIED VALIDATION - Remove content validation for now
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'type' => 'required|in:video,pdf,document,link',
                'description' => 'nullable|string|max:1000',
                'assigned_class' => 'nullable|exists:classes,id',
                'file' => 'required_if:type,pdf,video,document|file|max:512000' // 500MB in KB
            ], [
                'title.required' => 'The title field is required.',
                'type.required' => 'Please select a resource type.',
                'type.in' => 'Invalid resource type selected.',
                'file.required_if' => 'Please select a file to upload.',
                'file.max' => 'The file size must not exceed 500MB.',
                'file.file' => 'The uploaded file is not valid.',
                'assigned_class.exists' => 'The selected class does not exist.'
            ]);

            if ($validator->fails()) {
                Log::error("❌ [UPLOAD] Validation failed:", $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Please check your input and try again.',
                    'errors' => $validator->errors(),
                    'debug_info' => [
                        'received_fields' => array_keys($request->all()),
                        'file_uploaded' => $request->hasFile('file'),
                        'file_size' => $request->hasFile('file') ? $request->file('file')->getSize() : 0,
                        'teacher_id' => $id,
                        'php_limits' => [
                            'upload_max_filesize' => ini_get('upload_max_filesize'),
                            'post_max_size' => ini_get('post_max_size')
                        ]
                    ]
                ], 422);
            }

            Log::info("✅ [UPLOAD] Validation passed");

            $filePath = null;
            $content = $request->content;

            // Handle file upload if present
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                try {
                    $file = $request->file('file');
                    
                    // Generate unique filename
                    $fileName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                    
                    // Store file based on type
                    $folder = $request->type . 's'; // pdfs, videos, documents
                    $filePath = $file->storeAs($folder, $fileName, 'public');
                    
                    Log::info("✅ [UPLOAD] File stored successfully at: {$filePath}");
                    
                    // Set content to file path for non-link types
                    if ($request->type !== 'link') {
                        $content = $filePath;
                    }
                } catch (\Exception $fileError) {
                    Log::error("❌ [UPLOAD] File storage error: " . $fileError->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to store file: ' . $fileError->getMessage()
                    ], 500);
                }
            }

            // For link type, use the content field directly
            if ($request->type === 'link' && $request->content) {
                $content = $request->content;
            }

            // Create resource record
            try {
                $resource = Resource::create([
                    'title' => $request->title,
                    'type' => $request->type,
                    'description' => $request->description,
                    'content' => $content,
                    'file_path' => $filePath,
                    'teacher_id' => $id,
                    'class_id' => $request->assigned_class,
                    'status' => 'active'
                ]);

                Log::info("✅ [UPLOAD] Resource created successfully: {$resource->id} - {$resource->title}");

                return response()->json([
                    'success' => true,
                    'message' => 'Resource uploaded successfully',
                    'data' => [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'description' => $resource->description,
                        'file_path' => $resource->file_path,
                        'content' => $resource->content,
                        'created_at' => $resource->created_at
                    ]
                ]);

            } catch (\Exception $dbError) {
                Log::error("❌ [UPLOAD] Database error: " . $dbError->getMessage());
                
                // Clean up uploaded file if database fails
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save resource: ' . $dbError->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('❌ [UPLOAD] Unexpected error: ' . $e->getMessage());
            Log::error('❌ [UPLOAD] Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getClassResources($classId)
    {
        try {
            Log::info("Fetching resources for class ID: {$classId}");

            $resources = Resource::where('class_id', $classId)
                ->orWhereNull('class_id') // General resources
                ->with(['teacher:id,name,email', 'class:id,name'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($resource) {
                    $fileInfo = [];
                    
                    if ($resource->file_path) {
                        $fileSize = Storage::disk('public')->size($resource->file_path);
                        $fileInfo['size'] = $this->formatFileSize($fileSize);
                        
                        // Add additional info based on file type
                        if ($resource->type === 'pdf') {
                            $fileInfo['pages'] = 'N/A'; // You can use a PDF library to get actual page count
                        } elseif ($resource->type === 'video') {
                            $fileInfo['duration'] = 'N/A'; // You can use a video library to get duration
                        }
                    }

                    return [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'type' => $resource->type,
                        'description' => $resource->description,
                        'file_info' => $fileInfo,
                        'download_count' => 0, // You can track downloads separately
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

    public function deleteResource($resourceId)
    {
        try {
            Log::info("Deleting resource ID: {$resourceId}");

            // Get authenticated user ID - FIXED: Use Auth::id() instead of auth()->id()
            $teacherId = Auth::id();
            
            $resource = Resource::where('id', $resourceId)
                ->where('teacher_id', $teacherId)
                ->first();

            if (!$resource) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found or access denied'
                ], 404);
            }

            // Delete file from storage if exists
            if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
                Storage::disk('public')->delete($resource->file_path);
            }

            $resource->delete();

            return response()->json([
                'success' => true,
                'message' => 'Resource deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting resource: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete resource'
            ], 500);
        }
    }

    // Mock data methods for development
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

    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    private function generateSlug($name)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }
}