<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $settingsData = [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '+8801XXXXXXXXX',
                'bio' => 'Passionate learner focused on web development and programming.',
                'location' => 'Dhaka, Bangladesh',
                'website' => 'https://myportfolio.com',
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
            ]
        ];

        return Inertia::render('Settings/Index', [
            'settings' => $settingsData
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

        // Save preferences logic here

        return response()->json(['message' => 'Preferences updated successfully']);
    }
}