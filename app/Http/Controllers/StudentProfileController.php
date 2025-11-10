<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\CourseProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        
        Log::info('=== STUDENT PROFILE SHOW METHOD START ===');
        Log::info('User ID: ' . $user->id);

        // Get student record with fresh data
        $student = Student::with(['class'])
            ->where('user_id', $user->id)
            ->first();

        Log::info('Student found: ' . ($student ? 'YES' : 'NO'));
        
        if ($student) {
            Log::info('Student profile_picture: ' . $student->profile_picture);
            Log::info('Student data: ', $student->toArray());
        }

        // If student record doesn't exist, create a basic one
        if (!$student) {
            Log::info('Creating new student record');
            $student = Student::create([
                'user_id' => $user->id,
                'class_id' => 1,
                'roll_number' => 'STU' . $user->id . time(),
                'father_name' => 'Not set',
                'mother_name' => 'Not set',
                'parent_contact' => null,
                'address' => null,
                'admission_date' => now(),
                'status' => 'active'
            ]);
            
            // Reload to get relationships
            $student = Student::with(['class'])->find($student->id);
        }

        // Get real statistics using enrollments
        $coursesEnrolled = Enrollment::where('user_id', $user->id)->count();
        $coursesCompleted = Enrollment::where('user_id', $user->id)
            ->where('progress', '>=', 100)
            ->count();
        
        // Calculate learning hours
        $learningHours = $this->calculateLearningHours($user->id);
        
        // Calculate current streak
        $currentStreak = $this->calculateCurrentStreak($user->id);
        
        // Get recent activity
        $recentActivity = $this->getRecentActivity($user->id);

        // Safely format admission_date
        $admissionDate = null;
        if ($student->admission_date) {
            try {
                if ($student->admission_date instanceof \Carbon\Carbon) {
                    $admissionDate = $student->admission_date->format('M d, Y');
                } else {
                    $admissionDate = Carbon::parse($student->admission_date)->format('M d, Y');
                }
            } catch (\Exception $e) {
                $admissionDate = $user->created_at->format('M d, Y');
            }
        } else {
            $admissionDate = $user->created_at->format('M d, Y');
        }

        // Build avatar URL - IMPORTANT: Use the accessor
        $avatarUrl = $student->profile_picture_url;
        
        Log::info('Avatar URL constructed: ' . $avatarUrl);
        Log::info('Profile picture from DB: ' . $student->profile_picture);

        $profileData = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $student->phone ?? $user->phone ?? '+8801XXXXXXXXX',
                'avatar' => $avatarUrl, // This should be the full URL
                'joined_date' => $user->created_at->format('M d, Y'),
                'bio' => $student->bio,
                'location' => $student->location,
            ],
            'stats' => [
                'courses_enrolled' => $coursesEnrolled,
                'courses_completed' => $coursesCompleted,
                'learning_hours' => $learningHours,
                'current_streak' => $currentStreak,
            ],
            'recent_activity' => $recentActivity,
            'student_info' => [
                'roll_number' => $student->roll_number,
                'class' => $student->class ? $student->class->name : 'Not assigned',
                'admission_date' => $admissionDate,
                'father_name' => $student->father_name,
                'mother_name' => $student->mother_name,
                'parent_contact' => $student->full_parent_contact,
                'address' => $student->address,
                'profile_picture' => $student->profile_picture, // Raw filename for debugging
                'profile_picture_url' => $avatarUrl, // Full URL for debugging
            ]
        ];

        Log::info('Final profile data sent to frontend:');
        Log::info('Avatar in user section: ' . $profileData['user']['avatar']);
        Log::info('Profile picture in student_info: ' . $profileData['student_info']['profile_picture']);
        Log::info('=== STUDENT PROFILE SHOW METHOD END ===');

        return Inertia::render('StudentProfile/Index', [
            'profile' => $profileData
        ]);
    }

    private function calculateLearningHours($userId)
    {
        $totalMinutes = CourseProgress::where('user_id', $userId)
            ->sum('time_spent_minutes');
            
        return round($totalMinutes / 60, 1);
    }

    private function calculateCurrentStreak($userId)
    {
        $today = Carbon::today();
        $streak = 0;
        
        // Check if user had activity today
        $hasActivityToday = CourseProgress::where('user_id', $userId)
            ->whereDate('accessed_at', $today)
            ->exists();
            
        if (!$hasActivityToday) {
            return 0;
        }
        
        $streak = 1;
        $checkDate = $today->copy()->subDay();
        
        // Check consecutive days with activity
        for ($i = 0; $i < 365; $i++) {
            $hasActivity = CourseProgress::where('user_id', $userId)
                ->whereDate('accessed_at', $checkDate)
                ->exists();
                
            if ($hasActivity) {
                $streak++;
                $checkDate->subDay();
            } else {
                break;
            }
        }
        
        return $streak;
    }

    private function getRecentActivity($userId, $limit = 3)
    {
        $activities = Enrollment::with(['class' => function($query) {
                $query->select('id', 'name', 'subject');
            }])
            ->where('user_id', $userId)
            ->where('progress', '>', 0)
            ->whereNotNull('last_accessed')
            ->orderBy('last_accessed', 'desc')
            ->limit($limit)
            ->get();

        if ($activities->isEmpty()) {
            return [];
        }

        return $activities->map(function ($enrollment) {
            $lastAccessed = 'Never';
            if ($enrollment->last_accessed) {
                try {
                    if ($enrollment->last_accessed instanceof \Carbon\Carbon) {
                        $lastAccessed = $enrollment->last_accessed->diffForHumans();
                    } else {
                        $lastAccessed = Carbon::parse($enrollment->last_accessed)->diffForHumans();
                    }
                } catch (\Exception $e) {
                    $lastAccessed = 'Recently';
                }
            }

            return [
                'course' => $enrollment->class->name ?? 'Unknown Course',
                'progress' => $enrollment->progress,
                'last_accessed' => $lastAccessed,
                'type' => $this->getActivityType($enrollment->last_activity_type)
            ];
        })->toArray();
    }

    private function getActivityType($activityType)
    {
        $types = [
            'video' => 'video',
            'quiz' => 'quiz',
            'assignment' => 'assignment',
            'lesson' => 'video',
            'reading' => 'video'
        ];
        
        return $types[$activityType] ?? 'video';
    }

    public function uploadAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $user = $request->user();
            $student = Student::where('user_id', $user->id)->firstOrFail();

            Log::info('=== AVATAR UPLOAD START ===');
            Log::info('Current profile_picture: ' . $student->profile_picture);
            
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($student->profile_picture) {
                    $oldPath = storage_path('app/public/profile-pictures/' . $student->profile_picture);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                        Log::info('Old avatar deleted: ' . $student->profile_picture);
                    }
                }
                
                // Store new avatar - FIXED PATH
                $filename = 'avatar_' . $user->id . '_' . time() . '.' . $request->avatar->extension();
                
                // Use the correct storage path without extra 'public' folder
                $path = $request->file('avatar')->storeAs('profile-pictures', $filename, 'public');
                
                Log::info('Storage path: ' . $path);
                
                if (!$path) {
                    Log::error('Failed to store avatar file');
                    return response()->json(['error' => 'Failed to store image'], 500);
                }
                
                Log::info('New avatar stored: ' . $filename);
                
                // Update the student record
                $student->update(['profile_picture' => $filename]);
                
                // Refresh to get updated data
                $student->refresh();
                
                Log::info('Student record updated. New profile_picture: ' . $student->profile_picture);
                
                // Verify the file exists in correct location
                $correctPath = storage_path('app/public/profile-pictures/' . $filename);
                $fileExists = file_exists($correctPath);
                Log::info('File exists in correct location: ' . ($fileExists ? 'YES' : 'NO'));
                Log::info('Correct path: ' . $correctPath);
                
                $avatarUrl = asset('storage/profile-pictures/' . $filename);
                
                Log::info('Avatar URL to return: ' . $avatarUrl);
                Log::info('=== AVATAR UPLOAD END ===');
                
                return response()->json([
                    'message' => 'Avatar uploaded successfully',
                    'avatar_url' => $avatarUrl,
                    'filename' => $filename,
                    'file_exists' => $fileExists
                ]);
            }

            return response()->json(['error' => 'No file uploaded'], 400);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Avatar upload validation error: ' . json_encode($e->errors()));
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Avatar upload error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function getProfileData(Request $request)
    {
        $user = $request->user();
        $student = Student::where('user_id', $user->id)->first();
        
        $coursesEnrolled = Enrollment::where('user_id', $user->id)->count();
        $coursesCompleted = Enrollment::where('user_id', $user->id)
            ->where('progress', '>=', 100)
            ->count();
        $learningHours = $this->calculateLearningHours($user->id);
        $currentStreak = $this->calculateCurrentStreak($user->id);

        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $student->phone ?? $user->phone,
                'joined_date' => $user->created_at->format('M d, Y'),
            ],
            'stats' => [
                'courses_enrolled' => $coursesEnrolled,
                'courses_completed' => $coursesCompleted,
                'learning_hours' => $learningHours,
                'current_streak' => $currentStreak,
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        // Update user name and email
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email']
        ]);

        // Update student profile fields
        $student->update([
            'phone' => $validated['phone'],
            'bio' => $validated['bio'],
            'location' => $validated['location'],
            'father_name' => $validated['father_name'],
            'mother_name' => $validated['mother_name'],
            'address' => $validated['address'],
        ]);

        return response()->json(['message' => 'Profile updated successfully']);
    }
}