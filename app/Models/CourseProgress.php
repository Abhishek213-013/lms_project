<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_id',
        'lesson_id',
        'time_spent_minutes',
        'completed',
        'accessed_at',
        'progress_percentage'
    ];

    protected $casts = [
        'time_spent_minutes' => 'integer',
        'completed' => 'boolean',
        'accessed_at' => 'datetime',
        'progress_percentage' => 'integer'
    ];

    /**
     * Get the user that owns the progress.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class that the progress belongs to.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Scope for recent activity
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('accessed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for today's activity
     */
    public function scopeToday($query)
    {
        return $query->whereDate('accessed_at', today());
    }
}