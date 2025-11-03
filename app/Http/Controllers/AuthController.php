<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    /**
     * Show the admin/teacher login page
     */
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Show the admin/teacher registration page
     */
    public function showRegistration(): Response
    {
        return Inertia::render('Auth/Registration');
    }

    /**
     * Show the teacher registration page
     */
    public function showTeacherRegistration(): Response
    {
        return Inertia::render('Auth/TeacherRegistration');
    }

    /**
     * Show the student login page
     */
    public function showStudentLogin(): Response
    {
        return Inertia::render('Auth/StudentLogin');
    }

    /**
     * Show the student registration page
     */
    public function showStudentRegistration(): Response
    {
        return Inertia::render('Auth/StudentRegistration', [
            'phone' => request('phone')
        ]);
    }

    /**
     * Show the phone verification page
     */
    public function showPhoneVerification(): Response
    {
        return Inertia::render('Auth/PhoneVerification');
    }

    /**
     * Handle admin/teacher login request
     */
    public function login(Request $request)
    {
        // Validate the request
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // Redirect based on user role
            $user = Auth::user();
            
            if ($user->role === 'super_admin') {
                return redirect()->intended('/super-admin');
            } elseif ($user->role === 'admin') {
                return redirect()->intended('/admin');
            } elseif ($user->role === 'teacher') {
                return redirect()->intended("/teacher/portal");
            } else {
                return redirect()->intended('/');
            }
        }

        // Return back with error
        return back()->withErrors([
            'message' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('username', 'remember'));
    }

    /**
     * Handle student login request
     */
    public function studentLogin(Request $request)
    {
        Log::info('Student login attempt', ['login' => $request->login, 'ip' => $request->ip()]);

        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // Determine if login is email or username
            $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            
            if (Auth::attempt([
                $field => $credentials['login'], 
                'password' => $credentials['password']
            ], $request->boolean('remember'))) {
                
                $request->session()->regenerate();
                $user = Auth::user();

                Log::info('Student login successful', ['user_id' => $user->id, 'role' => $user->role]);

                // Check if user is a student
                if ($user->role !== 'student') {
                    Auth::logout();
                    return back()->withErrors([
                        'message' => 'This login is for students only.',
                    ]);
                }
                
                // FIX: Redirect to home page instead of student dashboard
                return redirect()->intended('/');
            }

            Log::warning('Student login failed - invalid credentials', ['login' => $credentials['login']]);
            return back()->withErrors([
                'message' => 'The provided credentials do not match our records.',
            ]);

        } catch (\Exception $e) {
            Log::error('Student login error', ['error' => $e->getMessage(), 'login' => $credentials['login']]);
            return back()->withErrors([
                'message' => 'An error occurred during login. Please try again.',
            ]);
        }
    }

    /**
     * Handle admin/teacher registration
     */
    public function register(Request $request)
    {
        Log::info('Registration attempt', ['email' => $request->email, 'role' => $request->role]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'dob' => 'required|date',
            'education_qualification' => 'required|in:HSC,BSC,BA,MA,MSC,PhD,Other',
            'institute' => 'required|string|max:255',
            'experience' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:teacher,admin',
        ]);

        if ($validator->fails()) {
            Log::warning('Registration validation failed', ['errors' => $validator->errors()->toArray()]);
            return redirect()->back()->withErrors($validator->errors());
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'dob' => $request->dob,
                'education_qualification' => $request->education_qualification,
                'institute' => $request->institute,
                'experience' => $request->experience,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => $request->role === 'teacher' ? 'pending' : 'active', // Teachers need approval
            ]);

            Auth::login($user);

            Log::info('Registration successful', ['user_id' => $user->id, 'role' => $user->role]);

            return $this->redirectBasedOnRole($user)
                ->with('status', $request->role === 'teacher' 
                    ? 'Registration submitted for approval!' 
                    : 'Registration successful! Welcome to Pathshala LMS.');

        } catch (\Exception $e) {
            Log::error('Registration failed', ['error' => $e->getMessage(), 'email' => $request->email]);
            return back()->withErrors([
                'message' => 'Registration failed. Please try again.',
            ]);
        }
    }

        /**
     * Handle teacher registration
     */
    public function teacherRegister(Request $request)
    {
        Log::info('Teacher registration attempt', ['email' => $request->email, 'data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'dob' => 'required|date',
            'education_qualification' => 'required|string|max:255|in:HSC,BSC,BA,MA,MSC,PhD,Other',
            'institute' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            Log::warning('Teacher registration validation failed', ['errors' => $validator->errors()->toArray()]);
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'dob' => $request->dob,
                'education_qualification' => $request->education_qualification,
                'institute' => $request->institute,
                'experience' => $request->experience,
                'bio' => $request->bio,
                'password' => Hash::make($request->password),
                'role' => 'teacher',
                'status' => 'pending', // Teachers need admin approval
            ]);

            DB::commit();

            Log::info('Teacher registration successful', ['user_id' => $user->id]);

            // Don't login automatically - wait for admin approval
            return redirect()->route('login')
                ->with('status', 'Teacher registration submitted successfully! Your account is pending admin approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Teacher registration failed', [
                'error' => $e->getMessage(), 
                'email' => $request->email,
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()->withErrors([
                'message' => 'Teacher registration failed: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    /**
     * Handle student registration
     */
    public function studentRegister(Request $request)
    {
        Log::info('Student registration attempt', ['email' => $request->email, 'data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'class_id' => 'required|integer|exists:classes,id',
            'country_code' => 'required|string|max:10',
            'parent_contact' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            Log::warning('Student registration validation failed', ['errors' => $validator->errors()->toArray()]);
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            DB::beginTransaction();

            Log::info('Creating user...');
            
            // Create user with student role
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'status' => 'active',
            ]);

            Log::info('User created successfully', ['user_id' => $user->id]);

            // Generate a unique roll number
            $rollNumber = $this->generateStudentRollNumber($request->class_id);

            // Create student record
            $studentData = [
                'user_id' => $user->id,
                'class_id' => $request->class_id,
                'roll_number' => $rollNumber,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'parent_contact' => $request->parent_contact,
                'country_code' => $request->country_code,
                'admission_date' => now(),
                'status' => 'active',
            ];

            Log::info('Creating student record', ['student_data' => $studentData]);

            $student = Student::create($studentData);

            Log::info('Student record created successfully', ['student_id' => $student->id]);

            DB::commit();

            // Log the user in automatically after registration
            Auth::login($user);

            Log::info('Student registration successful', [
                'user_id' => $user->id,
                'student_id' => $student->id,
                'authenticated' => Auth::check(),
                'user_role' => Auth::user()->role ?? 'none'
            ]);

            // FIX: Redirect to home page instead of student dashboard
            return redirect('/')->with([
                'success' => 'Registration successful! Welcome to Pathshala LMS.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Student registration failed', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'email' => $request->email,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->withErrors([
                'message' => 'Registration failed: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    /**
     * Generate unique roll number for student
     */
    private function generateStudentRollNumber($classId)
    {
        $currentYear = date('Y');
        $classCode = str_pad($classId, 2, '0', STR_PAD_LEFT);
        
        $lastStudent = Student::where('class_id', $classId)
            ->orderBy('id', 'desc')
            ->first();
            
        $sequence = $lastStudent ? (int)substr($lastStudent->roll_number, -3) + 1 : 1;
        $sequenceCode = str_pad($sequence, 3, '0', STR_PAD_LEFT);
        
        return "ST{$currentYear}{$classCode}{$sequenceCode}";
    }

    /**
     * Handle OTP sending request
     */
    public function sendOTP(Request $request)
    {
        Log::info('OTP sending attempt', ['phone' => $request->phoneNumber]);

        $validator = Validator::make($request->all(), [
            'countryCode' => 'required|string|max:10',
            'phoneNumber' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        try {
            // For demo purposes, we'll just return success
            $otp = '123456';
            
            // Store OTP in session for verification
            session([
                'otp_code' => $otp,
                'otp_phone' => $request->countryCode . $request->phoneNumber,
                'otp_expires' => now()->addMinutes(10),
            ]);

            Log::info('OTP sent (demo)', [
                'phone' => $request->countryCode . $request->phoneNumber,
                'otp' => $otp
            ]);

            return back()->with('status', 'OTP sent successfully to ' . $request->countryCode . $request->phoneNumber);

        } catch (\Exception $e) {
            Log::error('OTP sending failed', ['error' => $e->getMessage(), 'phone' => $request->phoneNumber]);
            return back()->withErrors([
                'message' => 'Failed to send OTP. Please try again.',
            ]);
        }
    }

    /**
     * Handle OTP verification request
     */
    public function verifyOTP(Request $request)
    {
        Log::info('OTP verification attempt', ['otp' => $request->otp]);

        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        try {
            $storedOtp = session('otp_code');
            $otpPhone = session('otp_phone');
            $otpExpires = session('otp_expires');

            // Check if OTP exists and is not expired
            if (!$storedOtp || !$otpExpires || now()->gt($otpExpires)) {
                return back()->withErrors([
                    'message' => 'OTP has expired. Please request a new one.',
                ]);
            }

            // For demo, accept only 123456
            if ($request->otp !== '123456') {
                Log::warning('OTP verification failed - invalid OTP', ['provided' => $request->otp]);
                return back()->withErrors([
                    'message' => 'Invalid OTP. Please use 123456 for demo.',
                ]);
            }

            // OTP verified successfully
            session([
                'phone_verified' => true,
                'verified_phone' => $otpPhone,
            ]);

            // Clear OTP data
            session()->forget(['otp_code', 'otp_phone', 'otp_expires']);

            Log::info('OTP verification successful', ['phone' => $otpPhone]);

            return redirect()->route('student.registration', ['phone' => $otpPhone])
                ->with('status', 'Phone number verified successfully!');

        } catch (\Exception $e) {
            Log::error('OTP verification failed', ['error' => $e->getMessage()]);
            return back()->withErrors([
                'message' => 'OTP verification failed. Please try again.',
            ]);
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to login page instead of home page
        return redirect('/login');
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        try {
            // In a real application, you would send a password reset email here
            Log::info('Password reset requested', ['email' => $request->email]);

            return back()->with('status', 'Password reset link has been sent to your email.');

        } catch (\Exception $e) {
            Log::error('Password reset request failed', ['error' => $e->getMessage(), 'email' => $request->email]);
            return back()->withErrors([
                'message' => 'Failed to send password reset link. Please try again.',
            ]);
        }
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole($user)
    {
        return match($user->role) {
            'super_admin' => redirect()->intended('/super-admin'),
            'admin' => redirect()->intended('/admin'),
            'teacher' => redirect()->intended('/teacher'),
            'student' => redirect()->intended('/student/dashboard'),
            default => redirect()->intended('/'),
        };
    }

    /**
     * Get current authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'username' => $request->user()->username,
                'role' => $request->user()->role,
            ] : null
        ]);
    }

    /**
     * Check authentication status
     */
    public function checkAuth(Request $request)
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::check() ? [
                'id' => Auth::user()->id,
                'name' => Auth::user()->name,
                'role' => Auth::user()->role,
            ] : null
        ]);
    }
}