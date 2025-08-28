<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'reference_number',
        'payment_date',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'payment_date' => 'date',
    ];

    const PAYMENT_METHODS = [
        'cash' => 'نقداً',
        'bank_transfer' => 'تحويل بنكي',
        'check' => 'شيك',
        'credit_card' => 'بطاقة ائتمان',
        'other' => 'أخرى'
    ];

    const STATUSES = [
        'completed' => 'مكتملة',
        'pending' => 'في الانتظار',
        'failed' => 'فاشلة',
        'cancelled' => 'ملغية'
    ];

    /**
     * Get the invoice that owns the payment.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the user who created the payment.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the payment method in Arabic.
     */
    public function getPaymentMethodNameAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Get the status in Arabic.
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 3) . ' د.ل';
    }

    /**
     * Get formatted payment date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->payment_date->format('Y/m/d');
    }

    /**
     * Scope for completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for payments by method.
     */
    public function scopeByMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope for payments in date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Update invoice status after payment is created
        static::created(function (InvoicePayment $payment) {
            if ($payment->status === 'completed') {
                $payment->invoice->updateStatus();
            }
        });

        // Update invoice status after payment is updated
        static::updated(function (InvoicePayment $payment) {
            if ($payment->isDirty('status') || $payment->isDirty('amount')) {
                $payment->invoice->updateStatus();
            }
        });

        // Update invoice status after payment is deleted
        static::deleted(function (InvoicePayment $payment) {
            $payment->invoice->updateStatus();
        });
    }
}