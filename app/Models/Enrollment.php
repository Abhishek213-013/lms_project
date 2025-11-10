<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_id',
        'progress',
        'last_activity_type',
        'last_accessed',
        'status',
        'enrolled_at'
    ];

    protected $casts = [
        'progress' => 'integer',
        'last_accessed' => 'datetime',
        'enrolled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class that the enrollment belongs to.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the course through class
     */
    public function course()
    {
        return $this->class();
    }

    /**
     * Scope completed enrollments
     */
    public function scopeCompleted($query)
    {
        return $query->where('progress', '>=', 100);
    }

    /**
     * Scope active enrollments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope with recent activity
     */
    public function scopeWithRecentActivity($query)
    {
        return $query->where('progress', '>', 0)
                    ->whereNotNull('last_accessed');
    }
}