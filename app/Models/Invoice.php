<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_number',
        'status',
        'date',
        'subtotal',
        'expenses_total',
        'discount',
        'total',
        'net_profit',
        'paid_amount',
        'remaining_amount',
        'invoice_date',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'subtotal' => 'decimal:2',
        'expenses_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'invoice_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(InvoiceExpense::class);
    }

    /**
     * Calculate all invoice totals including expenses and net profit
     */
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->expenses_total = $this->expenses->sum('amount');
        $this->total = $this->subtotal - $this->discount;
        $this->net_profit = $this->total - $this->expenses_total;
    
        $this->paid_amount = $this->getTotalPaidAttribute();
        $this->remaining_amount = $this->total - $this->paid_amount;
    
        $this->updateStatus();
        $this->save();
    }
    
    protected static function boot(): void
    {
        parent::boot();
    
        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                do {
                    $number = 'INV-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                } while (static::where('invoice_number', $number)->exists());
    
                $invoice->invoice_number = $number;
            }
        });
    }
    

    /**
     * Get all payments for this invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class)->orderBy('payment_date', 'desc');
    }

    /**
     * Get completed payments only.
     */
    public function completedPayments(): HasMany
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
     * Add an expense to this invoice.
     */
    public function addExpense(array $expenseData): InvoiceExpense
    {
        $expense = $this->expenses()->create($expenseData);
        $this->calculateTotals();
        return $expense;
    }

    /**
     * Get financial summary including profit calculations.
     */
    public function getFinancialSummary(): array
    {
        return [
            'sales_total' => $this->subtotal,
            'expenses_total' => $this->expenses_total,
            'discount' => $this->discount,
            'total_after_discount' => $this->total,
            'net_profit' => $this->net_profit,
            'profit_margin' => $this->subtotal > 0 ? ($this->net_profit / $this->subtotal) * 100 : 0,
            'total_paid' => $this->total_paid,
            'remaining_amount' => $this->remaining_amount,
            'payment_count' => $this->completedPayments()->count(),
            'is_fully_paid' => $this->is_fully_paid,
            'is_partially_paid' => $this->is_partially_paid,
        ];
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

    /**
     * Get profit analysis
     */
    public function getProfitAnalysis(): array
    {
        $salesTotal = $this->subtotal;
        $expensesTotal = $this->expenses_total;
        $netProfit = $this->net_profit;

        return [
            'sales_total' => $salesTotal,
            'expenses_total' => $expensesTotal,
            'gross_profit' => $salesTotal - $expensesTotal,
            'discount' => $this->discount,
            'net_profit' => $netProfit,
            'profit_margin_percentage' => $salesTotal > 0 ? round(($netProfit / $salesTotal) * 100, 2) : 0,
            'expense_ratio_percentage' => $salesTotal > 0 ? round(($expensesTotal / $salesTotal) * 100, 2) : 0,
        ];
    }
}