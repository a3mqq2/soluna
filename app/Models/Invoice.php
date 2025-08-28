<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'invoice_date',
        'subtotal',
        'discount',
        'total',
        'paid_amount',
        'remaining_amount',
        'notes',
        'status',
        'user_id',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->total = $this->subtotal - $this->discount;
        $this->remaining_amount = $this->total - $this->paid_amount;
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = 'INV-' . date('Y') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

/**
 * Get all payments for this invoice.
 */
public function payments()
{
    return $this->hasMany(InvoicePayment::class)->orderBy('payment_date', 'desc');
}

/**
 * Get completed payments only.
 */
public function completedPayments()
{
    return $this->hasMany(InvoicePayment::class)->completed();
}

/**
 * Calculate total paid amount from completed payments.
 */
public function getTotalPaidAttribute(): float
{
    return (float) $this->completedPayments()->sum('amount');
}

/**
 * Calculate remaining amount.
 */
public function getRemainingAmountAttribute(): float
{
    return max(0, $this->total - $this->total_paid);
}

/**
 * Check if invoice is fully paid.
 */
public function getIsFullyPaidAttribute(): bool
{
    return $this->remaining_amount <= 0;
}

/**
 * Check if invoice has partial payments.
 */
public function getIsPartiallyPaidAttribute(): bool
{
    return $this->total_paid > 0 && $this->remaining_amount > 0;
}

/**
 * Update invoice status based on payments.
 */
public function updateStatus(): void
{
    if ($this->is_fully_paid) {
        $this->status = 'paid';
    } elseif ($this->is_partially_paid) {
        $this->status = 'partial';
    } else {
        $this->status = 'unpaid';
    }
    
    $this->save();
}

/**
 * Add a payment to this invoice.
 */
public function addPayment(array $paymentData): InvoicePayment
{
    $paymentData['created_by'] = auth()->id();
    $paymentData['payment_date'] = $paymentData['payment_date'] ?? now()->toDateString();
    
    return $this->payments()->create($paymentData);
}

/**
 * Get payment summary.
 */
public function getPaymentSummary(): array
{
    return [
        'total_amount' => $this->total,
        'total_paid' => $this->total_paid,
        'remaining_amount' => $this->remaining_amount,
        'payment_count' => $this->completedPayments()->count(),
        'is_fully_paid' => $this->is_fully_paid,
        'is_partially_paid' => $this->is_partially_paid,
    ];
}
}