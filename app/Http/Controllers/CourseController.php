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

class CourseController extends Controller
{
    // Get all classes (grades 1-12 and other courses)
    public function getClasses()
    {
        try {
            // Check if classes table exists and has data
            if (!Schema::hasTable('classes')) {
                return $this->getMockClasses();
            }

            // Get all classes from database
            $databaseClasses = ClassModel::select('id', 'name', 'subject', 'grade', 'teacher_id', 'status', 'type', 'category', 'capacity')
                ->with(['teacher:id,name,email', 'students'])
                ->get();

            // If no classes in database, return mock data
            if ($databaseClasses->isEmpty()) {
                return $this->getMockClasses();
            }

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
                    'description' => $firstClass->description
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
                    'description' => $course->description
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedClasses,
                'source' => 'database',
                'total_classes' => count($formattedClasses),
                'regular_count' => $groupedRegularClasses->count(),
                'other_count' => $otherCourses->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching classes: ' . $e->getMessage());
            // Return mock data if there's an error
            return $this->getMockClasses();
        }
    }

    // Mock data for development
    private function getMockClasses()
    {
        $mockClasses = [];
        $subjects = ['Mathematics', 'Science', 'English', 'Social Studies', 'Computer Science', 'Physical Education'];
        
        // Regular classes 1-12
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
                'capacity' => 30
            ];
        }

        // Mock other courses
        $otherCourses = [
            [
                'id' => 13,
                'name' => 'Life Skills',
                'category' => 'Life Skills',
                'studentCount' => 25,
                'teachers' => [],
                'status' => 'active',
                'type' => 'other',
                'capacity' => 30
            ],
            [
                'id' => 14,
                'name' => 'Spoken English',
                'category' => 'Spoken English',
                'studentCount' => 30,
                'teachers' => [],
                'status' => 'active',
                'type' => 'other',
                'capacity' => 25
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

    // Get subjects for a specific class/grade
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
                ->select('id', 'subject', 'teacher_id', 'description', 'name', 'grade', 'type')
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

    // Helper method to generate subject codes
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
                'assignedTeachers' => []
            ];
        }, $subjects);

        return response()->json([
            'success' => true,
            'data' => $mockSubjects,
            'source' => 'mock'
        ]);
    }

    // Get teachers for a specific subject
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
                        'className' => $class->name
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

    // Assign teacher to a subject/class
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
                        'className' => $class->name
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

    // Remove teacher from subject
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

    // Get enrollments for a class
    public function getClassEnrollments($grade)
    {
        try {
            $className = "Class {$grade}";
            
            $enrollments = ClassModel::where('name', $className)
                ->with(['students.user:id,name,email', 'teacher:id,name,email'])
                ->get()
                ->map(function($class) {
                    return [
                        'id' => $class->id,
                        'subject' => $class->subject,
                        'teacher' => $class->teacher ? [
                            'id' => $class->teacher->id,
                            'name' => $class->teacher->name,
                            'email' => $class->teacher->email
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
                        }),
                        'totalStudents' => $class->students->count()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'grade' => $grade,
                    'className' => $className,
                    'subjects' => $enrollments
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching enrollments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch enrollments'
            ], 500);
        }
    }

    // Get course categories
    public function getCourseCategories()
    {
        try {
            // Simple implementation that works with your existing structure
            $categories = [
                [
                    'id' => 'primary',
                    'name' => 'Primary',
                    'grades' => 'Class 1-5',
                    'description' => 'Elementary education foundation',
                    'classCount' => 5,
                    'studentCount' => $this->getStudentCountForGrades([1, 2, 3, 4, 5])
                ],
                [
                    'id' => 'junior',
                    'name' => 'Junior',
                    'grades' => 'Class 6-8',
                    'description' => 'Middle school education',
                    'classCount' => 3,
                    'studentCount' => $this->getStudentCountForGrades([6, 7, 8])
                ],
                [
                    'id' => 'secondary',
                    'name' => 'Secondary',
                    'grades' => 'Class 9-10',
                    'description' => 'High school preparation',
                    'classCount' => 2,
                    'studentCount' => $this->getStudentCountForGrades([9, 10])
                ],
                [
                    'id' => 'higher-secondary',
                    'name' => 'Higher Secondary',
                    'grades' => 'Class 11-12',
                    'description' => 'College preparation',
                    'classCount' => 2,
                    'studentCount' => $this->getStudentCountForGrades([11, 12])
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            
            // Fallback to mock data with proper structure
            return response()->json([
                'success' => true,
                'data' => $this->getMockCategories(),
                'message' => 'Using demonstration data - database query failed'
            ]);
        }
    }

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

// Mock categories data
    private function getMockCategories()
    {
        return [
            [
                'id' => 'primary',
                'name' => 'Primary',
                'grades' => 'Class 1-5',
                'description' => 'Elementary education foundation',
                'classCount' => 5,
                'studentCount' => 250
            ],
            [
                'id' => 'junior',
                'name' => 'Junior',
                'grades' => 'Class 6-8',
                'description' => 'Middle school education',
                'classCount' => 3,
                'studentCount' => 180
            ],
            [
                'id' => 'secondary',
                'name' => 'Secondary',
                'grades' => 'Class 9-10',
                'description' => 'High school preparation',
                'classCount' => 2,
                'studentCount' => 120
            ],
            [
                'id' => 'higher-secondary',
                'name' => 'Higher Secondary',
                'grades' => 'Class 11-12',
                'description' => 'College preparation',
                'classCount' => 2,
                'studentCount' => 100
            ]
        ];
    }
    // Get classes by category
    public function getCategoryClasses($category)
    {
        try {
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

            for ($grade = $start; $grade <= $end; $grade++) {
                $className = "Class {$grade}";
                $classData = ClassModel::where('name', $className)->get();
                
                $classes[] = [
                    'id' => $grade,
                    'grade' => $grade,
                    'name' => $className,
                    'subjectCount' => $classData->unique('subject')->count(),
                    'studentCount' => $classData->sum(function($class) {
                        return $class->students->count();
                    })
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $classes
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching category classes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch classes'
            ], 500);
        }
    }

    // For regular academic classes (Class 1-12)
    public function getAcademicClasses()
    {
        try {
            $classes = ClassModel::where('type', 'regular')
                ->whereNotNull('grade')
                ->select('id', 'name', 'grade', 'subject', 'teacher_id', 'status')
                ->with(['teacher:id,name', 'students'])
                ->get()
                ->groupBy('grade')
                ->map(function($gradeClasses, $grade) {
                    $firstClass = $gradeClasses->first();
                    return [
                        'id' => $grade,
                        'grade' => $grade,
                        'name' => "Class {$grade}",
                        'subjectCount' => $gradeClasses->unique('subject')->count(),
                        'studentCount' => $gradeClasses->sum(function($class) {
                            return $class->students->count();
                        }),
                        'type' => 'academic'
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'data' => $classes,
                'type' => 'academic'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching academic classes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch academic classes'
            ], 500);
        }
    }

    // For other courses (Career Counseling, Dance Class, etc.)
    public function getOtherCourses()
    {
        try {
            $courses = ClassModel::where('type', 'other')
                ->select('id', 'name', 'category', 'subject', 'capacity', 'status', 'description')
                ->with(['teacher:id,name', 'students'])
                ->get()
                ->map(function($course) {
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                        'category' => $course->category,
                        'subject' => $course->subject,
                        'studentCount' => $course->students->count(),
                        'capacity' => $course->capacity,
                        'status' => $course->status,
                        'description' => $course->description,
                        'type' => 'other'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $courses,
                'type' => 'other_courses'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching other courses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch other courses'
            ], 500);
        }
    }

    // Get all classes combined (academic + other)
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

    // Get a specific course by ID - UPDATED to handle both regular and other courses
    /**
 * Get a specific course by ID
 */
    public function getCourse($courseId)
    {
        try {
            Log::info("Fetching course with ID: {$courseId}");

            $course = ClassModel::with(['teacher:id,name,email,experience,education_qualification', 'students'])
                ->find($courseId);

            if (!$course) {
                Log::warning("Course not found with ID: {$courseId}");
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            Log::info("Found course: {$course->name} (ID: {$course->id})");

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
            Log::error('Error fetching course: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch course: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get course details specifically for "other" type courses
    public function getOtherCourseDetails($courseId)
    {
        try {
            $course = ClassModel::where('type', 'other')
                ->with(['teacher:id,name,email,experience,education_qualification', 'students'])
                ->find($courseId);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

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
                ]] : []
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedCourse
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching course details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch course details'
            ], 500);
        }
    }

    // Update a course
    public function updateCourse(Request $request, $courseId)
    {
        try {
            $course = ClassModel::find($courseId);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'subject' => 'sometimes|string|max:255',
                'grade' => 'sometimes|integer|between:1,12',
                'capacity' => 'sometimes|integer|min:1|max:100',
                'status' => 'sometimes|in:active,inactive,upcoming',
                'description' => 'nullable|string',
                'teacher_id' => 'nullable|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $course->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Course updated successfully',
                'data' => $course
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating course: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update course'
            ], 500);
        }
    }

    // Delete a course
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
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:regular,other',
                'grade' => 'required_if:type,regular|integer|between:1,12',
                'name' => 'required|string|max:255',
                'category' => 'required_if:type,other|string|max:255',
                'courseName' => 'required_if:type,other|string|max:255',
                'courseCode' => 'required_if:type,other|string|max:50',
                'subjects' => 'required_if:type,regular|array',
                'subjects.*.name' => 'required_if:type,regular|string|max:255',
                'subjects.*.code' => 'required_if:type,regular|string|max:10',
                'capacity' => 'nullable|integer|min:1|max:100',
                'status' => 'required|in:active,inactive,upcoming',
                'description' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // For regular classes, create multiple class records for each subject
            if ($request->type === 'regular') {
                $createdClasses = [];
                
                foreach ($request->subjects as $subject) {
                    $class = ClassModel::create([
                        'name' => $request->name,
                        'subject' => $subject['name'],
                        'grade' => $request->grade,
                        'code' => $subject['code'],
                        'description' => $request->description,
                        'capacity' => $request->capacity ?? 30,
                        'status' => $request->status,
                        'type' => 'regular'
                    ]);
                    
                    $createdClasses[] = $class;
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Class created successfully with ' . count($createdClasses) . ' subjects',
                    'data' => [
                        'id' => $createdClasses[0]->id,
                        'grade' => $request->grade,
                        'name' => $request->name,
                        'subjectCount' => count($createdClasses),
                        'studentCount' => 0,
                        'teachers' => [],
                        'status' => $request->status,
                        'type' => 'regular'
                    ]
                ], 201);

            } else {
                // For other courses, create a single course record
                $class = ClassModel::create([
                    'name' => $request->courseName,
                    'subject' => $request->category, // Using subject column for category
                    'code' => $request->courseCode,
                    'description' => $request->description,
                    'capacity' => $request->capacity ?? 30,
                    'status' => $request->status,
                    'type' => 'other',
                    'category' => $request->category
                ]);

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
                        'type' => 'other'
                    ]
                ], 201);
            }

        } catch (\Exception $e) {
            Log::error('Error creating course: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create course',
                'error' => $e->getMessage()
            ], 500);
        }
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
                        'type' => $course->type
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

    public function enrollStudent(Request $request, $courseId)
    {
        try {
            $user = $request->user();
            $course = ClassModel::find($courseId);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            // Check if already enrolled
            if ($course->students()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already enrolled in this course'
                ], 400);
            }

            // Enroll student
            $course->students()->attach($user->id, [
                'enrolled_at' => now(),
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully enrolled in course',
                'data' => [
                    'course_id' => $course->id,
                    'course_name' => $course->name,
                    'enrolled_at' => now()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enroll in course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMyCourses(Request $request)
    {
        try {
            $user = $request->user();
            
            $courses = $user->studentClasses()
                ->with(['teacher:id,name,avatar', 'students'])
                ->get()
                ->map(function($course) {
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                        'subject' => $course->subject,
                        'type' => $course->type,
                        'description' => $course->description,
                        'thumbnail' => $course->thumbnail,
                        'teacher' => $course->teacher,
                        'enrolled_at' => $course->pivot->enrolled_at,
                        'progress' => 0, // You can implement progress tracking
                        'slug' => $this->generateSlug($course->name)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $courses,
                'message' => 'My courses retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function unenrollStudent(Request $request, $courseId)
    {
        try {
            $user = $request->user();
            $course = ClassModel::find($courseId);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            $course->students()->detach($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Successfully unenrolled from course'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unenroll from course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function generateSlug($name)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    public function getCourseSubjects($courseId)
    {
        try {
            Log::info("🔍 [DEBUG] Fetching subjects for course ID: {$courseId}");

            // First, get the main course
            $course = ClassModel::find($courseId);
            
            if (!$course) {
                Log::warning("❌ [DEBUG] Course not found with ID: {$courseId}");
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            Log::info("✅ [DEBUG] Found course: {$course->name} (Type: {$course->type})");

            $subjects = [];

            if ($course->type === 'regular') {
                // For regular classes, get all subjects for this grade/class
                $subjects = $this->getRegularCourseSubjects($course);
            } else {
                // For other courses, get the course itself as a subject
                $subjects = $this->getOtherCourseSubjects($course);
            }

            Log::info("✅ [DEBUG] Returning {$subjects->count()} subjects for course");

            return response()->json([
                'success' => true,
                'data' => $subjects,
                'course' => [
                    'id' => $course->id,
                    'name' => $course->name,
                    'type' => $course->type,
                    'grade' => $course->grade
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ [DEBUG] Error fetching course subjects: ' . $e->getMessage());
            Log::error('❌ [DEBUG] Stack trace: ' . $e->getTraceAsString());
            
            // Return mock data as fallback
            return $this->getMockCourseSubjects($courseId);
        }
    }


    private function getRegularCourseSubjects($course)
    {
        Log::info("📚 [DEBUG] Getting regular course subjects for: {$course->name}");

        // Get all classes with the same name (for different subjects)
        $subjectClasses = ClassModel::where('name', $course->name)
            ->where('type', 'regular')
            ->with(['teacher:id,name,email,education_qualification,experience,avatar', 'students'])
            ->get();

        Log::info("📚 [DEBUG] Found {$subjectClasses->count()} subject classes");

        if ($subjectClasses->isEmpty()) {
            Log::info("📚 [DEBUG] No subject classes found, using mock data");
            return $this->getMockSubjectsForRegularCourse($course);
        }

        return $subjectClasses->map(function($class) {
            return [
                'id' => $class->id,
                'name' => $class->subject ?: $class->name,
                'description' => $class->description ?: $this->getSubjectDescription($class->subject ?: $class->name),
                'lesson_count' => $class->students->count() > 0 ? rand(10, 20) : rand(5, 15),
                'duration' => $this->generateRandomDuration(),
                'student_count' => $class->students->count(),
                'teacher' => $class->teacher ? [
                    'id' => $class->teacher->id,
                    'name' => $class->teacher->name,
                    'email' => $class->teacher->email,
                    'qualification' => $class->teacher->education_qualification,
                    'experience' => $class->teacher->experience,
                    'rating' => $this->generateTeacherRating(),
                    'avatar' => $class->teacher->avatar ?: $this->getDefaultTeacherAvatar($class->teacher->name)
                ] : $this->getMockTeacher($class->subject ?: $class->name)
            ];
        });
    }

    private function getOtherCourseSubjects($course)
    {
        Log::info("🎯 [DEBUG] Getting other course subjects for: {$course->name}");

        // For other courses, we might have multiple modules/subjects
        // For now, return the course itself as the main subject
        $subjects = collect([$course]);

        // If it's a comprehensive course, break it down into modules
        if (str_contains(strtolower($course->name), 'programming') || 
            str_contains(strtolower($course->name), 'development')) {
            $subjects = $this->getProgrammingCourseSubjects($course);
        } elseif (str_contains(strtolower($course->name), 'design')) {
            $subjects = $this->getDesignCourseSubjects($course);
        } elseif (str_contains(strtolower($course->name), 'language') || 
                str_contains(strtolower($course->name), 'english')) {
            $subjects = $this->getLanguageCourseSubjects($course);
        }

        return $subjects->map(function($subject) use ($course) {
            return [
                'id' => $subject->id ?? ($course->id + rand(100, 999)),
                'name' => $subject->name ?? $subject->subject ?? $course->name,
                'description' => $subject->description ?? $this->getSubjectDescription($subject->name ?? $course->name),
                'lesson_count' => rand(8, 25),
                'duration' => $this->generateRandomDuration(),
                'student_count' => $subject->students->count() ?? rand(15, 40),
                'teacher' => $subject->teacher ?? $course->teacher ? [
                    'id' => $course->teacher->id,
                    'name' => $course->teacher->name,
                    'email' => $course->teacher->email,
                    'qualification' => $course->teacher->education_qualification,
                    'experience' => $course->teacher->experience,
                    'rating' => $this->generateTeacherRating(),
                    'avatar' => $course->teacher->avatar ?: $this->getDefaultTeacherAvatar($course->teacher->name)
                ] : $this->getMockTeacher($subject->name ?? $course->name)
            ];
        });
    }

    private function getProgrammingCourseSubjects($course)
    {
        $subjects = [
            ['name' => 'HTML & CSS Fundamentals', 'description' => 'Learn the building blocks of web development'],
            ['name' => 'JavaScript Programming', 'description' => 'Master dynamic web interactions and logic'],
            ['name' => 'React.js Development', 'description' => 'Build modern user interfaces with React'],
            ['name' => 'Node.js Backend', 'description' => 'Create server-side applications with Node.js'],
            ['name' => 'Database Management', 'description' => 'Learn SQL and database design principles'],
            ['name' => 'Project Development', 'description' => 'Build real-world applications']
        ];

        return collect($subjects)->map(function($subject, $index) use ($course) {
            $class = clone $course;
            $class->id = $course->id + $index + 1;
            $class->name = $subject['name'];
            $class->description = $subject['description'];
            return $class;
        });
    }

    private function getDesignCourseSubjects($course)
{
    $subjects = [
        ['name' => 'UI/UX Design Principles', 'description' => 'Learn user-centered design approaches'],
        ['name' => 'Adobe Photoshop', 'description' => 'Master image editing and graphic design'],
        ['name' => 'Figma Prototyping', 'description' => 'Create interactive prototypes with Figma'],
        ['name' => 'Color Theory & Typography', 'description' => 'Understand visual design fundamentals'],
        ['name' => 'Design Portfolio', 'description' => 'Build a professional design portfolio']
    ];

    return collect($subjects)->map(function($subject, $index) use ($course) {
        $class = clone $course;
        $class->id = $course->id + $index + 1;
        $class->name = $subject['name'];
        $class->description = $subject['description'];
        return $class;
    });
}

/**
 * Generate mock subjects for language courses
 */
    private function getLanguageCourseSubjects($course)
    {
        $subjects = [
            ['name' => 'Grammar Fundamentals', 'description' => 'Master essential grammar rules'],
            ['name' => 'Vocabulary Building', 'description' => 'Expand your word knowledge'],
            ['name' => 'Speaking Practice', 'description' => 'Improve pronunciation and fluency'],
            ['name' => 'Listening Comprehension', 'description' => 'Understand native speakers'],
            ['name' => 'Writing Skills', 'description' => 'Develop effective writing techniques'],
            ['name' => 'Reading Comprehension', 'description' => 'Understand complex texts']
        ];

        return collect($subjects)->map(function($subject, $index) use ($course) {
            $class = clone $course;
            $class->id = $course->id + $index + 1;
            $class->name = $subject['name'];
            $class->description = $subject['description'];
            return $class;
        });
    }

    private function getMockSubjectsForRegularCourse($course)
{
    $commonSubjects = [
        'Mathematics', 'English', 'Science', 'Social Studies', 
        'Bengali', 'Computer Science', 'Physical Education'
    ];

    return collect($commonSubjects)->map(function($subject, $index) use ($course) {
        return [
            'id' => $course->id + $index + 1,
            'name' => $subject,
            'description' => $this->getSubjectDescription($subject),
            'lesson_count' => rand(12, 24),
            'duration' => $this->generateRandomDuration(),
            'student_count' => rand(20, 45),
            'teacher' => $this->getMockTeacher($subject)
        ];
    });
}

/**
 * Generate mock course subjects as fallback
 */
    private function getMockCourseSubjects($courseId)
    {
        Log::info("🎭 [DEBUG] Using mock subjects for course ID: {$courseId}");

        $mockSubjects = [
            [
                'id' => $courseId + 1,
                'name' => 'Mathematics',
                'description' => 'Develop problem-solving skills and mathematical thinking',
                'lesson_count' => 15,
                'duration' => '12h 30m',
                'student_count' => 35,
                'teacher' => [
                    'id' => 1,
                    'name' => 'Dr. Ahmed Rahman',
                    'email' => 'ahmed.rahman@school.edu',
                    'qualification' => 'PhD in Mathematics',
                    'experience' => '10+ years',
                    'rating' => '4.9',
                    'avatar' => '/assets/img/teachers/teacher1.jpg'
                ]
            ],
            [
                'id' => $courseId + 2,
                'name' => 'Science',
                'description' => 'Explore scientific concepts and experimental methods',
                'lesson_count' => 12,
                'duration' => '10h 45m',
                'student_count' => 28,
                'teacher' => [
                    'id' => 2,
                    'name' => 'Ms. Fatima Begum',
                    'email' => 'fatima.begum@school.edu',
                    'qualification' => 'MSc in Physics',
                    'experience' => '8+ years',
                    'rating' => '4.7',
                    'avatar' => '/assets/img/teachers/teacher2.jpg'
                ]
            ],
            [
                'id' => $courseId + 3,
                'name' => 'English',
                'description' => 'Improve language proficiency and communication skills',
                'lesson_count' => 18,
                'duration' => '15h 20m',
                'student_count' => 42,
                'teacher' => [
                    'id' => 3,
                    'name' => 'Mr. Kabir Hossain',
                    'email' => 'kabir.hossain@school.edu',
                    'qualification' => 'MA in English Literature',
                    'experience' => '12+ years',
                    'rating' => '4.8',
                    'avatar' => '/assets/img/teachers/teacher3.jpg'
                ]
            ]
        ];

        return collect($mockSubjects);
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

    private function generateRandomDuration()
    {
        $hours = rand(5, 20);
        $minutes = rand(0, 59);
        return "{$hours}h {$minutes}m";
    }

    private function generateTeacherRating()
    {
        return number_format(4.0 + (rand(0, 10) / 10), 1);
    }

    private function getMockTeacher($subjectName)
    {
        $teachers = [
            'Mathematics' => [
                'id' => 1,
                'name' => 'Dr. Ahmed Rahman',
                'email' => 'ahmed.rahman@school.edu',
                'qualification' => 'PhD in Mathematics',
                'experience' => '10+ years',
                'rating' => '4.9',
                'avatar' => '/assets/img/teachers/teacher1.jpg'
            ],
            'Science' => [
                'id' => 2,
                'name' => 'Ms. Fatima Begum',
                'email' => 'fatima.begum@school.edu',
                'qualification' => 'MSc in Physics',
                'experience' => '8+ years',
                'rating' => '4.7',
                'avatar' => '/assets/img/teachers/teacher2.jpg'
            ],
            'English' => [
                'id' => 3,
                'name' => 'Mr. Kabir Hossain',
                'email' => 'kabir.hossain@school.edu',
                'qualification' => 'MA in English Literature',
                'experience' => '12+ years',
                'rating' => '4.8',
                'avatar' => '/assets/img/teachers/teacher3.jpg'
            ],
            'default' => [
                'id' => 4,
                'name' => 'Expert Teacher',
                'email' => 'teacher@school.edu',
                'qualification' => 'Subject Expert',
                'experience' => '5+ years',
                'rating' => '4.5',
                'avatar' => '/assets/img/teachers/default-teacher.jpg'
            ]
        ];

        foreach ($teachers as $key => $teacher) {
            if (str_contains(strtolower($subjectName), strtolower($key))) {
                return $teacher;
            }
        }

        return $teachers['default'];
    }

    private function getDefaultTeacherAvatar($teacherName)
    {
        // You can implement a logic to generate avatars based on teacher name
        return '/assets/img/teachers/default-teacher.jpg';
    }
}