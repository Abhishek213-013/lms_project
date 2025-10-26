<?php
// app/Models/Resource.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
    'teacher_id',
    'class_id',
    'type',
    'title',
    'description',
    'content',
    'file_path',
    'thumbnail_path',
    'download_count', // Add this
    'status'
];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Add accessors to make these available as attributes
    protected $appends = ['thumbnail_url', 'file_url', 'file_size', 'is_youtube', 'youtube_video_id', 'youtube_embed_url'];

    /**
     * Get the teacher that owns the resource.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the class that owns the resource.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_path) {
            // Check if it's a YouTube thumbnail reference
            if (str_starts_with($this->thumbnail_path, 'youtube_')) {
                $videoId = str_replace('youtube_', '', $this->thumbnail_path);
                return "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
            }
            
            // Check if it's a stored file - use asset() for public disk URLs
            if (Storage::disk('public')->exists($this->thumbnail_path)) {
                return asset('storage/' . $this->thumbnail_path);
            }
        }
        
        // Default thumbnail based on type
        if ($this->type === 'video') {
            return asset('/images/default-video-thumbnail.jpg');
        } elseif ($this->type === 'pdf') {
            return asset('/images/default-pdf-thumbnail.jpg');
        }
        
        return asset('/images/default-resource-thumbnail.jpg');
    }

    /**
     * Get the file URL
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    /**
     * Get the file size in human readable format
     */
    public function getFileSizeAttribute()
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            $bytes = Storage::disk('public')->size($this->file_path);
            return $this->formatFileSize($bytes);
        }
        return null;
    }

    /**
     * Format file size
     */
    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Check if resource has a YouTube video
     */
    public function getIsYouTubeAttribute()
    {
        if ($this->type !== 'video') return false;
        
        $content = $this->content;
        if (!$content) return false;
        
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get YouTube video ID
     */
    public function getYouTubeVideoIdAttribute()
    {
        if (!$this->is_youtube) return null;
        
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->content, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }

    /**
     * Get YouTube embed URL
     */
    public function getYouTubeEmbedUrlAttribute()
    {
        if (!$this->youtube_video_id) return null;
        
        return "https://www.youtube.com/embed/{$this->youtube_video_id}";
    }

    /**
     * Scope for video resources
     */
    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    /**
     * Scope for PDF resources
     */
    public function scopePdfs($query)
    {
        return $query->where('type', 'pdf');
    }

    /**
     * Scope for class resources
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope for teacher resources
     */
    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }
}