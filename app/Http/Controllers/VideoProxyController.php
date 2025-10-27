<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VideoProxyController extends Controller
{
    public function proxy(Request $request)
    {
        $url = $request->get('url');
        $videoId = $request->get('videoId');
        
        if (!$url || !$videoId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request: URL and videoId are required'
            ], 400);
        }
        
        // Generate a clean embed URL that hides YouTube branding
        $embedUrl = $this->generateCleanEmbedUrl($videoId);
        
        return response()->json([
            'success' => true,
            'embed_url' => $embedUrl,
            'video_id' => $videoId
        ]);
    }
    
    public function player(Request $request)
    {
        $videoId = $request->get('videoId');
        $title = $request->get('title', 'Video');
        
        if (!$videoId) {
            return response('Video ID required', 400);
        }
        
        $embedUrl = $this->generateCleanEmbedUrl($videoId);
        
        return view('video-player', [
            'embedUrl' => $embedUrl,
            'title' => $title,
            'videoId' => $videoId
        ]);
    }
    
    public function getEmbedUrl(Request $request)
    {
        $videoId = $request->get('videoId');
        
        if (!$videoId) {
            return response()->json([
                'success' => false,
                'message' => 'Video ID is required'
            ], 400);
        }
        
        $embedUrl = $this->generateCleanEmbedUrl($videoId);
        
        return response()->json([
            'success' => true,
            'embed_url' => $embedUrl
        ]);
    }
    
    public function processVideo(Request $request)
    {
        // This method can be used for future video processing features
        // For now, it just returns the clean embed URL
        
        $url = $request->get('url');
        $videoId = $request->get('videoId');
        
        if (!$url || !$videoId) {
            return response()->json([
                'success' => false,
                'message' => 'URL and videoId are required'
            ], 400);
        }
        
        $embedUrl = $this->generateCleanEmbedUrl($videoId);
        
        return response()->json([
            'success' => true,
            'embed_url' => $embedUrl,
            'processed' => true
        ]);
    }
    
    private function generateCleanEmbedUrl($videoId)
    {
        // Create a clean embed URL with maximum branding removal
        return "https://www.youtube-nocookie.com/embed/{$videoId}?" . http_build_query([
            'autoplay' => 1,
            'rel' => 0,
            'modestbranding' => 1,
            'controls' => 1,
            'showinfo' => 0,
            'iv_load_policy' => 3,
            'fs' => 1,
            'disablekb' => 1,
            'playsinline' => 1,
            'enablejsapi' => 1,
            'origin' => config('app.url'),
            'widget_referrer' => config('app.url'),
            'cc_load_policy' => 0,
            'color' => 'white'
        ]);
    }
}