<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Content extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];
    
    protected $table = 'content_management';

    public $timestamps = true;

    /**
     * Get all content as key-value pairs
     */
    public static function getAllContent()
    {
        try {
            Log::info('Fetching all content from database');
            
            $content = static::pluck('value', 'key')->toArray();
            Log::info('Database content found:', $content);
            
            // Merge with defaults for any missing keys
            $defaults = static::getDefaultContent();
            $mergedContent = array_merge($defaults, $content);
            
            Log::info('Final merged content:', $mergedContent);
            
            return $mergedContent;
        } catch (\Exception $e) {
            Log::error('Error getting all content: ' . $e->getMessage());
            return static::getDefaultContent();
        }
    }

    /**
     * Get default content structure
     */
    public static function getDefaultContent()
    {
        return [
            // About Page Content
            'about_banner_title' => 'About SkillGro',
            'about_banner_description' => 'Empowering learners worldwide with quality education and innovative learning solutions',
            'about_our_story_title' => 'Transforming Education Through Innovation',
            'about_our_story_content' => 'SkillGro was founded with a simple yet powerful vision: to make quality education accessible to everyone, everywhere. We believe that learning should be engaging, personalized, and available to all regardless of geographical or financial barriers.',
            'about_mission_title' => 'Our Mission',
            'about_mission_content' => 'To democratize education by providing high-quality, accessible, and affordable learning opportunities that empower individuals to achieve their personal and professional goals.',
            'about_vision_title' => 'Our Vision',
            'about_vision_content' => 'To create a world where anyone, anywhere can transform their life through access to the world\'s best learning experiences and educational resources.',

            // Home Page Content
            'home_hero_title' => 'Learning is What You Make of it. Make it Yours at SkillGro.',
            'home_hero_subtitle' => 'Unlock your potential with our expert-led courses and transform your career.',
            'home_hero_primary_button' => 'Start Free Trial',
            'home_hero_secondary_button' => 'Watch Our Class Demo',
            'home_courses_title' => 'Popular Courses',
            'home_courses_subtitle' => 'Discover our most enrolled courses',
            'home_courses_button' => 'View All Courses',
            'home_instructors_title' => 'Meet Our Expert Instructors',
            'home_instructors_subtitle' => 'Learn from experienced professionals',
            'home_instructors_button' => 'View All Instructors',
            'home_stats_title' => 'Our Impact',
            'home_cta_title' => 'Ready to Start Learning?',
            'home_cta_subtitle' => 'Join thousands of students already learning with SkillGro',
            'home_cta_button' => 'Get Started Today',
        ];
    }

    /**
     * Get content by key with fallback
     */
    public static function get($key, $default = null)
    {
        $content = static::where('key', $key)->first();
        return $content ? $content->value : ($default ?? static::getDefaultContent()[$key] ?? '');
    }

    /**
     * Update or create content
     */
    public static function updateOrCreateContent($key, $value)
    {
        try {
            Log::info("Saving content: {$key} => {$value}");
            
            $result = static::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            
            Log::info("Content saved successfully: {$key}", ['id' => $result->id]);
            return $result;
            
        } catch (\Exception $e) {
            Log::error("Failed to save content for key {$key}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get only about-related content
     */
    public static function getAboutContent()
    {
        $allContent = static::getAllContent();
        
        $aboutContent = array_filter($allContent, function($key) {
            return strpos($key, 'about_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        Log::info('About content filtered:', $aboutContent);
        
        return $aboutContent;
    }

    /**
     * Get only home-related content
     */
    public static function getHomeContent()
    {
        $allContent = static::getAllContent();
        
        $homeContent = array_filter($allContent, function($key) {
            return strpos($key, 'home_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        Log::info('Home content filtered:', $homeContent);
        
        return $homeContent;
    }
}