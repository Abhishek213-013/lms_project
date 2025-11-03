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
            // Get all existing content
            $content = Content::getAllContent();
            
            return Inertia::render('Admin/ContentManagement/Index', [
                'content' => $content,
                'sections' => [
                    'about' => [
                        'title' => 'About Page',
                        'description' => 'Manage the content of your About page',
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
                        'description' => 'Manage the content of your Home page',
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

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->with('error', 'Validation failed');
        }

        try {
            Log::info('Saving content', ['key' => $request->key]);
            
            Content::updateOrCreate(
                ['key' => $request->key],
                ['value' => $request->value]
            );
            
            Log::info('Content saved successfully', ['key' => $request->key]);

            return back()->with('success', 'Content updated successfully');

        } catch (\Exception $e) {
            Log::error('Failed to save content: ' . $e->getMessage());
            return back()->with('error', 'Failed to update content');
        }
    }

    // Handle image uploads specifically
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120', // 5MB max
            'key' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            // Generate unique filename
            $filename = 'content-' . time() . '-' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            
            // Store the image
            $path = $request->file('image')->storeAs('content-images', $filename, 'public');
            $url = Storage::url($path);

            // Save the image URL to content management
            Content::updateOrCreate(
                ['key' => $request->key],
                ['value' => $url]
            );

            Log::info('Image uploaded successfully', ['key' => $request->key, 'url' => $url]);

            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
                'message' => 'Image uploaded successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Image upload error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload image: ' . $e->getMessage()], 500);
        }
    }

    // Remove uploaded image
    public function removeImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            // Get the current content
            $content = Content::where('key', $request->key)->first();
            
            if ($content && $content->value) {
                // Extract path from URL and delete the file
                $path = str_replace('/storage/', '', $content->value);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
                
                // Reset to default image
                $defaults = Content::getDefaultContent();
                $defaultValue = $defaults[$request->key] ?? '';
                
                $content->update(['value' => $defaultValue]);
                
                Log::info('Image removed successfully', ['key' => $request->key]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Image removed successfully',
                    'default_value' => $defaultValue
                ]);
            }

            return response()->json(['error' => 'Image not found'], 404);

        } catch (\Exception $e) {
            Log::error('Image removal error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to remove image'], 500);
        }
    }
}