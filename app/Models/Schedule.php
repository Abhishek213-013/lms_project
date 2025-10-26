<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'teacher_id',
        'title',
        'description',
        'date',
        'time',
        'duration',
        'type',
        'status',
        'location',
        'max_attendees',
        'recurring',
        'recurrence_pattern',
        'recurrence_end_date',
        'students_attending'
    ];

    protected $casts = [
        'date' => 'date:Y-m-d', // Specify the format
        'recurrence_end_date' => 'date:Y-m-d', // Specify the format
        'recurring' => 'boolean'
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}