<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Treasury;

class MigratePaymentsAndExpensesSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $treasury = Treasury::firstOrFail();

            // الدفعات المكتملة
            $payments = DB::table('invoice_payments')
                ->where('status', 'completed')
                ->select('id', 'invoice_id', 'amount', 'payment_date as date', 'notes as description')
                ->get()
                ->map(function ($p) use ($treasury) {
                    return [
                        'treasury_id' => $treasury->id,
                        'invoice_id'  => $p->invoice_id,
                        'type'        => 'deposit',
                        'amount'      => $p->amount,
                        'description' => $p->description ?? "دفعة فاتورة #{$p->invoice_id}",
                        'created_at'  => $p->date,
                        'updated_at'  => $p->date,
                    ];
                });

            // المصروفات
            $expenses = DB::table('invoice_expenses')
                ->select('id', 'invoice_id', 'amount', 'created_at as date', 'description')
                ->get()
                ->map(function ($e) use ($treasury) {
                    return [
                        'treasury_id' => $treasury->id,
                        'invoice_id'  => $e->invoice_id,
                        'type'        => 'withdrawal',
                        'amount'      => $e->amount,
                        'description' => $e->description ?? "مصروف فاتورة #{$e->invoice_id}",
                        'created_at'  => $e->date,
                        'updated_at'  => $e->date,
                    ];
                });

            // دمج وترتيب
            $all = $payments->merge($expenses)
                ->sortBy('created_at')
                ->values();

            foreach ($all as $row) {
                Transaction::create($row);

                if ($row['type'] === 'deposit') {
                    $treasury->increment('balance', $row['amount']);
                } else {
                    $treasury->decrement('balance', $row['amount']);
                }
            }
        });
    }
}
