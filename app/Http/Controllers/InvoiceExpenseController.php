<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceExpense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redirect;

class InvoiceExpenseController extends Controller
{
    /**
     * Display expenses for specific invoice
     */
    public function index(Invoice $invoice): JsonResponse
    {
        $expenses = $invoice->expenses()->get();

        return response()->json([
            'success' => true,
            'expenses' => $expenses,
            'financial_summary' => $invoice->getFinancialSummary(),
            'profit_analysis' => $invoice->getProfitAnalysis()
        ]);
    }

    /**
     * Store a new expense for invoice
     */
    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense = $invoice->addExpense($validated);
        return redirect()->back()->with('success', 'تم إضافة المصروف بنجاح');
    }

    /**
     * Update an existing expense
     */
    public function update(Request $request, Invoice $invoice, InvoiceExpense $expense)
    {
        // التأكد من أن المصروف ينتمي للفاتورة
        if ($expense->invoice_id != $invoice->id) {
            return response()->json([
                'success' => false,
                'message' => 'المصروف غير موجود في هذه الفاتورة'
            ], 404);
        }

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense->update($validated);

        return redirect()->back()->with('success', 'تم تحديث المصروف بنجاح');
    }

    /**
     * Remove expense from invoice
     */
    public function destroy(Invoice $invoice, InvoiceExpense $expense)
    {
        // التأكد من أن المصروف ينتمي للفاتورة
        if ($expense->invoice_id != $invoice->id) {
            return redirect()->back()->with('error', 'المصروف غير موجود في هذه الفاتورة');
        }

        $expense->delete();

        return redirect()->back()->with('success', 'تم حذف المصروف بنجاح');
    }

    /**
     * Get detailed financial report for invoice
     */
    public function getFinancialReport(Invoice $invoice): JsonResponse
    {
        $invoice->load(['items', 'expenses']);

        $report = [
            'invoice_info' => [
                'number' => $invoice->invoice_number,
                'date' => $invoice->date,
                'customer' => $invoice->customer->name ?? null,
            ],
            'sales_breakdown' => [
                'items' => $invoice->items->map(function ($item) {
                    return [
                        'name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total' => $item->total_price,
                    ];
                }),
                'subtotal' => $invoice->subtotal,
                'discount' => $invoice->discount,
                'total_after_discount' => $invoice->total,
            ],
            'expenses_breakdown' => [
                'expenses' => $invoice->expenses->map(function ($expense) {
                    return [
                        'description' => $expense->description,
                        'amount' => $expense->amount,
                    ];
                }),
                'total_expenses' => $invoice->expenses_total,
            ],
            'profit_calculation' => [
                'sales_total' => $invoice->subtotal,
                'minus_discount' => $invoice->discount,
                'total_after_discount' => $invoice->total,
                'minus_expenses' => $invoice->expenses_total,
                'net_profit' => $invoice->net_profit,
                'profit_margin' => $invoice->subtotal > 0 ? round(($invoice->net_profit / $invoice->subtotal) * 100, 2) : 0,
            ],
            'payment_info' => $invoice->getPaymentSummary(),
        ];

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }
}