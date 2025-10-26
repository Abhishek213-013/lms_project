<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function dashboard(): Response
    {
        return Inertia::render('Student/Dashboard');
    }

    public function myCourses(): Response
    {
        return Inertia::render('Student/MyCourses');
    }

    public function profile(): Response
    {
        return Inertia::render('Student/Profile');
    }

    public function progress(): Response
    {
        return Inertia::render('Student/Progress');
    }

    public function settings(): Response
    {
        return Inertia::render('Student/Settings');
    }

    public function grades(): Response
    {
        return Inertia::render('Student/Grades');
    }

    public function getStudentStats()
    {
        // Return student stats as JSON
    }
}