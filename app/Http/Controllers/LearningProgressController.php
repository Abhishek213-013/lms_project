<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LearningProgressController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $progressData = [
            'overview' => [
                'total_courses' => 12,
                'completed_courses' => 8,
                'in_progress_courses' => 4,
                'total_learning_hours' => 156,
                'average_progress' => 72,
                'current_streak' => 7,
                'longest_streak' => 21
            ],
            'weekly_progress' => [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'hours' => [2.5, 3.0, 1.5, 2.0, 4.0, 1.0, 2.5],
                'courses' => [3, 4, 2, 3, 5, 1, 3]
            ],
            'course_progress' => [
                [
                    'id' => 1,
                    'title' => 'Web Development Bootcamp',
                    'category' => 'Development',
                    'progress' => 85,
                    'time_spent' => '45 hours',
                    'last_activity' => '2 hours ago',
                    'weekly_trend' => 'up'
                ],
                [
                    'id' => 2,
                    'title' => 'JavaScript Mastery',
                    'category' => 'Programming',
                    'progress' => 100,
                    'time_spent' => '30 hours',
                    'last_activity' => '1 day ago',
                    'weekly_trend' => 'stable'
                ],
                [
                    'id' => 3,
                    'title' => 'React Advanced Patterns',
                    'category' => 'Frontend',
                    'progress' => 45,
                    'time_spent' => '15 hours',
                    'last_activity' => '3 days ago',
                    'weekly_trend' => 'up'
                ],
                [
                    'id' => 4,
                    'title' => 'Node.js Backend Development',
                    'category' => 'Backend',
                    'progress' => 20,
                    'time_spent' => '8 hours',
                    'last_activity' => '1 week ago',
                    'weekly_trend' => 'down'
                ]
            ],
            'achievements' => [
                [
                    'title' => 'Early Bird',
                    'description' => 'Complete 5 lessons before 8 AM',
                    'icon' => 'fas fa-sun',
                    'progress' => 100,
                    'completed' => true,
                    'date_earned' => '2024-01-15'
                ],
                [
                    'title' => 'Weekend Warrior',
                    'description' => 'Learn for 10 hours on weekends',
                    'icon' => 'fas fa-calendar-week',
                    'progress' => 80,
                    'completed' => false,
                    'date_earned' => null
                ],
                [
                    'title' => 'Quick Learner',
                    'description' => 'Complete a course in under 7 days',
                    'icon' => 'fas fa-bolt',
                    'progress' => 100,
                    'completed' => true,
                    'date_earned' => '2024-01-20'
                ],
                [
                    'title' => 'Consistent Learner',
                    'description' => 'Maintain a 30-day streak',
                    'icon' => 'fas fa-fire',
                    'progress' => 23,
                    'completed' => false,
                    'date_earned' => null
                ]
            ]
        ];

        return Inertia::render('LearningProgress/Index', [
            'progress' => $progressData
        ]);
    }

    public function getProgressData(Request $request)
    {
        $user = $request->user();
        
        // Return progress data for API calls
        return response()->json([
            'overview' => [
                'average_progress' => 0,
                'total_learning_hours' => 0,
                'completed_courses' => 0,
                'current_streak' => 0
            ],
            'weekly_progress' => [
                'labels' => [],
                'hours' => [],
                'courses' => []
            ],
            'course_progress' => [],
            'achievements' => []
        ]);
    }
}