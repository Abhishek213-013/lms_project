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
        'verified_by',
        'rejection_reason' // Added missing field
    ];

    protected $casts = [
        'payment_details' => 'array',
        'additional_services' => 'array',
        'verified_at' => 'datetime',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Add appends for computed attributes
    protected $appends = [
        'receipt_url',
        'is_verified',
        'payment_method_name',
        'formatted_amount',
        'status_badge_class'
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
    public function verifiedBy()
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
     * Scope a query to only include rejected payments
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include mobile payments
     */
    public function scopeMobilePayments($query)
    {
        return $query->whereIn('payment_method', ['bkash', 'nagad', 'rocket', 'upay']);
    }

    /**
     * Scope a query to only include bank transfers
     */
    public function scopeBankTransfers($query)
    {
        return $query->where('payment_method', 'bank_transfer');
    }

    /**
     * Get receipt URL
     */
    public function getReceiptUrlAttribute()
    {
        if (!$this->receipt_path) {
            return null;
        }

        // Check if it's already a full URL
        if (str_starts_with($this->receipt_path, 'http')) {
            return $this->receipt_path;
        }

        // Check if it's a storage path
        if (str_starts_with($this->receipt_path, 'storage/')) {
            return asset($this->receipt_path);
        }

        // Default case - assume it's in storage
        return Storage::url($this->receipt_path);
    }

    /**
     * Check if payment is verified
     */
    public function getIsVerifiedAttribute()
    {
        return !is_null($this->verified_at);
    }

    /**
     * Get payment method name in readable format
     */
    public function getPaymentMethodNameAttribute()
    {
        $methods = [
            'bkash' => 'bKash',
            'nagad' => 'Nagad',
            'rocket' => 'Rocket',
            'upay' => 'uPay',
            'bank_transfer' => 'Bank Transfer'
        ];

        return $methods[$this->payment_method] ?? ucfirst(str_replace('_', ' ', $this->payment_method));
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute()
    {
        return '৳ ' . number_format($this->amount, 2);
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'completed' => 'bg-success',
            'pending' => 'bg-warning',
            'rejected' => 'bg-danger'
        ];

        return $classes[$this->status] ?? 'bg-secondary';
    }

    /**
     * Get status text in readable format
     */
    public function getStatusTextAttribute()
    {
        $texts = [
            'completed' => 'Verified',
            'pending' => 'Pending',
            'rejected' => 'Rejected'
        ];

        return $texts[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get additional services as formatted text
     */
    public function getAdditionalServicesTextAttribute()
    {
        if (!$this->additional_services || empty($this->additional_services)) {
            return 'None';
        }

        $services = [];
        
        if (isset($this->additional_services['certificate']) && $this->additional_services['certificate']) {
            $services[] = 'Certificate';
        }
        
        if (isset($this->additional_services['consulting']) && $this->additional_services['consulting']) {
            $services[] = '1-on-1 Consulting';
        }

        return empty($services) ? 'None' : implode(', ', $services);
    }

    /**
     * Calculate total amount with additional services
     */
    public function getTotalAmountAttribute()
    {
        $total = $this->amount;

        if ($this->additional_services) {
            if (isset($this->additional_services['certificate']) && $this->additional_services['certificate']) {
                $total += 500; // Certificate fee
            }
            
            if (isset($this->additional_services['consulting']) && $this->additional_services['consulting']) {
                $total += 2000; // Consulting fee
            }
        }

        return $total;
    }

    /**
     * Get formatted total amount with currency
     */
    public function getFormattedTotalAmountAttribute()
    {
        return '৳ ' . number_format($this->total_amount, 2);
    }

    /**
     * Check if payment can be verified (for admin)
     */
    public function getCanVerifyAttribute()
    {
        return $this->status === 'pending' && $this->payment_method === 'bank_transfer';
    }

    /**
     * Check if payment can be rejected (for admin)
     */
    public function getCanRejectAttribute()
    {
        return $this->status === 'pending';
    }

    /**
     * Get payment age in days
     */
    public function getPaymentAgeAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Check if payment is recent (less than 24 hours)
     */
    public function getIsRecentAttribute()
    {
        return $this->created_at->gt(now()->subDay());
    }

    /**
     * Automatically set verified_at when status is changed to completed
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($payment) {
            if ($payment->isDirty('status') && $payment->status === 'completed' && !$payment->verified_at) {
                $payment->verified_at = now();
            }
        });
    }

    /**
     * Get related announcements for this payment
     */
    public function announcements()
    {
        return $this->morphMany(Announcement::class, 'related');
    }

    /**
     * Get payment verification status with icon
     */
    public function getStatusWithIconAttribute()
    {
        $icons = [
            'completed' => '✅',
            'pending' => '⏳',
            'rejected' => '❌'
        ];

        return ($icons[$this->status] ?? '❓') . ' ' . $this->status_text;
    }
}