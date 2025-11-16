<?php
// app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'academic_class_id', // Use this instead of class_id
        // 'class_id', // Remove this if you're removing the column
        'roll_number',
        'father_name',
        'mother_name',
        'parent_contact',
        'country_code',
        'address',
        'admission_date',
        'phone',
        'bio',
        'location',
        'profile_picture',
        'status'
    ];

    protected $casts = [
        'admission_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['profile_picture_url', 'full_parent_contact'];

    /**
     * Get the user that owns the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the academic class that the student belongs to.
     */
    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'academic_class_id');
    }

    /**
     * Get the subjects (classes) that the student is enrolled in.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(ClassModel::class, 'class_student', 'student_id', 'class_id')
                    ->withTimestamps()
                    ->withPivot('progress', 'last_accessed', 'last_activity_type');
    }

    // Remove or comment out the old class relationship if removing class_id
    // public function class(): BelongsTo
    // {
    //     return $this->belongsTo(ClassModel::class, 'class_id');
    // }

    /**
     * Get full parent contact number with country code
     */
    public function getFullParentContactAttribute(): string
    {
        return $this->country_code . $this->parent_contact;
    }

    /**
     * Get profile picture URL
     */
    public function getProfilePictureUrlAttribute()
    {
        if (!$this->profile_picture) {
            return null;
        }
        
        // Check if it's already a full URL
        if (str_starts_with($this->profile_picture, 'http')) {
            return $this->profile_picture;
        }
        
        // Generate the storage URL
        return asset('storage/profile-pictures/' . $this->profile_picture);
    }

    /**
     * Scope active students
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Generate roll number based on academic class
     */
    public static function generateRollNumber($academicClassId)
    {
        $academicClass = AcademicClass::find($academicClassId);
        $studentCount = self::where('academic_class_id', $academicClassId)->count() + 1;
        
        return 'ST' . date('Y') . 
               str_pad($academicClass->grade, 2, '0', STR_PAD_LEFT) . 
               str_pad($studentCount, 3, '0', STR_PAD_LEFT);
    }
}