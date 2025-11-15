<?php
// app/Models/AcademicClass.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'grade',
        'section',
        'description',
        'capacity',
        'status'
    ];

    protected $casts = [
        'grade' => 'integer',
        'capacity' => 'integer'
    ];

    /**
     * Get the students for this academic class.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'academic_class_id');
    }

    /**
     * Get the subjects (classes) for this academic class.
     */
    public function subjects()
    {
        return $this->hasMany(ClassModel::class, 'grade', 'grade');
    }

    /**
     * Scope active classes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get student count
     */
    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }

    /**
     * Get available seats
     */
    public function getAvailableSeatsAttribute()
    {
        return $this->capacity - $this->student_count;
    }
}