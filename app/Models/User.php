<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'dob',
        'education_qualification',
        'institute',
        'password',
        'role',
        'experience',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function getEmailVerifiedAtAttribute()
    {
        return null;
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Get the classes taught by the teacher.
     */
    public function taughtClasses(): HasMany
    {
        return $this->hasMany(ClassModel::class, 'teacher_id');
    }

    /**
     * Get the resources uploaded by the teacher.
     */
    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class, 'teacher_id');
    }

    /**
     * Get the student record associated with the user.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the classes where this user is a student (if applicable)
     */
    public function enrolledClasses()
    {
        return $this->hasManyThrough(
            ClassModel::class,
            Student::class,
            'user_id',
            'id',
            'id',
            'class_id'
        );
    }

    /**
     * Check if user is enrolled in a specific class
     */
    public function isEnrolledInClass($classId): bool
    {
        // If user is a teacher, they have access to their own classes
        if ($this->isTeacher()) {
            return $this->taughtClasses()->where('id', $classId)->exists();
        }

        // If user is a student, check enrollment
        if ($this->student) {
            return $this->student()->where('class_id', $classId)->exists();
        }

        // Admins and super admins have access to all classes
        if ($this->isAdmin() || $this->isSuperAdmin()) {
            return true;
        }

        return false;
    }
}