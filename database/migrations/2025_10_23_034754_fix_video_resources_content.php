<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Fix video resources that have YouTube URLs in description but empty content
        DB::table('resources')
            ->where('type', 'video')
            ->where(function($query) {
                $query->whereNull('content')
                      ->orWhere('content', '')
                      ->orWhere('content', 'LIKE', '%video-link.txt%');
            })
            ->where('description', 'LIKE', '%youtu%')
            ->get()
            ->each(function ($resource) {
                // Extract YouTube URL from description
                preg_match('/(https?:\/\/(www\.)?(youtube\.com|youtu\.be)\/[^\s]+)/', $resource->description, $matches);
                
                if (isset($matches[1])) {
                    $youtubeUrl = $matches[1];
                    
                    DB::table('resources')
                        ->where('id', $resource->id)
                        ->update([
                            'content' => $youtubeUrl,
                            'file_path' => null, // Remove the text file reference
                            'updated_at' => now(),
                        ]);
                    
                    echo "Fixed resource {$resource->id}: {$youtubeUrl}\n";
                }
            });
    }

    public function down()
    {
        // This migration cannot be easily reversed
    }
};