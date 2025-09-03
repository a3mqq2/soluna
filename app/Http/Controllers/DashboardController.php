<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today      = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();

        // Subquery: total completed payments per invoice
        $paidSub = InvoicePayment::selectRaw('invoice_id, SUM(amount) AS paid_sum')
            ->where('status', 'completed')
            ->groupBy('invoice_id');

        // Global totals
        $totals = Invoice::leftJoinSub($paidSub, 'p', 'p.invoice_id', '=', 'invoices.id')
            ->selectRaw('COALESCE(SUM(invoices.total),0) AS total_sum')
            ->selectRaw('COALESCE(SUM(COALESCE(p.paid_sum,0)),0) AS paid_sum')
            ->first();

        $totalInvoicesAmount = (float) $totals->total_sum;
        $totalPaidAmount     = (float) $totals->paid_sum;
        $totalOutstanding    = max(0, $totalInvoicesAmount - $totalPaidAmount);

        // Overdue (no due_date in schema -> assume invoice_date < today & still has remaining)
        $overdueAgg = Invoice::leftJoinSub($paidSub, 'p', 'p.invoice_id', '=', 'invoices.id')
            ->whereRaw('(invoices.total - COALESCE(p.paid_sum,0)) > 0')
            ->whereDate('invoices.invoice_date', '<', $today)
            ->selectRaw('COUNT(invoices.id) AS cnt')
            ->selectRaw('COALESCE(SUM(invoices.total - COALESCE(p.paid_sum,0)),0) AS amt')
            ->first();

        // Month-to-date totals
        $mtdTotals = Invoice::whereBetween('invoice_date', [$monthStart, $today])
            ->selectRaw('COALESCE(SUM(total),0) AS mtd_total')
            ->first();

        // Quick counts
        $customersCount       = Customer::count();
        $invoicesCount        = Invoice::count();
        $todayInvoicesCount   = Invoice::whereDate('invoice_date', $today)->count();
        $recentInvoices = Invoice::leftJoinSub($paidSub, 'p', 'p.invoice_id', '=', 'invoices.id')
            ->with('customer')
            ->orderByDesc('invoice_date')
            ->limit(8)
            ->get([
                'invoices.*',
                DB::raw('COALESCE(p.paid_sum,0) AS paid_sum'),
                DB::raw('(invoices.total - COALESCE(p.paid_sum,0)) AS remaining_sum'),
            ]);

        $recentPayments = InvoicePayment::with(['invoice.customer', 'createdBy'])
            ->orderByDesc('payment_date')
            ->limit(8)
            ->get();

        $topCustomers = Customer::leftJoin('invoices', 'invoices.customer_id', '=', 'customers.id')
            ->leftJoinSub($paidSub, 'p', 'p.invoice_id', '=', 'invoices.id')
            ->groupBy('customers.id', 'customers.name', 'customers.phone')
            ->select('customers.id', 'customers.name', 'customers.phone')
            ->selectRaw('COALESCE(SUM(invoices.total - COALESCE(p.paid_sum,0)),0) AS receivable')
            ->orderByDesc('receivable')
            ->limit(5)
            ->get();


            $todayInvoices = Invoice::whereDate('invoice_date', Carbon::today())->get();
            $tomorrowInvoices = Invoice::whereDate('invoice_date', Carbon::tomorrow())->get();



        return view('dashboard', [
            'customersCount'        => $customersCount,
            'invoicesCount'         => $invoicesCount,
            'totalInvoicesAmount'   => $totalInvoicesAmount,
            'totalPaidAmount'       => $totalPaidAmount,
            'totalOutstanding'      => $totalOutstanding,
            'overdueCount'          => (int) ($overdueAgg->cnt ?? 0),
            'overdueAmount'         => (float) ($overdueAgg->amt ?? 0),
            'todayInvoicesCount'    => $todayInvoicesCount,
            'mtdTotal'              => (float) ($mtdTotals->mtd_total ?? 0),
            'recentInvoices'        => $recentInvoices,
            'recentPayments'        => $recentPayments,
            'topCustomers'          => $topCustomers,
            'today'                 => $today,
            'monthStart'            => $monthStart,
            'todayInvoicesAlerts' => $todayInvoices,
            'tomorrowInvoicesAlerts' => $tomorrowInvoices,
        ]);
    }
}
