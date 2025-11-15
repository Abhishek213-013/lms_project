<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\AcademicClass;
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
    // app/Http\Controllers\AuthController.php

    public function showStudentRegistration(): Response
    {
        // Get active academic classes for the registration form
        $academicClasses = AcademicClass::active()->get(['id', 'name', 'grade']);
        
        // Debug: Log the data
        Log::info('Academic Classes in Controller:', [
            'count' => $academicClasses->count(),
            'data' => $academicClasses->toArray()
        ]);
        
        // Debug: Also check what's being sent to Inertia
        $props = [
            'phone' => request('phone'),
            'academicClasses' => $academicClasses
        ];
        
        Log::info('Props sent to Inertia:', [
            'academicClasses_count' => count($props['academicClasses']),
            'academicClasses_type' => gettype($props['academicClasses']),
            'phone' => $props['phone']
        ]);
        
        return Inertia::render('Auth/StudentRegistration', $props);
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
     * Handle student registration with new database structure
     */
    public function studentRegister(Request $request)
    {
        Log::info('🎯 ============ STUDENT REGISTRATION START ============');
        Log::info('📦 Request data:', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'academic_class_id' => 'required|integer|exists:academic_classes,id',
            'country_code' => 'required|string|max:10',
            'parent_contact' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            Log::error('❌ Validation failed:', $validator->errors()->toArray());
            return back()->withErrors($validator->errors())->withInput();
        }

        Log::info('✅ Validation passed');

        try {
            DB::beginTransaction();
            Log::info('💾 Starting database transaction...');

            // Check if academic class exists
            $academicClass = AcademicClass::find($request->academic_class_id);
            if (!$academicClass) {
                throw new \Exception('Academic class not found');
            }

            // Create user with all required fields
            $userData = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'status' => 'active',
                'bio' => '', // Explicitly set empty bio
                'phone' => $request->parent_contact, // Use parent contact as phone
            ];

            Log::info('👤 Creating user with data:', $userData);
            $user = User::create($userData);
            Log::info('✅ User created successfully', ['user_id' => $user->id]);

            // Generate roll number
            $rollNumber = $this->generateStudentRollNumber($request->academic_class_id);
            Log::info('🎫 Generated roll number:', ['roll_number' => $rollNumber]);

            // Create student record
            $studentData = [
                'user_id' => $user->id,
                'academic_class_id' => $request->academic_class_id,
                'roll_number' => $rollNumber,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'parent_contact' => $request->parent_contact,
                'country_code' => $request->country_code,
                'admission_date' => now(),
                'status' => 'active',
            ];

            Log::info('📝 Creating student record:', $studentData);
            $student = Student::create($studentData);
            Log::info('✅ Student record created successfully', ['student_id' => $student->id]);

            // Enroll in subjects
            Log::info('📚 Enrolling student in subjects...');
            $this->enrollInClassSubjects($student, $request->academic_class_id);

            DB::commit();
            Log::info('💾 Database transaction committed');

            // Login the user
            Log::info('🔐 Logging in user...');
            Auth::login($user);
            $request->session()->regenerate();

            Log::info('✅ User logged in', [
                'authenticated' => Auth::check(),
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role
            ]);

            Log::info('🎉 ============ STUDENT REGISTRATION SUCCESSFUL ============');

            // Redirect to home
            return redirect()->route('home')->with([
                'success' => 'Registration successful! Welcome to Pathshala LMS.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('💥 ============ STUDENT REGISTRATION FAILED ============');
            Log::error('❌ Error: ' . $e->getMessage());
            Log::error('📝 Stack trace: ' . $e->getTraceAsString());
            
            return back()->withErrors([
                'message' => 'Registration failed: ' . $e->getMessage(),
            ])->withInput();
        }
    }

        /**
     * Generate unique roll number for student based on academic class
     */
    private function generateStudentRollNumber($academicClassId)
    {
        $academicClass = AcademicClass::find($academicClassId);
        if (!$academicClass) {
            throw new \Exception('Academic class not found');
        }

        $currentYear = date('Y');
        $classCode = str_pad($academicClass->grade, 2, '0', STR_PAD_LEFT);
        
        // Try up to 10 times to generate a unique roll number
        for ($attempt = 1; $attempt <= 10; $attempt++) {
            // Get the count of students in this academic class
            $studentCount = Student::where('academic_class_id', $academicClassId)->count();
            $sequence = $studentCount + $attempt;
            $sequenceCode = str_pad($sequence, 3, '0', STR_PAD_LEFT);
            
            $rollNumber = "ST{$currentYear}{$classCode}{$sequenceCode}";
            
            // Check if this roll number already exists
            $existingStudent = Student::where('roll_number', $rollNumber)->first();
            if (!$existingStudent) {
                Log::info('✅ Unique roll number generated:', [
                    'roll_number' => $rollNumber,
                    'attempt' => $attempt
                ]);
                return $rollNumber;
            }
            
            Log::warning('⚠️ Roll number already exists, trying again:', [
                'roll_number' => $rollNumber,
                'attempt' => $attempt
            ]);
        }
        
        // If we still can't generate a unique roll number, use timestamp
        $timestamp = time();
        $fallbackRollNumber = "ST{$currentYear}{$classCode}" . substr($timestamp, -3);
        
        Log::warning('🔄 Using fallback roll number:', [
            'roll_number' => $fallbackRollNumber
        ]);
        
        return $fallbackRollNumber;
    }

    /**
     * Automatically enroll student in all subjects for their academic class
     */
    private function enrollInClassSubjects(Student $student, $academicClassId)
    {
        try {
            $academicClass = AcademicClass::find($academicClassId);
            
            if (!$academicClass) {
                Log::warning('Academic class not found for subject enrollment', ['academic_class_id' => $academicClassId]);
                return;
            }

            // Get all subjects for this grade level
            $subjects = ClassModel::where('grade', $academicClass->grade)
                                ->where('type', 'regular')
                                ->where('status', 'active')
                                ->get();

            Log::info('Found subjects for enrollment', [
                'student_id' => $student->id,
                'grade' => $academicClass->grade,
                'subjects_count' => $subjects->count()
            ]);

            $enrollmentData = [];
            foreach ($subjects as $subject) {
                $enrollmentData[$subject->id] = [
                    'progress' => 0,
                    'last_accessed' => null,
                    'last_activity_type' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($enrollmentData)) {
                $student->subjects()->attach($enrollmentData);
                Log::info('Student enrolled in subjects successfully', [
                    'student_id' => $student->id,
                    'subjects_enrolled' => count($enrollmentData)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to enroll student in subjects', [
                'student_id' => $student->id,
                'academic_class_id' => $academicClassId,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception - subject enrollment failure shouldn't block registration
        }
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
            'student' => redirect()->intended('/'),
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