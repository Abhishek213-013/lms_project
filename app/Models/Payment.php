<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'amount',
        'payment_method',
        'transaction_id',
        'phone_number',
        'receipt_path',
        'status',
        'payment_details',
        'additional_services',
        'coupon_code',
        'verified_at',
        'verified_by'
    ];

    protected $casts = [
        'payment_details' => 'array',
        'additional_services' => 'array',
        'verified_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    /**
     * Get the student that owns the payment
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the class that the payment is for
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the user who verified the payment
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope a query to only include completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get receipt URL
     */
    public function getReceiptUrlAttribute()
    {
        return $this->receipt_path ? Storage::url($this->receipt_path) : null;
    }

    /**
     * Check if payment is verified
     */
    public function getIsVerifiedAttribute()
    {
        return !is_null($this->verified_at);
    }
}