<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Content;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ContentManagementController extends Controller
{
    public function index()
    {
        try {
            // FIX: Use public method or direct query instead of protected method
            $content = $this->getAllContentWithDefaults('bn');
            
            return Inertia::render('Admin/ContentManagement/Index', [
                'content' => $content,
                'sections' => [
                    'about' => [
                        'title' => 'About Page',
                        'description' => 'Manage the content of your About page in both English and Bengali',
                        'fields' => [
                            'about_banner_title' => 'Banner Title',
                            'about_banner_description' => 'Banner Description',
                            'about_our_story_title' => 'Our Story Title',
                            'about_our_story_content' => 'Our Story Content',
                            'about_mission_title' => 'Mission Title',
                            'about_mission_content' => 'Mission Content',
                            'about_vision_title' => 'Vision Title',
                            'about_vision_content' => 'Vision Content',
                        ]
                    ],
                    'home' => [
                        'title' => 'Home Page',
                        'description' => 'Manage the content of your Home page in both English and Bengali',
                        'fields' => [
                            'home_hero_title' => 'Hero Title',
                            'home_hero_subtitle' => 'Hero Subtitle',
                            'home_hero_image' => 'Hero Image URL',
                            'home_hero_primary_button' => 'Hero Primary Button Text',
                            'home_hero_secondary_button' => 'Hero Secondary Button Text',
                            'home_courses_title' => 'Courses Section Title',
                            'home_courses_subtitle' => 'Courses Section Subtitle',
                            'home_courses_button' => 'Courses Button Text',
                            'home_instructors_title' => 'Instructors Section Title',
                            'home_instructors_subtitle' => 'Instructors Section Subtitle',
                            'home_instructors_button' => 'Instructors Button Text',
                            'home_stats_title' => 'Stats Section Title',
                            'home_cta_title' => 'CTA Section Title',
                            'home_cta_subtitle' => 'CTA Section Subtitle',
                            'home_cta_button' => 'CTA Button Text',
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Content management index error: ' . $e->getMessage());
            return Inertia::render('Admin/ContentManagement/Index', [
                'content' => [],
                'sections' => []
            ]);
        }
    }

        /**
     * Helper method to get all content with defaults
     */
    private function getAllContentWithDefaults($language = 'bn') // Default to Bengali
    {
        try {
            // Get all content from database
            $contentItems = Content::all();
            
            $content = [];
            foreach ($contentItems as $item) {
                // DEFAULT: Bengali is default, English is secondary
                if ($language === 'en') {
                    // For English: Use value (English content)
                    $content[$item->key] = $item->value;
                } else {
                    // For Bengali (default): Use value_bn if available, otherwise fallback to English value
                    if (!empty($item->value_bn) && trim($item->value_bn) !== '') {
                        $content[$item->key] = $item->value_bn;
                    } else {
                        $content[$item->key] = $item->value; // Fallback to English
                    }
                }
            }
            
            // Merge with defaults for any missing keys
            $defaults = $this->getDefaultContent($language);
            $mergedContent = array_merge($defaults, $content);
            
            return $mergedContent;
        } catch (\Exception $e) {
            Log::error('Error getting all content: ' . $e->getMessage());
            return $this->getDefaultContent($language);
        }
    }

    /**
     * Get default content structure with Bengali translations
     */
    private function getDefaultContent($language = 'bn')
    {
        $defaults = [
            // About Page Content
            'about_banner_title' => [
                'en' => 'About SkillGro',
                'bn' => 'স্কিলগ্রো সম্পর্কে'
            ],
            'about_banner_description' => [
                'en' => 'Empowering learners worldwide with quality education and innovative learning solutions',
                'bn' => 'মানসম্মত শিক্ষা এবং উদ্ভাবনী লার্নিং সমাধানের মাধ্যমে বিশ্বব্যাপী শিক্ষার্থীদের ক্ষমতায়ন'
            ],
            'about_our_story_title' => [
                'en' => 'Transforming Education Through Innovation',
                'bn' => 'উদ্ভাবনের মাধ্যমে শিক্ষার রূপান্তর'
            ],
            'about_our_story_content' => [
                'en' => 'SkillGro was founded with a simple yet powerful vision: to make quality education accessible to everyone, everywhere. We believe that learning should be engaging, personalized, and available to all regardless of geographical or financial barriers.',
                'bn' => 'স্কিলগ্রো একটি সহজ কিন্তু শক্তিশালী ভিশন নিয়ে প্রতিষ্ঠিত হয়েছিল: মানসম্মত শিক্ষা সবার জন্য, সর্বত্র সহজলভ্য করা। আমরা বিশ্বাস করি যে শিক্ষা আকর্ষণীয়, ব্যক্তিগতকৃত এবং ভৌগলিক বা আর্থিক বাধা নির্বিশেষে সবার জন্য উপলব্ধ হওয়া উচিত।'
            ],
            'about_mission_title' => [
                'en' => 'Our Mission',
                'bn' => 'আমাদের মিশন'
            ],
            'about_mission_content' => [
                'en' => 'To democratize education by providing high-quality, accessible, and affordable learning opportunities that empower individuals to achieve their personal and professional goals.',
                'bn' => 'উচ্চ-গুণমান, সহজলভ্য এবং সাশ্রয়ী মূল্যের শিক্ষার সুযোগ প্রদানের মাধ্যমে শিক্ষাকে গণতান্ত্রিক করা যা ব্যক্তিদের তাদের ব্যক্তিগত এবং পেশাদার লক্ষ্য অর্জনে সক্ষম করে।'
            ],
            'about_vision_title' => [
                'en' => 'Our Vision',
                'bn' => 'আমাদের ভিশন'
            ],
            'about_vision_content' => [
                'en' => 'To create a world where anyone, anywhere can transform their life through access to the world\'s best learning experiences and educational resources.',
                'bn' => 'এমন একটি বিশ্ব তৈরি করা যেখানে যে কেউ, যে কোনও জায়গায় বিশ্বের সেরা লার্নিং অভিজ্ঞতা এবং শিক্ষাগত সম্পদের অ্যাক্সেসের মাধ্যমে তাদের জীবন পরিবর্তন করতে পারে।'
            ],

            // Home Page Content
            'home_hero_title' => [
                'en' => 'Learning is What You Make of it. Make it Yours at SkillGro.',
                'bn' => 'শেখা হলো আপনার যা বানাতে চান। এটিকে আপনার নিজের করে নিন স্কিলগ্রোতে।'
            ],
            'home_hero_subtitle' => [
                'en' => 'Unlock your potential with our expert-led courses and transform your career.',
                'bn' => 'আমাদের বিশেষজ্ঞ-নির্দেশিত কোর্সের মাধ্যমে আপনার সম্ভাবনা উন্মুক্ত করুন এবং আপনার ক্যারিয়ার রূপান্তর করুন।'
            ],
            'home_hero_image' => [
                'en' => '/assets/img/h2_banner_img.png',
                'bn' => '/assets/img/h2_banner_img.png'
            ],
            'home_hero_primary_button' => [
                'en' => 'Start Free Trial',
                'bn' => 'বিনামূল্যে ট্রায়াল শুরু করুন'
            ],
            'home_hero_secondary_button' => [
                'en' => 'Watch Our Class Demo',
                'bn' => 'আমাদের ক্লাস ডেমো দেখুন'
            ],
            'home_courses_title' => [
                'en' => 'Popular Courses',
                'bn' => 'জনপ্রিয় কোর্সসমূহ'
            ],
            'home_courses_subtitle' => [
                'en' => 'Discover our most enrolled courses',
                'bn' => 'আমাদের সবচেয়ে নিবন্ধিত কোর্সগুলি আবিষ্কার করুন'
            ],
            'home_courses_button' => [
                'en' => 'View All Courses',
                'bn' => 'সমস্ত কোর্স দেখুন'
            ],
            'home_instructors_title' => [
                'en' => 'Meet Our Expert Instructors',
                'bn' => 'আমাদের বিশেষজ্ঞ ইন্সট্রাক্টরদের সাথে পরিচিত হোন'
            ],
            'home_instructors_subtitle' => [
                'en' => 'Learn from experienced professionals',
                'bn' => 'অভিজ্ঞ পেশাদারদের থেকে শিখুন'
            ],
            'home_instructors_button' => [
                'en' => 'View All Instructors',
                'bn' => 'সমস্ত ইন্সট্রাক্টর দেখুন'
            ],
            'home_stats_title' => [
                'en' => 'Our Impact',
                'bn' => 'আমাদের প্রভাব'
            ],
            'home_cta_title' => [
                'en' => 'Ready to Start Learning?',
                'bn' => 'শেখা শুরু করতে প্রস্তুত?'
            ],
            'home_cta_subtitle' => [
                'en' => 'Join thousands of students already learning with SkillGro',
                'bn' => 'ইতিমধ্যে স্কিলগ্রো দিয়ে শেখা হাজার হাজার শিক্ষার্থীর সাথে যোগ দিন'
            ],
            'home_cta_button' => [
                'en' => 'Get Started Today',
                'bn' => 'আজই শুরু করুন'
            ],
        ];

        // Return only the requested language
        $result = [];
        foreach ($defaults as $key => $translations) {
            $result[$key] = $translations[$language] ?? $translations['en'];
        }

        return $result;
    }

    public function getContent(Request $request, $language = 'bn')
    {
        try {
            $validLanguages = ['en', 'bn'];
            $lang = in_array($language, $validLanguages) ? $language : 'en';
            
            $content = $this->getAllContentWithDefaults($lang);
            
            return response()->json([
                'success' => true,
                'content' => $content,
                'language' => $lang
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch content'
            ], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
                'field_key' => 'required|string',
                'language' => 'required|in:en,bn'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get the uploaded file
            $image = $request->file('image');
            $fieldKey = $request->field_key;
            $language = $request->language;

            // Generate unique filename
            $filename = $fieldKey . '_' . $language . '_' . time() . '.' . $image->getClientOriginalExtension();
            
            // Store image in public disk
            $path = $image->storeAs('content-images', $filename, 'public');

            if (!$path) {
                throw new \Exception('Failed to store image');
            }

            // Generate public URL - FIXED: Use manual URL construction
            $imageUrl = $this->generateImageUrl($path);

            Log::info('Image uploaded successfully', [
                'field_key' => $fieldKey,
                'language' => $language,
                'path' => $path,
                'url' => $imageUrl
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'image_url' => $imageUrl,
                'path' => $path
            ]);

        } catch (\Exception $e) {
            Log::error('Image upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Generate URL for stored image
     */
    private function generateImageUrl($path)
    {
        // If using local storage, construct URL manually
        if (config('filesystems.default') === 'public') {
            return url('storage/' . $path);
        }
        
        // For other drivers (s3, etc.), use the Storage facade if available
        try {
            return Storage::url($path);
        } catch (\Exception $e) {
            // Fallback to manual URL construction
            return url('storage/' . $path);
        }
    }

    public function removeImage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'field_key' => 'required|string',
                'language' => 'required|in:en,bn'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fieldKey = $request->field_key;
            $language = $request->language;

            // Find the content entry to get the current image URL
            $content = Content::where('key', $fieldKey)->first();

            if ($content) {
                $currentImageUrl = $language === 'en' ? $content->value : $content->value_bn;
                
                // Extract filename from URL and delete the file
                if ($currentImageUrl && strpos($currentImageUrl, 'content-images') !== false) {
                    $filename = basename($currentImageUrl);
                    $filePath = 'content-images/' . $filename;
                    
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                        Log::info('Image file deleted', ['file_path' => $filePath]);
                    }
                }

                // Reset the content value to default
                $defaults = $this->getDefaultContent($language);
                $defaultValue = $defaults[$fieldKey] ?? '';

                if ($language === 'en') {
                    $content->value = $defaultValue;
                } else {
                    $content->value_bn = $defaultValue;
                }
                
                $content->save();
            }

            Log::info('Image removed successfully', [
                'field_key' => $fieldKey,
                'language' => $language
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image removed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Image removal error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enhanced save method to handle image URLs
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'value_bn' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->with('error', 'Validation failed');
        }

        try {
            Log::info('Saving content', [
                'key' => $request->key,
                'value' => $request->value,
                'value_bn' => $request->value_bn
            ]);
            
            // Use standard Eloquent updateOrCreate
            Content::updateOrCreate(
                ['key' => $request->key],
                [
                    'value' => $request->value,
                    'value_bn' => $request->value_bn
                ]
            );
            
            Log::info('Content saved successfully', ['key' => $request->key]);

            return back()->with('success', 'Content updated successfully');

        } catch (\Exception $e) {
            Log::error('Failed to save content: ' . $e->getMessage());
            return back()->with('error', 'Failed to update content');
        }
    }
}