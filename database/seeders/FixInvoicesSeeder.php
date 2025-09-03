<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixInvoicesSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $count = 0;

            Invoice::with(['items', 'expenses', 'payments'])
                ->chunk(100, function ($invoices) use (&$count) {
                    foreach ($invoices as $invoice) {
                        $invoice->calculateTotals(); // هذي تحدث كل القيم + status
                        $count++;
                    }
                });

            $this->command->info("تم تحديث $count فاتورة بنجاح ✅");
        });
    }
}
