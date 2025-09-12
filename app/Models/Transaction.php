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

    
}