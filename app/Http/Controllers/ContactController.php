<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    // Display contact page
    public function show(): Response
    {
        return Inertia::render('Frontend/Contact', [
            'meta' => [
                'title' => 'Contact Us - SkillGro',
                'description' => 'Get in touch with SkillGro. We\'re here to help with any questions about our courses and learning platform.'
            ],
            'contactInfo' => $this->getContactInfo(),
            'supportHours' => $this->getSupportHours()
        ]);
    }

    // API: Submit contact form
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|in:general,technical,billing,support,other'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check the form for errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Save contact submission to database (if you have a contacts table)
            // Contact::create([...]);
            
            $contactData = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'phone' => $request->phone,
                'department' => $request->department ?? 'general',
                'submitted_at' => now()->toISOString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];

            // Send email notification (uncomment when ready)
            // $this->sendContactEmail($contactData);

            // Log the contact for admin review
            Log::info('Contact form submission', $contactData);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message! We will get back to you within 24 hours.',
                'data' => [
                    'submission_id' => uniqid('contact_'),
                    'submitted_at' => $contactData['submitted_at'],
                    'department' => $contactData['department']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Contact form submission failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'We encountered an error while submitting your message. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Get FAQ data for contact page
    public function getFaqs()
    {
        try {
            $faqs = $this->getFaqData();

            return response()->json([
                'success' => true,
                'data' => $faqs
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching FAQs: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load FAQs'
            ], 500);
        }
    }

    // Data methods for Inertia
    private function getContactInfo()
    {
        return [
            'email' => 'support@skillgro.com',
            'phone' => '+1 (555) 123-4567',
            'address' => [
                'street' => '123 Education Street',
                'city' => 'Learning City',
                'state' => 'LC',
                'zip' => '12345',
                'country' => 'United States'
            ],
            'social_media' => [
                'facebook' => 'https://facebook.com/skillgro',
                'twitter' => 'https://twitter.com/skillgro',
                'linkedin' => 'https://linkedin.com/company/skillgro',
                'instagram' => 'https://instagram.com/skillgro'
            ],
            'departments' => [
                ['value' => 'general', 'label' => 'General Inquiry'],
                ['value' => 'technical', 'label' => 'Technical Support'],
                ['value' => 'billing', 'label' => 'Billing & Payments'],
                ['value' => 'support', 'label' => 'Student Support'],
                ['value' => 'other', 'label' => 'Other']
            ]
        ];
    }

    private function getSupportHours()
    {
        return [
            'monday_friday' => '9:00 AM - 6:00 PM',
            'saturday' => '10:00 AM - 4:00 PM',
            'sunday' => 'Closed',
            'timezone' => 'EST',
            'emergency_support' => '24/7 for technical issues'
        ];
    }

    private function getFaqData()
    {
        return [
            [
                'id' => 1,
                'question' => 'How do I enroll in a course?',
                'answer' => 'You can enroll in any course by visiting the course page and clicking the "Enroll Now" button. You\'ll need to create an account first if you don\'t have one.',
                'category' => 'enrollment'
            ],
            [
                'id' => 2,
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept all major credit cards, PayPal, and bank transfers. All payments are processed securely through our payment partners.',
                'category' => 'billing'
            ],
            [
                'id' => 3,
                'question' => 'Can I access courses on mobile devices?',
                'answer' => 'Yes! Our platform is fully responsive and works on all devices including smartphones, tablets, and desktop computers.',
                'category' => 'technical'
            ],
            [
                'id' => 4,
                'question' => 'What if I need help during my course?',
                'answer' => 'We provide multiple support channels including direct messaging with instructors, community forums, and 24/7 technical support for platform issues.',
                'category' => 'support'
            ],
            [
                'id' => 5,
                'question' => 'Do you offer certificates upon completion?',
                'answer' => 'Yes, all completed courses include a downloadable certificate that you can share on LinkedIn and other professional platforms.',
                'category' => 'certification'
            ],
            [
                'id' => 6,
                'question' => 'Can I get a refund if I\'m not satisfied?',
                'answer' => 'We offer a 30-day money-back guarantee for all courses. If you\'re not satisfied, contact our support team for a full refund.',
                'category' => 'billing'
            ]
        ];
    }

    // Email sending method (ready for production)
    private function sendContactEmail($contactData)
    {
        try {
            Mail::send('emails.contact-notification', $contactData, function($message) use ($contactData) {
                $message->to('support@skillgro.com')
                        ->subject('New Contact Form Submission: ' . $contactData['subject'])
                        ->replyTo($contactData['email'], $contactData['name']);
            });

            // Optional: Send confirmation email to user
            Mail::send('emails.contact-confirmation', $contactData, function($message) use ($contactData) {
                $message->to($contactData['email'])
                        ->subject('We\'ve received your message - SkillGro')
                        ->from('noreply@skillgro.com', 'SkillGro Support');
            });

        } catch (\Exception $e) {
            Log::error('Failed to send contact email: ' . $e->getMessage());
            // Don't fail the entire request if email fails
        }
    }

    // API: Get contact information (for footer/header)
    public function getContactInformation()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'contact_info' => $this->getContactInfo(),
                    'support_hours' => $this->getSupportHours()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching contact information: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load contact information'
            ], 500);
        }
    }
}