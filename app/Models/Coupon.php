<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:3',
        'minimum_amount' => 'decimal:3',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Generate a unique coupon code
     */
    public static function generateCode($length = 8)
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Check if coupon is valid
     */
    public function isValid($invoiceAmount = null)
    {
        // Check if coupon is active
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'الكوبون غير مفعل'];
        }

        // Check if coupon has started
        if ($this->start_date && Carbon::now()->lt($this->start_date)) {
            return ['valid' => false, 'message' => 'الكوبون لم يصبح فعالاً بعد'];
        }

        // Check if coupon has expired
        if ($this->end_date && Carbon::now()->gt($this->end_date)) {
            return ['valid' => false, 'message' => 'انتهت صلاحية الكوبون'];
        }

        // Check usage limit
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'تم استنفاد عدد مرات الاستخدام المسموحة'];
        }

        // Check minimum amount
        if ($invoiceAmount && $this->minimum_amount && $invoiceAmount < $this->minimum_amount) {
            return ['valid' => false, 'message' => 'المبلغ أقل من الحد الأدنى المطلوب: ' . number_format($this->minimum_amount, 3) . ' د.ل'];
        }

        return ['valid' => true, 'message' => 'الكوبون صالح للاستخدام'];
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount($amount)
    {
        if ($this->type === 'percentage') {
            return ($amount * $this->value) / 100;
        } else {
            return min($this->value, $amount); // Fixed amount, but not more than invoice total
        }
    }

    /**
     * Use the coupon (increment used count)
     */
    public function use()
    {
        $this->increment('used_count');
    }

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for valid coupons (not expired and within usage limit)
     */
    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', Carbon::now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', Carbon::now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                  ->orWhereRaw('used_count < usage_limit');
            });
    }

    /**
     * Get formatted discount value for display
     */
    public function getFormattedDiscountAttribute()
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        } else {
            return number_format($this->value, 3) . ' د.ل';
        }
    }

    /**
     * Get remaining uses
     */
    public function getRemainingUsesAttribute()
    {
        if (!$this->usage_limit) {
            return null; // Unlimited
        }

        return max(0, $this->usage_limit - $this->used_count);
    }

    /**
     * Check if coupon is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->end_date && Carbon::now()->gt($this->end_date);
    }

    /**
     * Check if coupon has started
     */
    public function getIsStartedAttribute()
    {
        return !$this->start_date || Carbon::now()->gte($this->start_date);
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        if (!$this->is_active) {
            return 'غير مفعل';
        }

        if (!$this->is_started) {
            return 'لم يبدأ بعد';
        }

        if ($this->is_expired) {
            return 'منتهي الصلاحية';
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'مستنفد';
        }

        return 'نشط';
    }

    /**
     * Find coupon by code
     */
    public static function findByCode($code)
    {
        return self::where('code', strtoupper(trim($code)))->first();
    }
}