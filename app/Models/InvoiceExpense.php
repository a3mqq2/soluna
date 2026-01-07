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
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot(): void
    {
        parent::boot();

        // تسجيل المُعِد تلقائياً عند الإنشاء
        static::creating(function ($expense) {
            if (!$expense->created_by && auth()->check()) {
                $expense->created_by = auth()->id();
            }
        });

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
