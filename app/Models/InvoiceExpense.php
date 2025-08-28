<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceExpense extends Model
{
    protected $fillable = [
        'invoice_id',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        // إعادة حساب الإجماليات عند إضافة أو تعديل أو حذف مصروف
        static::created(function ($expense) {
            $expense->invoice->calculateTotals();
        });

        static::updated(function ($expense) {
            $expense->invoice->calculateTotals();
        });

        static::deleted(function ($expense) {
            $expense->invoice->calculateTotals();
        });
    }
}