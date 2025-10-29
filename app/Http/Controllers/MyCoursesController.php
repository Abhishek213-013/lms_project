<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MyCoursesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $courses = [
            'enrolled' => [
                [
                    'id' => 1,
                    'title' => 'Web Development Bootcamp',
                    'instructor' => 'John Smith',
                    'thumbnail' => '/images/courses/web-dev.jpg',
                    'progress' => 85,
                    'duration' => '45 hours',
                    'lessons_completed' => 34,
                    'total_lessons' => 40,
                    'category' => 'Development',
                    'last_accessed' => '2 hours ago',
                    'rating' => 4.8
                ],
                [
                    'id' => 2,
                    'title' => 'JavaScript Mastery',
                    'instructor' => 'Sarah Johnson',
                    'thumbnail' => '/images/courses/js-mastery.jpg',
                    'progress' => 100,
                    'duration' => '30 hours',
                    'lessons_completed' => 25,
                    'total_lessons' => 25,
                    'category' => 'Programming',
                    'last_accessed' => '1 day ago',
                    'rating' => 4.9
                ]
            ],
            'completed' => [
                [
                    'id' => 5,
                    'title' => 'HTML & CSS Fundamentals',
                    'instructor' => 'Emily Wilson',
                    'thumbnail' => '/images/courses/html-css.jpg',
                    'progress' => 100,
                    'duration' => '15 hours',
                    'lessons_completed' => 20,
                    'total_lessons' => 20,
                    'category' => 'Web Design',
                    'completed_date' => '2 weeks ago',
                    'rating' => 4.5,
                    'certificate_available' => true
                ]
            ],
            'wishlist' => [
                [
                    'id' => 6,
                    'title' => 'Python for Data Science',
                    'instructor' => 'Dr. James Brown',
                    'thumbnail' => '/images/courses/python-ds.jpg',
                    'duration' => '40 hours',
                    'price' => 79.99,
                    'category' => 'Data Science',
                    'rating' => 4.8,
                    'students' => 12500
                ]
            ]
        ];

        return Inertia::render('MyCourses/Index', [
            'courses' => $courses
        ]);
    }

    public function getCoursesData(Request $request)
    {
        $user = $request->user();
        
        // Return courses data for API calls
        return response()->json([
            'enrolled' => [],
            'completed' => [],
            'wishlist' => []
        ]);
    }
}