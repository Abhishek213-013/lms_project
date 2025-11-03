<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Content extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'value_bn'];
    
    protected $table = 'content_management';

    public $timestamps = true;

    /**
     * Get content by key with fallback and language support
     */
    public static function get($key, $default = null, $language = 'en')
    {
        $content = static::where('key', $key)->first();
        
        if ($content) {
            // Return Bengali value if requested and available
            if ($language === 'bn' && !empty($content->value_bn)) {
                return $content->value_bn;
            }
            return $content->value;
        }
        
        $defaults = self::getDefaultContent($language);
        return $default ?? $defaults[$key] ?? '';
    }

    /**
     * Get only about-related content
     */
    public static function getAboutContent($language = 'en')
    {
        $allContent = self::getAllContent($language);
        
        $aboutContent = array_filter($allContent, function($key) {
            return strpos($key, 'about_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        Log::info('About content filtered for language ' . $language . ':', $aboutContent);
        
        return $aboutContent;
    }

    /**
     * Get only home-related content
     */
    public static function getHomeContent($language = 'en')
    {
        $allContent = self::getAllContent($language);
        
        $homeContent = array_filter($allContent, function($key) {
            return strpos($key, 'home_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        Log::info('Home content filtered for language ' . $language . ':', $homeContent);
        
        return $homeContent;
    }

    /**
     * Get all content as key-value pairs with language support
     */
    public static function getAllContent($language = 'en')
    {
        try {
            Log::info('Fetching all content from database for language: ' . $language);
            
            // Get all content from database
            $contentItems = static::all();
            
            $content = [];
            foreach ($contentItems as $item) {
                // Use Bengali value if language is 'bn' and value_bn exists and is not empty
                if ($language === 'bn' && !empty($item->value_bn)) {
                    $content[$item->key] = $item->value_bn;
                } else {
                    $content[$item->key] = $item->value;
                }
            }
            
            Log::info('Database content found for language ' . $language . ':', $content);
            
            // Merge with defaults for any missing keys
            $defaults = self::getDefaultContent($language);
            $mergedContent = array_merge($defaults, $content);
            
            Log::info('Final merged content for language ' . $language . ':', $mergedContent);
            
            return $mergedContent;
        } catch (\Exception $e) {
            Log::error('Error getting all content for language ' . $language . ': ' . $e->getMessage());
            return self::getDefaultContent($language);
        }
    }

    /**
     * Get default content structure with Bengali translations
     */
    public static function getDefaultContent($language = 'en')
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

    /**
     * Get content with both languages
     */
    public static function getContentWithBothLanguages($key)
    {
        $content = static::where('key', $key)->first();
        
        if ($content) {
            return [
                'en' => $content->value,
                'bn' => $content->value_bn ?: self::getDefaultContent('bn')[$key] ?? ''
            ];
        }
        
        $defaults = self::getDefaultContent();
        return [
            'en' => $defaults[$key] ?? '',
            'bn' => self::getDefaultContent('bn')[$key] ?? ''
        ];
    }
}