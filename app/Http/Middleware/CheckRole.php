<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // If no user is logged in, redirect to login
        if (!$user) {
            return redirect('/login');
        }

        // Check if user has any of the required roles
        if (!in_array($user->role, $roles)) {
            return $this->redirectToDashboard($user->role);
        }

        return $next($request);
    }

    /**
     * Redirect user to their appropriate dashboard
     */
    protected function redirectToDashboard($role)
    {
        return match($role) {
            'super_admin' => redirect()->route('super.admin'),
            'admin' => redirect()->route('admin'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect('/'), // Redirect students to home instead of undefined student.dashboard
            default => redirect('/')->with('error', 'Access denied.'),
        };
    }
}