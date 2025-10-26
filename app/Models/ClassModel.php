<?php
// app/Models/ClassModel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    use HasFactory;

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
        'category'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'grade' => 'integer',
        'schedule' => 'array'
    ];

    /**
     * Get the teacher that owns the class.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the students for the class (many-to-many relationship).
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'student_id')
                    ->withTimestamps();
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
}