<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    public function getYouTubeDirectStream(Request $request)
    {
        $videoId = $request->get('videoId');
        
        if (!$videoId) {
            return response()->json(['success' => false, 'message' => 'Video ID required']);
        }
        
        try {
            Log::info("Getting direct stream for video: {$videoId}");
            
            // Method 1: Use yt-dlp to get direct URL
            $directUrl = $this->getDirectUrlWithYtDlp($videoId);
            if ($directUrl) {
                Log::info("Successfully got direct URL for video: {$videoId}");
                return response()->json(['success' => true, 'directUrl' => $directUrl]);
            }
            
            // Method 2: Return a simple message for now
            Log::warning("Could not get direct URL for video: {$videoId}");
            return response()->json([
                'success' => false, 
                'message' => 'Direct streaming not available. Using fallback method.'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error getting direct stream for video {$videoId}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Service temporarily unavailable']);
        }
    }
    
    private function getDirectUrlWithYtDlp($videoId)
    {
        try {
            $url = "https://www.youtube.com/watch?v={$videoId}";
            $command = "yt-dlp -g --format best[height<=720] '{$url}' 2>/dev/null";
            $directUrl = shell_exec($command);
            
            if ($directUrl) {
                return trim($directUrl);
            }
        } catch (\Exception $e) {
            Log::error("yt-dlp error for video {$videoId}: " . $e->getMessage());
        }
        
        return null;
    }
    
    public function proxyVideo($videoId)
    {
        try {
            Log::info("Proxying video: {$videoId}");
            
            $url = "https://www.youtube.com/watch?v={$videoId}";
            $command = "yt-dlp -g --format best[height<=720] '{$url}' 2>/dev/null";
            $videoUrl = trim(shell_exec($command));
            
            if (!$videoUrl) {
                Log::warning("Video URL not found for: {$videoId}");
                abort(404, 'Video not found');
            }
            
            // For now, just return the URL instead of proxying
            // This avoids potential memory issues with large video files
            return response()->json([
                'success' => true,
                'videoUrl' => $videoUrl,
                'message' => 'Use this URL directly in video player'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Proxy error for video {$videoId}: " . $e->getMessage());
            abort(500, 'Error processing video request');
        }
    }
}