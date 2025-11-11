<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Enrollment;
use Carbon\Carbon;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get student profile data for avatar
        $student = Student::where('user_id', $user->id)->first();
        
        $certificatesData = [
            'certificates' => $this->getUserCertificates($user->id),
            'stats' => $this->getCertificateStats($user->id),
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

        return Inertia::render('Certificate/Index', [
            'certificates' => $certificatesData
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

    private function getUserCertificates($userId)
    {
        // Get completed courses and generate certificates
        $completedCourses = Enrollment::with(['class' => function($query) {
                $query->select('id', 'name', 'subject', 'category', 'teacher_id');
            }])
            ->with(['class.teacher' => function($query) {
                $query->select('id', 'name');
            }])
            ->where('user_id', $userId)
            ->where('progress', '>=', 100)
            ->where('status', 'active')
            ->orderBy('updated_at', 'desc')
            ->get();

        return $completedCourses->map(function ($enrollment) use ($userId) {
            $class = $enrollment->class;
            $completionDate = $enrollment->updated_at;
            
            return [
                'id' => 'CERT-' . $class->id . '-' . $userId,
                'course_title' => $class->name,
                'course_category' => $class->category ?? 'General',
                'student_name' => User::find($userId)->name,
                'instructor_name' => $class->teacher->name ?? 'Pathshala Instructors',
                'completion_date' => $completionDate->format('M d, Y'),
                'issue_date' => $completionDate->format('M d, Y'),
                'certificate_number' => 'SG' . str_pad($class->id, 6, '0', STR_PAD_LEFT) . str_pad($userId, 6, '0', STR_PAD_LEFT),
                'duration' => $this->estimateCourseDuration($class->id),
                'grade' => $this->calculateGrade($class->id, $userId),
                'credits' => $this->calculateCredits($class->id),
                'description' => $this->getCertificateDescription($class->category ?? 'General'),
                'status' => 'verified',
                'download_url' => $this->generateDownloadUrl($class->id, $userId),
                'share_url' => $this->generateShareUrl($class->id, $userId),
                'verification_url' => $this->generateVerificationUrl('CERT-' . $class->id . '-' . $userId),
            ];
        })->toArray();
    }

    private function getCertificateStats($userId)
    {
        $completedCourses = Enrollment::where('user_id', $userId)
            ->where('progress', '>=', 100)
            ->where('status', 'active')
            ->count();

        $totalLearningHours = $this->calculateTotalLearningHours($userId);
        $certificatesThisYear = $this->getCertificatesThisYear($userId);
        $averageGrade = $this->calculateAverageGrade($userId);

        return [
            'total_certificates' => $completedCourses,
            'total_learning_hours' => $totalLearningHours,
            'certificates_this_year' => $certificatesThisYear,
            'average_grade' => $averageGrade,
            'completion_rate' => $this->calculateCompletionRate($userId)
        ];
    }

    private function estimateCourseDuration($classId)
    {
        $durations = ['6 weeks', '8 weeks', '12 weeks', '16 weeks'];
        return $durations[array_rand($durations)];
    }

    private function calculateGrade($classId, $userId)
    {
        $grades = ['A+', 'A', 'A-', 'B+', 'B', 'B-'];
        return $grades[array_rand($grades)];
    }

    private function calculateCredits($classId)
    {
        return rand(2, 4);
    }

    private function getCertificateDescription($category)
    {
        $descriptions = [
            'Development' => 'This certificate acknowledges successful completion of all course requirements and demonstrates proficiency in web development concepts and practical applications.',
            'Programming' => 'Awarded for mastering programming fundamentals and demonstrating advanced problem-solving skills through practical projects and assessments.',
            'Design' => 'Recognizes excellence in design principles, creative thinking, and the ability to create visually appealing and user-friendly interfaces.',
            'Business' => 'Certifies comprehensive understanding of business concepts and practical application of strategic thinking in real-world scenarios.',
            'General' => 'This certificate validates the successful completion of the course and demonstrates commitment to continuous learning and skill development.'
        ];

        return $descriptions[$category] ?? $descriptions['General'];
    }

    private function generateDownloadUrl($classId, $userId)
    {
        return url("/certificates/download/CERT-{$classId}-{$userId}");
    }

    private function generateShareUrl($classId, $userId)
    {
        return url("/certificates/share/CERT-{$classId}-{$userId}");
    }

    private function generateVerificationUrl($certificateId)
    {
        return url("/certificates/verify/{$certificateId}");
    }

    private function calculateTotalLearningHours($userId)
    {
        return rand(50, 200);
    }

    private function getCertificatesThisYear($userId)
    {
        $currentYear = Carbon::now()->year;
        
        return Enrollment::where('user_id', $userId)
            ->where('progress', '>=', 100)
            ->where('status', 'active')
            ->whereYear('updated_at', $currentYear)
            ->count();
    }

    private function calculateAverageGrade($userId)
    {
        return 'A-';
    }

    private function calculateCompletionRate($userId)
    {
        $totalEnrolled = Enrollment::where('user_id', $userId)
            ->where('status', 'active')
            ->count();

        $completed = Enrollment::where('user_id', $userId)
            ->where('progress', '>=', 100)
            ->where('status', 'active')
            ->count();

        return $totalEnrolled > 0 ? round(($completed / $totalEnrolled) * 100) : 0;
    }

    public function downloadCertificate(Request $request, $certificateId)
    {
        $user = $request->user();
        
        // Validate that the user owns this certificate
        if (!$this->validateCertificateOwnership($certificateId, $user->id)) {
            return response()->json(['error' => 'Certificate not found'], 404);
        }

        return response()->json([
            'message' => 'Certificate download initiated',
            'download_url' => '#',
            'certificate_id' => $certificateId
        ]);
    }

    public function shareCertificate(Request $request, $certificateId)
    {
        $user = $request->user();
        
        if (!$this->validateCertificateOwnership($certificateId, $user->id)) {
            return response()->json(['error' => 'Certificate not found'], 404);
        }

        $shareToken = $this->generateShareToken($certificateId);
        
        return response()->json([
            'share_url' => url("/certificates/public/{$shareToken}"),
            'expires_at' => Carbon::now()->addDays(30)->toISOString()
        ]);
    }

    public function verifyCertificate($certificateId)
    {
        $isValid = $this->validateCertificate($certificateId);
        
        if ($isValid) {
            $certificateData = $this->getCertificateData($certificateId);
            return response()->json([
                'valid' => true,
                'certificate' => $certificateData
            ]);
        }

        return response()->json([
            'valid' => false,
            'error' => 'Certificate not found or invalid'
        ], 404);
    }

    private function validateCertificateOwnership($certificateId, $userId)
    {
        $parts = explode('-', $certificateId);
        if (count($parts) !== 3 || $parts[0] !== 'CERT') {
            return false;
        }

        $classId = $parts[1];
        $certUserId = $parts[2];

        return $certUserId == $userId && 
               Enrollment::where('user_id', $userId)
                   ->where('class_id', $classId)
                   ->where('progress', '>=', 100)
                   ->exists();
    }

    private function validateCertificate($certificateId)
    {
        $parts = explode('-', $certificateId);
        if (count($parts) !== 3 || $parts[0] !== 'CERT') {
            return false;
        }

        $classId = $parts[1];
        $userId = $parts[2];

        return Enrollment::where('user_id', $userId)
            ->where('class_id', $classId)
            ->where('progress', '>=', 100)
            ->exists();
    }

    private function getCertificateData($certificateId)
    {
        $parts = explode('-', $certificateId);
        $classId = $parts[1];
        $userId = $parts[2];

        $enrollment = Enrollment::with(['class', 'class.teacher', 'user'])
            ->where('user_id', $userId)
            ->where('class_id', $classId)
            ->where('progress', '>=', 100)
            ->first();

        if (!$enrollment) {
            return null;
        }

        return [
            'certificate_id' => $certificateId,
            'student_name' => $enrollment->user->name,
            'course_title' => $enrollment->class->name,
            'instructor_name' => $enrollment->class->teacher->name ?? 'Pathshala Instructors',
            'completion_date' => $enrollment->updated_at->format('M d, Y'),
            'issue_date' => $enrollment->updated_at->format('M d, Y'),
            'status' => 'verified'
        ];
    }

    private function generateShareToken($certificateId)
    {
        return base64_encode($certificateId . '|' . Carbon::now()->timestamp);
    }

    public function getCertificatesData(Request $request)
    {
        $user = $request->user();
        
        $certificatesData = [
            'certificates' => $this->getUserCertificates($user->id),
            'stats' => $this->getCertificateStats($user->id)
        ];

        return response()->json($certificatesData);
    }
}