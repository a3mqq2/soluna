<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treasury extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'balance',
    ];

    /**
     * المعاملات المرتبطة بالخزنة
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * إيداع مبلغ في الخزنة
     */
    public function deposit(float $amount, ?string $description = null): Transaction
    {
        $this->increment('balance', $amount);

        return $this->transactions()->create([
            'type' => 'deposit',
            'amount' => $amount,
            'description' => $description,
        ]);
    }

    /**
     * سحب مبلغ من الخزنة
     */
    public function withdraw(float $amount, ?string $description = null): Transaction
    {
        if ($this->balance < $amount) {
            throw new \Exception('الرصيد غير كافٍ في الخزنة.');
        }

        $this->decrement('balance', $amount);

        return $this->transactions()->create([
            'type' => 'withdrawal',
            'amount' => $amount,
            'description' => $description,
        ]);
    }
}