<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Student;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get student profile data for avatar
        $student = Student::where('user_id', $user->id)->first();
        
        $settingsData = [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? $student->phone ?? '+8801XXXXXXXXX',
                'bio' => $student->bio ?? 'Passionate learner focused on web development and programming.',
                'location' => $student->location ?? 'Dhaka, Bangladesh',
                'website' => $user->website ?? 'https://myportfolio.com',
                'language' => 'bn',
                'timezone' => 'Asia/Dhaka'
            ],
            'preferences' => [
                'email_notifications' => true,
                'push_notifications' => true,
                'sms_notifications' => false,
                'course_updates' => true,
                'newsletter' => true,
                'learning_reminders' => true,
                'dark_mode' => false
            ],
            'security' => [
                'two_factor_auth' => false,
                'login_alerts' => true,
                'device_management' => true
            ],
            'billing' => [
                'plan' => 'Free',
                'status' => 'active',
                'next_billing_date' => null,
                'payment_method' => null
            ],
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $student ? $this->getAvatarUrl($student->profile_picture) : null,
                'student_info' => $student ? [
                    'roll_number' => $student->roll_number,
                    'class' => $student->class ? $student->class->name : 'Not assigned'
                ] : null
            ]
        ];

        return Inertia::render('Settings/Index', [
            'settings' => $settingsData
        ]);
    }

    private function getAvatarUrl($profilePicture)
    {
        if (!$profilePicture) {
            return null;
        }
        
        // Check if it's already a full URL
        if (str_starts_with($profilePicture, 'http')) {
            return $profilePicture;
        }
        
        // Return storage URL
        return asset('storage/profile-pictures/' . $profilePicture);
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
            'website' => 'nullable|url|max:255',
            'language' => 'required|string|in:en,bn',
            'timezone' => 'required|string'
        ]);

        // Update user data
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'website' => $validated['website']
        ]);

        // Update student data
        $student->update([
            'phone' => $validated['phone'],
            'bio' => $validated['bio'],
            'location' => $validated['location']
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $validated
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'course_updates' => 'boolean',
            'newsletter' => 'boolean',
            'learning_reminders' => 'boolean',
            'dark_mode' => 'boolean',
        ]);

        // Save preferences to user settings or separate table
        // For now, we'll just return success
        // You can implement your preferences storage logic here

        return response()->json([
            'message' => 'Preferences updated successfully',
            'preferences' => $validated
        ]);
    }

    public function updateSecurity(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'two_factor_auth' => 'boolean',
            'login_alerts' => 'boolean',
            'device_management' => 'boolean',
        ]);

        // Save security settings
        // You can implement your security settings storage logic here

        return response()->json([
            'message' => 'Security settings updated successfully',
            'security' => $validated
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user->update([
            'password' => bcrypt($validated['new_password'])
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }
}