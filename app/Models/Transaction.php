<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'treasury_id',
        'invoice_id',
        'user_id',
        'type',
        'amount',
        'description',
    ];

    public function treasury()
    {
        return $this->belongsTo(Treasury::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        // تسجيل المُعِد تلقائياً عند الإنشاء
        static::creating(function ($transaction) {
            if (!$transaction->user_id && auth()->check()) {
                $transaction->user_id = auth()->id();
            }
        });
    }
}