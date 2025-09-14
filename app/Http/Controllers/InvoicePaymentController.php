<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Transaction;
use App\Models\Treasury;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class InvoicePaymentController extends Controller
{
    /**
     * Store a new payment for an invoice.
     */
    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.001', 'max:' . $invoice->remaining_amount],
            'payment_method' => ['required', 'in:cash,bank_transfer,check,credit_card,other'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['nullable', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $payment = null;

            DB::transaction(function () use ($request, $invoice, $payment) {
                $payment = $invoice->addPayment([
                    'amount' => $request->amount,
                    'payment_method' => $request->payment_method,
                    'reference_number' => $request->reference_number,
                    'payment_date' => $request->payment_date ?: now()->toDateString(),
                    'notes' => $request->notes,
                    'status' => 'completed',
                ]);

                $treasury = Treasury::first();
                if ($treasury) {
                    Transaction::create([
                        'treasury_id' => $treasury->id,
                        'type' => 'deposit',
                        'amount' => $payment->amount,
                        'description' => "دفعة من فاتورة #{$invoice->invoice_number}",
                        'invoice_id' => $invoice->id,
                    ]);
                    $treasury->increment('balance', $payment->amount);
                }
            });

            return redirect()->back()->with('success', 'تمت اضافة دفعة بنجاح');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الدفعة: ' . $e->getMessage());
        }
    }

    /**
     * Show payment receipt for printing.
     */
    public function receipt(InvoicePayment $payment)
    {
        $payment->load(['invoice.customer', 'invoice.items.service', 'createdBy']);
        return view('payments.receipt', compact('payment'));
    }

    /**
     * Show payment details for an invoice.
     */
    public function show(Invoice $invoice)
    {
        $payments = $invoice->payments()
            ->with('createdBy')
            ->latest('payment_date')
            ->paginate(10);

        return view('invoices.payments', compact('invoice', 'payments'));
    }

    /**
     * Update payment status.
     */
    public function updateStatus(Request $request, InvoicePayment $payment): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:completed,pending,failed,cancelled'],
        ]);

        try {
            DB::transaction(function () use ($request, $payment) {
                $oldStatus = $payment->status;
                $payment->update(['status' => $request->status]);

                $treasury = Treasury::first();
                if ($treasury && $oldStatus !== $payment->status) {
                    if ($request->status === 'cancelled' && $oldStatus === 'completed') {
                        // خصم المبلغ من الخزنة
                        Transaction::create([
                            'treasury_id' => $treasury->id,
                            'type' => 'withdrawal',
                            'amount' => $payment->amount,
                            'description' => "إلغاء دفعة فاتورة #{$payment->invoice->invoice_number}",
                            'invoice_id' => $payment->invoice_id,
                        ]);
                        $treasury->decrement('balance', $payment->amount);
                    }
                }
            });

            return redirect()
                ->back()
                ->with('success', 'تم تحديث حالة الدفعة بنجاح');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء تحديث حالة الدفعة');
        }
    }

    /**
     * Delete a payment.
     */
    public function destroy(InvoicePayment $payment): RedirectResponse
    {
        try {
            DB::transaction(function () use ($payment) {
                $treasury = Treasury::first();

                if ($treasury && $payment->status === 'completed') {
                    Transaction::create([
                        'treasury_id' => $treasury->id,
                        'type' => 'withdrawal',
                        'amount' => $payment->amount,
                        'description' => "حذف دفعة فاتورة #{$payment->invoice->invoice_number}",
                        'invoice_id' => $payment->invoice_id,
                    ]);
                    $treasury->decrement('balance', $payment->amount);
                }

                $payment->delete();
            });

            return redirect()
                ->back()
                ->with('success', 'تم حذف الدفعة بنجاح');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء حذف الدفعة');
        }
    }

    /**
     * Get payment statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_payments' => InvoicePayment::completed()->count(),
            'total_amount' => InvoicePayment::completed()->sum('amount'),
            'this_month' => InvoicePayment::completed()
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
            'payment_methods' => InvoicePayment::completed()
                ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
                ->groupBy('payment_method')
                ->get()
                ->keyBy('payment_method')
                ->map(fn($item) => [
                    'count' => $item->count,
                    'total' => $item->total,
                    'name' => InvoicePayment::PAYMENT_METHODS[$item->payment_method] ?? $item->payment_method
                ]),
        ];

        return response()->json($stats);
    }
}