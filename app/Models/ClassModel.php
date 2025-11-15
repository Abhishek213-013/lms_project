<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ClassModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'subject',
        'grade',
        'code',
        'teacher_id',
        'capacity',
        'status',
        'description',
        'type',
        'category',
        'image',
        'thumbnail',
        'image_alt',
        'image_caption'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'grade' => 'integer',
        'schedule' => 'array'
    ];

    protected $appends = ['image_url', 'thumbnail_url']; // Add this line

    /**
     * Get the teacher that owns the class.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the students for the class.
     */
    public function students(): BelongsToMany
    {
        return $this->enrolledStudents();
    }

    /**
     * Get the resources for the class.
     */
    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class, 'class_id');
    }

    /**
     * Scope for regular classes
     */
    public function scopeRegular($query)
    {
        return $query->where('type', 'regular');
    }

    /**
     * Scope for other courses
     */
    public function scopeOther($query)
    {
        return $query->where('type', 'other');
    }

    /**
     * Scope for active classes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the full URL for the image
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
        // Use a different fallback or create the file
            return asset('images/default-course.jpg'); // or
            return null; // or
            return asset('storage/courses/default-course.jpg');
        }
        
        // Check if it's already a full URL
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        
        // Return storage URL using asset helper
        return asset('storage/' . $this->image);
    }

    /**
     * Get the full URL for the thumbnail
     */
    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail) {
            return $this->image_url; // Fallback to main image
        }
        
        // Check if it's already a full URL
        if (str_starts_with($this->thumbnail, 'http')) {
            return $this->thumbnail;
        }
        
        // Return storage URL using asset helper
        return asset('storage/' . $this->thumbnail);
    }

    /**
     * Check if image exists in storage
     */
    public function imageExists()
    {
        if (!$this->image || str_starts_with($this->image, 'http')) {
            return false;
        }
        
        return Storage::disk('public')->exists($this->image);
    }

    /**
     * Check if thumbnail exists in storage
     */
    public function thumbnailExists()
    {
        if (!$this->thumbnail || str_starts_with($this->thumbnail, 'http')) {
            return false;
        }
        
        return Storage::disk('public')->exists($this->thumbnail);
    }

        /**
     * Get the students enrolled in this subject.
     */
    public function enrolledStudents(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'class_student', 'class_id', 'student_id')
                    ->withTimestamps()
                    ->withPivot('progress', 'last_accessed', 'last_activity_type');
    }

}