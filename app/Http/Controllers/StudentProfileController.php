<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        
        // Mock data for demonstration
        $profileData = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '+8801XXXXXXXXX',
                'avatar' => null,
                'joined_date' => $user->created_at->format('M d, Y'),
            ],
            'stats' => [
                'courses_enrolled' => 12,
                'courses_completed' => 8,
                'learning_hours' => 156,
                'current_streak' => 7,
            ],
            'recent_activity' => [
                [
                    'course' => 'Web Development Bootcamp',
                    'progress' => 85,
                    'last_accessed' => '2 hours ago',
                    'type' => 'video'
                ],
                [
                    'course' => 'JavaScript Mastery',
                    'progress' => 100,
                    'last_accessed' => '1 day ago',
                    'type' => 'quiz'
                ],
                [
                    'course' => 'React Advanced Patterns',
                    'progress' => 45,
                    'last_accessed' => '3 days ago',
                    'type' => 'assignment'
                ]
            ]
        ];

        return Inertia::render('StudentProfile/Index', [
            'profile' => $profileData
        ]);
    }

    public function getProfileData(Request $request)
    {
        $user = $request->user();
        
        // Return profile data for API calls
        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'joined_date' => $user->created_at->format('M d, Y'),
            ],
            'stats' => [
                'courses_enrolled' => 12,
                'courses_completed' => 8,
                'learning_hours' => 156,
                'current_streak' => 7,
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $user->update($validated);

        return response()->json(['message' => 'Profile updated successfully']);
    }
}