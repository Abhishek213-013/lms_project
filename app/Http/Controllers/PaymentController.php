<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use App\Models\Announcement; // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Process mobile payment
     */
    public function processMobilePayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'course_id' => 'required|exists:classes,id',
                'amount' => 'required|numeric',
                'payment_method' => 'required|string|in:bkash,nagad,rocket,upay',
                'payment_details' => 'required|array',
                'payment_details.phoneNumber' => 'required|string',
                'payment_details.transactionId' => 'required|string',
                'additional_services' => 'array',
                'coupon_code' => 'nullable|string'
            ]);

            DB::transaction(function () use ($validated) {
                // Get student record
                $student = Student::where('user_id', auth()->id())->first();
                
                if (!$student) {
                    throw new \Exception('Student record not found');
                }

                // Create payment record
                $payment = Payment::create([
                    'student_id' => $student->id,
                    'class_id' => $validated['course_id'],
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'transaction_id' => $validated['payment_details']['transactionId'],
                    'phone_number' => $validated['payment_details']['phoneNumber'],
                    'status' => 'completed',
                    'payment_details' => $validated['payment_details'],
                    'additional_services' => $validated['additional_services'],
                    'coupon_code' => $validated['coupon_code'],
                    'verified_at' => now(), // Auto-verify mobile payments
                    'verified_by' => auth()->id() // Current admin user ID
                ]);

                // Enroll student in the course
                $this->enrollStudent($student->id, $validated['course_id']);

                // Create announcement for successful enrollment
                $this->createPaymentSuccessAnnouncement($student, $validated['course_id'], $payment);

                Log::info('Mobile payment processed successfully', [
                    'payment_id' => $payment->id,
                    'student_id' => $student->id,
                    'class_id' => $validated['course_id']
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully! You are now enrolled in the course.'
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile payment processing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process bank transfer payment
     */
    public function processBankTransfer(Request $request)
    {
        try {
            $validated = $request->validate([
                'course_id' => 'required|exists:classes,id',
                'amount' => 'required|numeric',
                'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'additional_services' => 'array',
                'coupon_code' => 'nullable|string'
            ]);

            DB::transaction(function () use ($validated, $request) {
                // Get student record
                $student = Student::where('user_id', auth()->id())->first();
                
                if (!$student) {
                    throw new \Exception('Student record not found');
                }

                // Store receipt file
                $receiptPath = $request->file('receipt')->store('payment-receipts', 'public');

                // Create pending payment record
                $payment = Payment::create([
                    'student_id' => $student->id,
                    'class_id' => $validated['course_id'],
                    'amount' => $validated['amount'],
                    'payment_method' => 'bank_transfer',
                    'status' => 'pending',
                    'receipt_path' => $receiptPath,
                    'additional_services' => $validated['additional_services'],
                    'coupon_code' => $validated['coupon_code']
                ]);

                // Create announcement for pending payment
                $this->createPaymentPendingAnnouncement($student, $validated['course_id'], $payment);

                Log::info('Bank transfer payment submitted', [
                    'payment_id' => $payment->id,
                    'student_id' => $student->id,
                    'class_id' => $validated['course_id']
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment receipt uploaded successfully! Your enrollment will be activated after verification (within 24 hours).'
            ]);

        } catch (\Exception $e) {
            Log::error('Bank transfer payment processing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enroll student in class
     */
    private function enrollStudent($studentId, $classId)
    {
        // Check if already enrolled
        $existingEnrollment = DB::table('class_student')
            ->where('student_id', $studentId)
            ->where('class_id', $classId)
            ->first();

        if (!$existingEnrollment) {
            DB::table('class_student')->insert([
                'student_id' => $studentId,
                'class_id' => $classId,
                'enrolled_at' => now(),
                'progress' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Student enrolled in class', [
                'student_id' => $studentId,
                'class_id' => $classId
            ]);
        } else {
            Log::info('Student already enrolled in class', [
                'student_id' => $studentId,
                'class_id' => $classId
            ]);
        }
    }

    /**
     * Verify payment (for admin)
     */
    public function verifyPayment(Request $request, $paymentId)
    {
        try {
            $payment = Payment::findOrFail($paymentId);
            
            if ($payment->status !== 'pending') {
                throw new \Exception('Payment is not pending verification');
            }

            DB::transaction(function () use ($payment) {
                $payment->update([
                    'status' => 'completed',
                    'verified_at' => now(),
                    'verified_by' => auth()->id()
                ]);

                // Enroll student after verification
                $this->enrollStudent($payment->student_id, $payment->class_id);

                // Create announcement for successful verification
                $this->createPaymentVerifiedAnnouncement($payment);
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Payment verification error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject payment (for admin)
     */
    public function rejectPayment(Request $request, $paymentId)
    {
        try {
            $payment = Payment::findOrFail($paymentId);
            
            if ($payment->status !== 'pending') {
                throw new \Exception('Payment is not pending verification');
            }

            $payment->update([
                'status' => 'rejected',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
                'rejection_reason' => $request->input('reason', 'No reason provided')
            ]);

            // Create announcement for rejected payment
            $this->createPaymentRejectedAnnouncement($payment);

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Payment rejection error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment rejection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create announcement for successful payment verification
     */
    private function createPaymentVerifiedAnnouncement($payment)
    {
        try {
            $student = Student::with('user')->find($payment->student_id);
            $course = ClassModel::find($payment->class_id);

            if (!$student || !$course) {
                Log::warning('Could not create payment announcement: Student or course not found');
                return;
            }

            $bengaliDate = $this->getBengaliDate();

            $announcement = new Announcement();
            $announcement->title = "Payment Verified: {$student->user->name} enrolled in {$course->name}";
            $announcement->title_bn = "পেমেন্ট যাচাইকৃত: {$student->user->name} {$course->name} এ ভর্তি হয়েছেন";
            $announcement->content = "Great news! {$student->user->name}'s payment has been verified and they are now officially enrolled in {$course->name}. Welcome to the class!";
            $announcement->content_bn = "খুশির খবর! {$student->user->name} এর পেমেন্ট যাচাই করা হয়েছে এবং তারা এখন আনুষ্ঠানিকভাবে {$course->name} এ ভর্তি হয়েছেন। ক্লাসে স্বাগতম!";
            $announcement->date = now()->toDateString();
            $announcement->date_bn = $bengaliDate;
            $announcement->type = 'payment_verified';
            $announcement->related_id = $payment->id;
            $announcement->related_type = 'payment';
            $announcement->is_active = true;

            $announcement->save();

            Log::info('Payment verification announcement created', [
                'announcement_id' => $announcement->id,
                'payment_id' => $payment->id,
                'student_id' => $student->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating payment verification announcement: ' . $e->getMessage());
        }
    }

    /**
     * Create announcement for pending payment
     */
    private function createPaymentPendingAnnouncement($student, $courseId, $payment)
    {
        try {
            $course = ClassModel::find($courseId);

            if (!$course) {
                return;
            }

            $bengaliDate = $this->getBengaliDate();

            $announcement = new Announcement();
            $announcement->title = "Payment Under Review: {$student->user->name} applied for {$course->name}";
            $announcement->title_bn = "পেমেন্ট পর্যালোচনাধীন: {$student->user->name} {$course->name} এর জন্য আবেদন করেছেন";
            $announcement->content = "{$student->user->name} has submitted a payment for {$course->name}. The payment is currently under verification and will be processed within 24 hours.";
            $announcement->content_bn = "{$student->user->name} {$course->name} এর জন্য পেমেন্ট জমা দিয়েছেন। পেমেন্টটি বর্তমানে যাচাইকরণাধীন এবং ২৪ ঘন্টার মধ্যে প্রক্রিয়া করা হবে।";
            $announcement->date = now()->toDateString();
            $announcement->date_bn = $bengaliDate;
            $announcement->type = 'payment_pending';
            $announcement->related_id = $payment->id;
            $announcement->related_type = 'payment';
            $announcement->is_active = true;

            $announcement->save();

        } catch (\Exception $e) {
            Log::error('Error creating pending payment announcement: ' . $e->getMessage());
        }
    }

    /**
     * Create announcement for successful mobile payment
     */
    private function createPaymentSuccessAnnouncement($student, $courseId, $payment)
    {
        try {
            $course = ClassModel::find($courseId);

            if (!$course) {
                return;
            }

            $bengaliDate = $this->getBengaliDate();

            $announcement = new Announcement();
            $announcement->title = "Instant Enrollment: {$student->user->name} joined {$course->name}";
            $announcement->title_bn = "তাত্ক্ষণিক ভর্তি: {$student->user->name} {$course->name} এ যোগদান করেছেন";
            $announcement->content = "Welcome {$student->user->name}! You have successfully enrolled in {$course->name} through instant payment. Start learning now!";
            $announcement->content_bn = "স্বাগতম {$student->user->name}! আপনি তাত্ক্ষণিক পেমেন্টের মাধ্যমে {$course->name} এ সফলভাবে ভর্তি হয়েছেন। এখনই শেখা শুরু করুন!";
            $announcement->date = now()->toDateString();
            $announcement->date_bn = $bengaliDate;
            $announcement->type = 'payment_success';
            $announcement->related_id = $payment->id;
            $announcement->related_type = 'payment';
            $announcement->is_active = true;

            $announcement->save();

        } catch (\Exception $e) {
            Log::error('Error creating payment success announcement: ' . $e->getMessage());
        }
    }

    /**
     * Create announcement for rejected payment
     */
    private function createPaymentRejectedAnnouncement($payment)
    {
        try {
            $student = Student::with('user')->find($payment->student_id);
            $course = ClassModel::find($payment->class_id);

            if (!$student || !$course) {
                return;
            }

            $bengaliDate = $this->getBengaliDate();

            $announcement = new Announcement();
            $announcement->title = "Payment Issue: {$student->user->name}'s payment for {$course->name} was rejected";
            $announcement->title_bn = "পেমেন্ট সমস্যা: {$student->user->name} এর {$course->name} এর জন্য পেমেন্ট প্রত্যাখ্যান করা হয়েছে";
            $announcement->content = "We encountered an issue with {$student->user->name}'s payment for {$course->name}. Please contact support for assistance.";
            $announcement->content_bn = "আমরা {$student->user->name} এর {$course->name} এর পেমেন্টে একটি সমস্যা পেয়েছি। সহায়তার জন্য সাপোর্টে যোগাযোগ করুন।";
            $announcement->date = now()->toDateString();
            $announcement->date_bn = $bengaliDate;
            $announcement->type = 'payment_rejected';
            $announcement->related_id = $payment->id;
            $announcement->related_type = 'payment';
            $announcement->is_active = true;

            $announcement->save();

        } catch (\Exception $e) {
            Log::error('Error creating payment rejected announcement: ' . $e->getMessage());
        }
    }

    /**
     * Get Bengali date
     */
    private function getBengaliDate()
    {
        $englishMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $bengaliMonths = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
        
        $date = now();
        $bengaliMonth = $bengaliMonths[$date->month - 1];
        
        return $date->format('d') . ' ' . $bengaliMonth . ' ' . $date->format('Y');
    }

    // ... rest of your existing methods (getPaymentHistory, getPendingPayments, getPaymentStats)

    /**
     * Get payment history for student
     */
    public function getPaymentHistory(Request $request)
    {
        try {
            $student = Student::where('user_id', auth()->id())->first();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            $payments = Payment::with(['class' => function($query) {
                    $query->select('id', 'name', 'subject', 'grade', 'type');
                }])
                ->where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);

        } catch (\Exception $e) {
            Log::error('Get payment history error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment history'
            ], 500);
        }
    }

    /**
     * Get pending payments for admin
     */
    public function getPendingPayments()
    {
        try {
            $payments = Payment::with(['student.user', 'class'])
                ->where('status', 'pending')
                ->where('payment_method', 'bank_transfer')
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'student_name' => $payment->student->user->name ?? 'N/A',
                        'student_email' => $payment->student->user->email ?? 'N/A',
                        'course_name' => $payment->class->name ?? 'N/A',
                        'course_subject' => $payment->class->subject ?? 'N/A',
                        'amount' => $payment->amount,
                        'receipt_url' => $payment->receipt_url,
                        'created_at' => $payment->created_at,
                        'payment_details' => $payment->payment_details,
                        'additional_services' => $payment->additional_services,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);

        } catch (\Exception $e) {
            Log::error('Get pending payments error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending payments'
            ], 500);
        }
    }

    /**
     * Get payment stats for admin
     */
    public function getPaymentStats()
    {
        try {
            $pendingPayments = Payment::where('status', 'pending')->count();
            $verifiedToday = Payment::where('status', 'completed')
                ->whereDate('verified_at', today())
                ->count();
            $totalPendingAmount = Payment::where('status', 'pending')->sum('amount');
            $bankTransfers = Payment::where('payment_method', 'bank_transfer')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'pendingPayments' => $pendingPayments,
                    'verifiedToday' => $verifiedToday,
                    'totalPendingAmount' => $totalPendingAmount,
                    'bankTransfers' => $bankTransfers,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get payment stats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment stats'
            ], 500);
        }
    }
}