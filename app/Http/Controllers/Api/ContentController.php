<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContentController extends Controller
{
    public function getAboutContent()
    {
        try {
            $content = Content::getAllContent();
            
            // Filter only about-related content
            $aboutContent = array_filter($content, function($key) {
                return strpos($key, 'about_') === 0;
            }, ARRAY_FILTER_USE_KEY);

            return response()->json($aboutContent);
        } catch (\Exception $e) {
            Log::error('Failed to fetch about content: ' . $e->getMessage());
            return response()->json(Content::getDefaultContent(), 500);
        }
    }

    public function getHomeContent()
    {
        try {
            $content = Content::getAllContent();
            
            // Filter only home-related content
            $homeContent = array_filter($content, function($key) {
                return strpos($key, 'home_') === 0;
            }, ARRAY_FILTER_USE_KEY);

            return response()->json($homeContent);
        } catch (\Exception $e) {
            Log::error('Failed to fetch home content: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    public function getAllContent()
    {
        try {
            $content = Content::getAllContent();
            return response()->json($content);
        } catch (\Exception $e) {
            Log::error('Failed to fetch all content: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    public function saveContent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string',
            'value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Content::updateOrCreate(
                ['key' => $request->key],
                ['value' => $request->value]
            );

            return response()->json([
                'success' => true,
                'message' => 'Content saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save content'
            ], 500);
        }
    }

    public function saveBulkContent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->content as $key => $value) {
                Content::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'All content saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save bulk content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save content'
            ], 500);
        }
    }
}