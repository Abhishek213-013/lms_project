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

class LearningProgressController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        Log::info('=== LEARNING PROGRESS SHOW METHOD START ===');
        Log::info('User ID: ' . $user->id);

        // Get student record with fresh data
        $student = Student::with(['class'])
            ->where('user_id', $user->id)
            ->first();

        Log::info('Student found: ' . ($student ? 'YES' : 'NO'));
        
        if ($student) {
            Log::info('Student profile_picture: ' . $student->profile_picture);
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

        // Get progress data
        $overview = $this->getOverviewStats($user->id);
        $weeklyProgress = $this->getWeeklyProgress($user->id);
        $courseProgress = $this->getCourseProgress($user->id);
        $achievements = $this->getAchievements($user->id);

        // Build profile data for the sidebar
        $profileData = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $student->phone ?? $user->phone ?? '+8801XXXXXXXXX',
                'avatar' => $student->profile_picture_url, // Use the accessor
                'joined_date' => $user->created_at->format('M d, Y'),
                'bio' => $student->bio,
                'location' => $student->location,
            ],
            'stats' => [
                'courses_enrolled' => $overview['total_courses'],
                'courses_completed' => $overview['completed_courses'],
                'learning_hours' => $overview['total_learning_hours'],
                'current_streak' => $overview['current_streak'],
            ],
            'recent_activity' => $this->getRecentActivity($user->id),
            'student_info' => [
                'roll_number' => $student->roll_number,
                'class' => $student->class ? $student->class->name : 'Not assigned',
                'admission_date' => $student->admission_date ? $student->admission_date->format('M d, Y') : $user->created_at->format('M d, Y'),
                'father_name' => $student->father_name,
                'mother_name' => $student->mother_name,
                'parent_contact' => $student->full_parent_contact,
                'address' => $student->address,
                'profile_picture' => $student->profile_picture,
                'profile_picture_url' => $student->profile_picture_url,
            ]
        ];

        Log::info('Final profile data sent to frontend:');
        Log::info('Avatar in user section: ' . $profileData['user']['avatar']);
        Log::info('=== LEARNING PROGRESS SHOW METHOD END ===');

        return Inertia::render('LearningProgress/Index', [
            'progress' => [
                'overview' => $overview,
                'weekly_progress' => $weeklyProgress,
                'course_progress' => $courseProgress,
                'achievements' => $achievements
            ],
            'profile' => $profileData
        ]);
    }

    private function getOverviewStats($userId)
    {
        // Get total courses enrolled
        $totalCourses = Enrollment::where('user_id', $userId)->count();
        
        // Get completed courses (progress >= 100%)
        $completedCourses = Enrollment::where('user_id', $userId)
            ->where('progress', '>=', 100)
            ->count();
            
        // Get in-progress courses
        $inProgressCourses = Enrollment::where('user_id', $userId)
            ->where('progress', '>', 0)
            ->where('progress', '<', 100)
            ->count();
        
        // Calculate total learning hours
        $totalLearningHours = CourseProgress::where('user_id', $userId)
            ->sum('time_spent_minutes') / 60;
            
        // Calculate average progress
        $averageProgress = Enrollment::where('user_id', $userId)
            ->where('progress', '>', 0)
            ->avg('progress') ?? 0;
            
        // Calculate current streak
        $currentStreak = $this->calculateCurrentStreak($userId);
        
        // Calculate longest streak
        $longestStreak = $this->calculateLongestStreak($userId);

        return [
            'total_courses' => $totalCourses,
            'completed_courses' => $completedCourses,
            'in_progress_courses' => $inProgressCourses,
            'total_learning_hours' => round($totalLearningHours, 1),
            'average_progress' => round($averageProgress),
            'current_streak' => $currentStreak,
            'longest_streak' => $longestStreak
        ];
    }

    private function getWeeklyProgress($userId)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $hoursData = [];
        $coursesData = [];
        
        // Get progress data for each day of the current week
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            
            // Calculate learning hours for the day
            $dayHours = CourseProgress::where('user_id', $userId)
                ->whereDate('accessed_at', $day)
                ->sum('time_spent_minutes') / 60;
                
            // Count unique courses accessed on the day
            $dayCourses = CourseProgress::where('user_id', $userId)
                ->whereDate('accessed_at', $day)
                ->distinct('class_id')
                ->count('class_id');
                
            $hoursData[] = round($dayHours, 1);
            $coursesData[] = $dayCourses;
        }

        return [
            'labels' => $daysOfWeek,
            'hours' => $hoursData,
            'courses' => $coursesData
        ];
    }

    private function getCourseProgress($userId)
    {
        return Enrollment::with(['class' => function($query) {
                $query->select('id', 'name', 'subject', 'category');
            }])
            ->where('user_id', $userId)
            ->where('progress', '>', 0)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($enrollment) use ($userId) {
                // Calculate time spent on this course
                $timeSpent = CourseProgress::where('user_id', $userId)
                    ->where('class_id', $enrollment->class_id)
                    ->sum('time_spent_minutes');
                    
                // Get last activity
                $lastActivity = CourseProgress::where('user_id', $userId)
                    ->where('class_id', $enrollment->class_id)
                    ->latest('accessed_at')
                    ->first();
                    
                // Calculate weekly trend
                $weeklyTrend = $this->calculateWeeklyTrend($userId, $enrollment->class_id);
                
                return [
                    'id' => $enrollment->class_id,
                    'title' => $enrollment->class->name ?? 'Unknown Course',
                    'category' => $enrollment->class->category ?? 'General',
                    'progress' => $enrollment->progress,
                    'time_spent' => $this->formatTimeSpent($timeSpent),
                    'last_activity' => $lastActivity ? $lastActivity->accessed_at->diffForHumans() : 'Never',
                    'weekly_trend' => $weeklyTrend
                ];
            })
            ->toArray();
    }

    private function getAchievements($userId)
    {
        // This would typically come from an achievements table
        // For now, we'll calculate based on user activity
        
        $totalLearningHours = CourseProgress::where('user_id', $userId)
            ->sum('time_spent_minutes') / 60;
            
        $completedCourses = Enrollment::where('user_id', $userId)
            ->where('progress', '>=', 100)
            ->count();
            
        $currentStreak = $this->calculateCurrentStreak($userId);
        
        return [
            [
                'title' => 'First Steps',
                'description' => 'Complete your first lesson',
                'icon' => 'fas fa-footsteps',
                'progress' => $this->hasCompletedLesson($userId) ? 100 : 0,
                'completed' => $this->hasCompletedLesson($userId),
                'date_earned' => $this->getFirstLessonDate($userId)
            ],
            [
                'title' => 'Course Completer',
                'description' => 'Complete your first course',
                'icon' => 'fas fa-graduation-cap',
                'progress' => $completedCourses > 0 ? 100 : 0,
                'completed' => $completedCourses > 0,
                'date_earned' => $this->getFirstCompletionDate($userId)
            ],
            [
                'title' => 'Dedicated Learner',
                'description' => 'Reach 50 total learning hours',
                'icon' => 'fas fa-clock',
                'progress' => min(($totalLearningHours / 50) * 100, 100),
                'completed' => $totalLearningHours >= 50,
                'date_earned' => $this->getFiftyHoursDate($userId)
            ],
            [
                'title' => 'Weekend Warrior',
                'description' => 'Learn for 5 hours on weekends',
                'icon' => 'fas fa-calendar-week',
                'progress' => $this->getWeekendProgress($userId),
                'completed' => $this->getWeekendProgress($userId) >= 100,
                'date_earned' => $this->getWeekendAchievementDate($userId)
            ],
            [
                'title' => 'Consistent Learner',
                'description' => 'Maintain a 7-day streak',
                'icon' => 'fas fa-fire',
                'progress' => min(($currentStreak / 7) * 100, 100),
                'completed' => $currentStreak >= 7,
                'date_earned' => $this->getStreakAchievementDate($userId, 7)
            ],
            [
                'title' => 'Rapid Learner',
                'description' => 'Complete 3 courses in one month',
                'icon' => 'fas fa-bolt',
                'progress' => $this->getRapidLearnerProgress($userId),
                'completed' => $this->getRapidLearnerProgress($userId) >= 100,
                'date_earned' => $this->getRapidLearnerDate($userId)
            ]
        ];
    }

    private function getRecentActivity($userId, $limit = 3)
    {
        return Enrollment::with(['class' => function($query) {
                $query->select('id', 'name', 'subject');
            }])
            ->where('user_id', $userId)
            ->where('progress', '>', 0)
            ->whereNotNull('last_accessed')
            ->orderBy('last_accessed', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($enrollment) {
                return [
                    'course' => $enrollment->class->name ?? 'Unknown Course',
                    'progress' => $enrollment->progress,
                    'last_accessed' => $enrollment->last_accessed ? $enrollment->last_accessed->diffForHumans() : 'Never',
                    'type' => $this->getActivityType($enrollment->last_activity_type)
                ];
            })
            ->toArray();
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

    private function calculateLongestStreak($userId)
    {
        // This is a simplified version - in production you'd want a more robust solution
        $currentStreak = $this->calculateCurrentStreak($userId);
        return max($currentStreak, 7); // Example fallback
    }

    private function calculateWeeklyTrend($userId, $classId)
    {
        $thisWeekStart = Carbon::now()->startOfWeek();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        
        // This week's progress
        $thisWeekProgress = CourseProgress::where('user_id', $userId)
            ->where('class_id', $classId)
            ->where('accessed_at', '>=', $thisWeekStart)
            ->sum('time_spent_minutes');
            
        // Last week's progress
        $lastWeekProgress = CourseProgress::where('user_id', $userId)
            ->where('class_id', $classId)
            ->where('accessed_at', '>=', $lastWeekStart)
            ->where('accessed_at', '<', $thisWeekStart)
            ->sum('time_spent_minutes');
            
        if ($lastWeekProgress == 0) {
            return $thisWeekProgress > 0 ? 'up' : 'stable';
        }
        
        $change = (($thisWeekProgress - $lastWeekProgress) / $lastWeekProgress) * 100;
        
        if ($change > 10) return 'up';
        if ($change < -10) return 'down';
        return 'stable';
    }

    private function formatTimeSpent($minutes)
    {
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes === 0) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '');
        }
        
        return $hours . 'h ' . $remainingMinutes . 'm';
    }

    // Achievement helper methods
    private function hasCompletedLesson($userId)
    {
        return CourseProgress::where('user_id', $userId)
            ->where('completed', true)
            ->exists();
    }

    private function getFirstLessonDate($userId)
    {
        $firstLesson = CourseProgress::where('user_id', $userId)
            ->where('completed', true)
            ->orderBy('accessed_at')
            ->first();
            
        return $firstLesson?->accessed_at?->format('Y-m-d');
    }

    private function getFirstCompletionDate($userId)
    {
        $firstCompletion = Enrollment::where('user_id', $userId)
            ->where('progress', '>=', 100)
            ->orderBy('updated_at')
            ->first();
            
        return $firstCompletion?->updated_at?->format('Y-m-d');
    }

    private function getFiftyHoursDate($userId)
    {
        // This would need more complex logic to track when exactly 50 hours was reached
        return null;
    }

    private function getWeekendProgress($userId)
    {
        $weekendHours = CourseProgress::where('user_id', $userId)
            ->whereIn(DB::raw('DAYOFWEEK(accessed_at)'), [1, 7]) // Sunday = 1, Saturday = 7
            ->sum('time_spent_minutes') / 60;
            
        return min(($weekendHours / 5) * 100, 100);
    }

    private function getWeekendAchievementDate($userId)
    {
        // Simplified - would need more complex tracking
        return null;
    }

    private function getStreakAchievementDate($userId, $streakLength)
    {
        // Simplified - would need more complex tracking
        return null;
    }

    private function getRapidLearnerProgress($userId)
    {
        $oneMonthAgo = Carbon::now()->subMonth();
        $recentCompletions = Enrollment::where('user_id', $userId)
            ->where('progress', '>=', 100)
            ->where('updated_at', '>=', $oneMonthAgo)
            ->count();
            
        return min(($recentCompletions / 3) * 100, 100);
    }

    private function getRapidLearnerDate($userId)
    {
        // Simplified - would need more complex tracking
        return null;
    }

    public function getProgressData(Request $request)
    {
        $user = $request->user();
        
        $overview = $this->getOverviewStats($user->id);
        $weeklyProgress = $this->getWeeklyProgress($user->id);
        $courseProgress = $this->getCourseProgress($user->id);
        $achievements = $this->getAchievements($user->id);

        return response()->json([
            'overview' => $overview,
            'weekly_progress' => $weeklyProgress,
            'course_progress' => $courseProgress,
            'achievements' => $achievements
        ]);
    }
}