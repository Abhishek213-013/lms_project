<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_id',
        'roll_number',
        'father_name',
        'mother_name',
        'parent_contact',
        'country_code',
        'address',
        'admission_date', // Add this
        'status'
    ];

    protected $dates = [
        'admission_date', // Add this
        'created_at',
        'updated_at'
    ];

    /**
     * Get the user that owns the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class that the student belongs to.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get full parent contact number with country code
     */
    public function getFullParentContactAttribute(): string
    {
        return $this->country_code . $this->parent_contact;
    }

    /**
     * Scope active students
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}