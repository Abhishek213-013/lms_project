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
        'bio', // Make sure this is included
        'status',
        'rejection_reason'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
        'email_verified_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'active', // Default status for existing users
    ];

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include pending users.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include rejected users.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include approved teachers (active status).
     */
    public function scopeApprovedTeachers($query)
    {
        return $query->where('role', 'teacher')->where('status', 'active');
    }

    /**
     * Scope a query to only include pending teacher requests.
     */
    public function scopePendingTeachers($query)
    {
        return $query->where('role', 'teacher')->where('status', 'pending');
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if user is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if user is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Get the formatted status with badge colors.
     */
    public function getStatusBadgeAttribute(): array
    {
        $statusConfig = [
            'active' => ['color' => 'green', 'label' => 'Active'],
            'pending' => ['color' => 'yellow', 'label' => 'Pending'],
            'rejected' => ['color' => 'red', 'label' => 'Rejected'],
            'inactive' => ['color' => 'gray', 'label' => 'Inactive'],
        ];

        return $statusConfig[$this->status] ?? ['color' => 'gray', 'label' => 'Unknown'];
    }

    /**
     * Check if user can be approved (must be pending).
     */
    public function canBeApproved(): bool
    {
        return $this->isPending();
    }

    /**
     * Check if user can be rejected (must be pending).
     */
    public function canBeRejected(): bool
    {
        return $this->isPending();
    }

    /**
     * Approve the user (set status to active).
     */
    public function approve(): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        return $this->update([
            'status' => 'active',
            'email_verified_at' => $this->email_verified_at ?? now(),
            'rejection_reason' => null, // Clear any previous rejection reason
        ]);
    }

    /**
     * Reject the user (set status to rejected).
     */
    public function reject(string $reason): bool
    {
        if (!$this->canBeRejected()) {
            return false;
        }

        return $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Deactivate the user.
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => 'inactive']);
    }

    /**
     * Reactivate the user.
     */
    public function reactivate(): bool
    {
        return $this->update(['status' => 'active']);
    }

    /**
     * Get the display name for educational qualification.
     */
    public function getQualificationDisplayAttribute(): string
    {
        $qualifications = [
            'HSC' => 'Higher Secondary Certificate',
            'BSC' => 'Bachelor of Science',
            'BA' => 'Bachelor of Arts',
            'MA' => 'Master of Arts',
            'MSC' => 'Master of Science',
            'PhD' => 'Doctor of Philosophy',
            'Other' => 'Other Qualification',
        ];

        return $qualifications[$this->education_qualification] ?? $this->education_qualification;
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher' && $this->isActive();
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student' && $this->isActive();
    }

    /**
     * Check if user is a pending teacher.
     */
    public function isPendingTeacher(): bool
    {
        return $this->role === 'teacher' && $this->isPending();
    }

    /**
     * Check if user can access teacher features.
     */
    public function canAccessTeacherFeatures(): bool
    {
        return $this->isTeacher() || $this->isAdmin() || $this->isSuperAdmin();
    }

    /**
     * Check if user can access admin features.
     */
    public function canAccessAdminFeatures(): bool
    {
        return $this->isAdmin() || $this->isSuperAdmin();
    }

    /**
     * Get the classes taught by the teacher.
     */
    public function taughtClasses(): HasMany
    {
        return $this->hasMany(ClassModel::class, 'teacher_id');
    }

    /**
     * Alias for taughtClasses for backward compatibility.
     */
    public function classes(): HasMany
    {
        return $this->taughtClasses();
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
     * Get the classes where this user is a student (if applicable).
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
     * Check if user is enrolled in a specific class.
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

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        // You can implement your avatar logic here
        $avatars = [
            '/assets/img/instructor/instructor01.png',
            '/assets/img/instructor/instructor02.png',
            '/assets/img/instructor/instructor03.png',
            '/assets/img/instructor/instructor04.png'
        ];
        
        $avatarIndex = ($this->id - 1) % count($avatars);
        return $avatars[$avatarIndex];
    }

    /**
     * Get the user's age based on date of birth.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->dob?->age;
    }

    /**
     * Check if user is eligible to become an instructor (at least 18 years old).
     */
    public function isEligibleForInstructor(): bool
    {
        return $this->age >= 18;
    }

    /**
     * Get the user's teaching experience in years.
     */
    public function getTeachingExperienceAttribute(): ?string
    {
        if (!$this->experience) {
            return null;
        }

        // Parse experience string to extract years
        preg_match('/(\d+)\s*year/', $this->experience, $matches);
        
        if (isset($matches[1])) {
            $years = (int)$matches[1];
            return $years . ' year' . ($years > 1 ? 's' : '');
        }

        return $this->experience;
    }

    /**
     * Get statistics for teacher (if applicable).
     */
    public function getTeacherStatsAttribute(): array
    {
        if (!$this->isTeacher()) {
            return [];
        }

        return [
            'classes_count' => $this->taughtClasses()->count(),
            'resources_count' => $this->resources()->count(),
            'students_count' => $this->taughtClasses()->withCount('students')->get()->sum('students_count'),
            'rating' => 4.8, // You can implement actual rating system
        ];
    }

    /**
     * Boot method for model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Set default status based on role when creating new users
        static::creating(function ($user) {
            if ($user->role === 'teacher' && !isset($user->status)) {
                $user->status = 'pending'; // Teacher requests need approval
            } elseif (!isset($user->status)) {
                $user->status = 'active'; // Other roles are active by default
            }
        });

        // Send notification when teacher is approved
        static::updated(function ($user) {
            if ($user->isDirty('status') && $user->isTeacher() && $user->isActive()) {
                // You can send approval notification here
                // Notification::send($user, new TeacherApprovedNotification());
            }
        });
    }
}