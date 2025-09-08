<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Service;
use App\Models\InvoiceItem;
use App\Models\InvoiceExpense;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Determine date range
        $dateRange = $this->getDateRange($request);
        $fromDate = $dateRange['from'];
        $toDate   = $dateRange['to'];

        // Get main statistics with profitability (payments - expenses)
        $stats = $this->getMainStats($fromDate, $toDate);

        // Get profitability analysis per invoice (based on completed payments in range)
        $invoiceProfitability = $this->getInvoiceProfitability($fromDate, $toDate);

        // Get top services
        $topServices = $this->getTopServices($fromDate, $toDate);

        // Get invoice status distribution
        $invoiceStats = $this->getInvoiceStats($fromDate, $toDate);

        // Get expense categories
        $expenseCategories = $this->getExpenseCategories($fromDate, $toDate);

        // Get top customers with profitability (payments - expenses)
        $topCustomers = $this->getTopCustomers($fromDate, $toDate);

        // Get monthly profit trend (payments - expenses)
        $monthlyProfit = $this->getMonthlyProfit($fromDate, $toDate);

        // Get monthly revenue trend (use completed payments as revenue)
        $monthlyRevenue = $this->getMonthlyRevenue($fromDate, $toDate);

        // Get invoice registrations
        $invoiceRegistrations = $this->getInvoiceRegistrations($fromDate, $toDate);

        // Get payment registrations
        $paymentRegistrations = $this->getPaymentRegistrations($fromDate, $toDate);

        return view('reports.index', compact(
            'stats',
            'invoiceProfitability',
            'topServices',
            'invoiceStats',
            'expenseCategories',
            'topCustomers',
            'monthlyProfit',
            'monthlyRevenue',
            'invoiceRegistrations',
            'paymentRegistrations'
        ));
    }

    private function getDateRange(Request $request)
    {
        $period = $request->input('period');

        if ($period === 'today') {
            return [
                'from' => Carbon::today(),
                'to'   => Carbon::today(),
            ];
        } elseif ($period === 'week') {
            return [
                'from' => Carbon::now()->startOfWeek(),
                'to'   => Carbon::now()->endOfWeek(),
            ];
        } elseif ($period === 'month') {
            return [
                'from' => Carbon::now()->startOfMonth(),
                'to'   => Carbon::now()->endOfMonth(),
            ];
        }

        // Custom date range
        $fromDate = $request->input('from_date')
            ? Carbon::parse($request->input('from_date'))
            : Carbon::now()->startOfMonth();

        $toDate = $request->input('to_date')
            ? Carbon::parse($request->input('to_date'))
            : Carbon::now();

        return [
            'from' => $fromDate,
            'to'   => $toDate,
        ];
    }

    private function getMainStats($fromDate, $toDate)
    {
        // Invoice IDs within the invoice_date range (used for expense aggregation)
        $invoiceIdsInRange = Invoice::whereBetween('invoice_date', [$fromDate, $toDate])->pluck('id');

        // Core KPIs
        $totalInvoices = Invoice::whereBetween('invoice_date', [$fromDate, $toDate])->count();

        // Payments considered as "revenue" (completed only, within period by payment_date)
        $totalPayments = InvoicePayment::where('status', 'completed')
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->sum('amount');

        // Expenses tied to invoices within the date range
        $totalExpenses = InvoiceExpense::whereIn('invoice_id', $invoiceIdsInRange)->sum('amount');

        // Discounts (for reference KPI; not used in net profit since profit is payments - expenses)
        $totalDiscount = Invoice::whereIn('id', $invoiceIdsInRange)->sum('discount');

        // Net profit definition: total payments - total expenses
        $netProfit = $totalPayments - $totalExpenses;

        // Profit margin based on collected payments
        $profitMargin = $totalPayments > 0 ? ($netProfit / $totalPayments) * 100 : 0;

        // Outstanding across all time (unchanged)
        $totalCustomers    = Customer::count();
        $outstandingAmount = Invoice::where('remaining_amount', '>', 0)->sum('remaining_amount');

        // Average amount per invoice (based on payments in the period)
        $avgInvoiceAmount = $totalInvoices > 0 ? ($totalPayments / $totalInvoices) : 0;

        // Status counts must use fresh builders (avoid builder mutation)
        $paidInvoices = Invoice::whereBetween('invoice_date', [$fromDate, $toDate])->where('status', 'paid')->count();
        $partialInvoices = Invoice::whereBetween('invoice_date', [$fromDate, $toDate])->where('status', 'partial')->count();
        $unpaidInvoices = Invoice::whereBetween('invoice_date', [$fromDate, $toDate])->where('status', 'unpaid')->count();

        return [
            'total_invoices'     => $totalInvoices,
            'total_payments'     => $totalPayments,  // new KPI
            'total_expenses'     => $totalExpenses,
            'total_discount'     => $totalDiscount,
            'net_profit'         => $netProfit,      // = payments - expenses
            'profit_margin'      => $profitMargin,   // based on payments
            'total_customers'    => $totalCustomers,
            'outstanding_amount' => $outstandingAmount,
            'avg_invoice_amount' => $avgInvoiceAmount,
            'paid_invoices'      => $paidInvoices,
            'partial_invoices'   => $partialInvoices,
            'unpaid_invoices'    => $unpaidInvoices,
        ];
    }

    private function getInvoiceProfitability($fromDate, $toDate)
    {
        // Profit per invoice based on payments within the period minus that invoice's expenses
        return DB::table('invoices')
            ->leftJoin('customers', 'invoices.customer_id', '=', 'customers.id')
            ->leftJoin('invoice_payments', function ($join) use ($fromDate, $toDate) {
                $join->on('invoice_payments.invoice_id', '=', 'invoices.id')
                    ->where('invoice_payments.status', 'completed')
                    ->whereBetween('invoice_payments.payment_date', [$fromDate, $toDate]);
            })
            ->leftJoin('invoice_expenses', 'invoice_expenses.invoice_id', '=', 'invoices.id')
            ->whereBetween('invoices.invoice_date', [$fromDate, $toDate])
            ->groupBy('invoices.id', 'invoices.invoice_number', 'customers.name', 'invoices.subtotal')
            ->select(
                'invoices.id',
                'invoices.invoice_number',
                'customers.name as customer_name',
                DB::raw('COALESCE(SUM(invoice_payments.amount),0) AS paid_sum'),
                DB::raw('COALESCE(SUM(invoice_expenses.amount),0) AS expenses_sum'),
                DB::raw('invoices.subtotal AS invoice_subtotal')
            )
            ->get()
            ->map(function ($row) {
                $row->net_profit   = (float)$row->paid_sum - (float)$row->expenses_sum;
                $row->profit_margin = $row->paid_sum > 0 ? ($row->net_profit / $row->paid_sum) * 100 : 0;
                return $row;
            })
            ->sortByDesc('net_profit')
            ->values();
    }

    private function getTopServices($fromDate, $toDate)
    {
        return DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('services', 'invoice_items.service_id', '=', 'services.id')
            ->whereBetween('invoices.invoice_date', [$fromDate, $toDate])
            ->select(
                'services.name as service_name',
                DB::raw('COUNT(invoice_items.id) as order_count'),
                DB::raw('SUM(invoice_items.quantity) as total_quantity'),
                DB::raw('SUM(invoice_items.total_price) as total_revenue')
            )
            ->groupBy('services.id', 'services.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();
    }

    private function getInvoiceStats($fromDate, $toDate)
    {
        return Invoice::whereBetween('invoice_date', [$fromDate, $toDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getExpenseCategories($fromDate, $toDate)
    {
        return InvoiceExpense::join('invoices', 'invoice_expenses.invoice_id', '=', 'invoices.id')
            ->whereBetween('invoices.invoice_date', [$fromDate, $toDate])
            ->groupBy('description')
            ->selectRaw('description, SUM(amount) as total_amount, COUNT(*) as count')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();
    }

    private function getTopCustomers($fromDate, $toDate)
    {
        // Aggregate completed payments per customer in range, minus expenses of their invoices in range
        $paymentsSub = DB::table('invoice_payments')
            ->select('invoice_id', DB::raw('SUM(amount) AS paid_sum'))
            ->where('status', 'completed')
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->groupBy('invoice_id');

        $expensesSub = DB::table('invoice_expenses')
            ->select('invoice_id', DB::raw('SUM(amount) AS expenses_sum'))
            ->groupBy('invoice_id');

        return DB::table('invoices')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->leftJoinSub($paymentsSub, 'p', 'p.invoice_id', '=', 'invoices.id')
            ->leftJoinSub($expensesSub, 'e', 'e.invoice_id', '=', 'invoices.id')
            ->whereBetween('invoices.invoice_date', [$fromDate, $toDate])
            ->groupBy('customers.id', 'customers.name')
            ->select(
                'customers.name as customer_name',
                DB::raw('COUNT(DISTINCT invoices.id) as invoice_count'),
                DB::raw('COALESCE(SUM(p.paid_sum),0) as total_paid'),
                DB::raw('COALESCE(SUM(e.expenses_sum),0) as total_expenses'),
                DB::raw('(COALESCE(SUM(p.paid_sum),0) - COALESCE(SUM(e.expenses_sum),0)) as total_profit')
            )
            ->orderByDesc('total_profit')
            ->limit(10)
            ->get();
    }

    private function getMonthlyProfit($fromDate, $toDate)
    {
        // Start from max(6 months ago, fromDate)
        $startDate = (clone $fromDate)->copy();
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();
        if ($startDate->lt($sixMonthsAgo)) {
            $startDate = $sixMonthsAgo;
        }

        // Subquery: monthly payments (completed)
        $monthlyPayments = DB::table('invoice_payments')
            ->where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $toDate])
            ->select(
                DB::raw('YEAR(payment_date) as y'),
                DB::raw('MONTH(payment_date) as m'),
                DB::raw('SUM(amount) as payments_sum')
            )
            ->groupBy('y', 'm');

        // Subquery: monthly expenses from invoices within range
        $monthlyExpenses = DB::table('invoices')
            ->join('invoice_expenses', 'invoice_expenses.invoice_id', '=', 'invoices.id')
            ->whereBetween('invoices.invoice_date', [$startDate, $toDate])
            ->select(
                DB::raw('YEAR(invoices.invoice_date) as y'),
                DB::raw('MONTH(invoices.invoice_date) as m'),
                DB::raw('SUM(invoice_expenses.amount) as expenses_sum')
            )
            ->groupBy('y', 'm');

        // Join months
        $rows = DB::table(DB::raw('(' . $monthlyPayments->toSql() . ') mp'))
            ->mergeBindings($monthlyPayments)
            ->leftJoin(DB::raw('(' . $monthlyExpenses->toSql() . ') me'), function ($join) {
                $join->on('mp.y', '=', 'me.y')->on('mp.m', '=', 'me.m');
            })
            ->mergeBindings($monthlyExpenses)
            ->select(
                'mp.y as year',
                'mp.m as month',
                DB::raw('COALESCE(mp.payments_sum,0) as revenue'),
                DB::raw('COALESCE(me.expenses_sum,0) as expenses'),
                DB::raw('(COALESCE(mp.payments_sum,0) - COALESCE(me.expenses_sum,0)) as net_profit')
            )
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $item->month_name = Carbon::create($item->year, $item->month, 1)->format('M Y');
                $item->invoice_count = null; // not applicable when using payments-based aggregation
                $item->discounts = null;     // optional field kept for compatibility
                return $item;
            });

        return $rows;
    }

    private function getMonthlyRevenue($fromDate, $toDate)
    {
        // Use completed payments as monthly revenue
        $startDate = (clone $fromDate)->copy();
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();
        if ($startDate->lt($sixMonthsAgo)) {
            $startDate = $sixMonthsAgo;
        }

        return DB::table('invoice_payments')
            ->where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $toDate])
            ->select(
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount) as revenue'),
                DB::raw('COUNT(*) as payment_count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                $item->month_name = Carbon::create($item->year, $item->month, 1)->format('M Y');
                return $item;
            });
    }

    private function getInvoiceRegistrations($fromDate, $toDate)
    {
        return DB::table('invoices')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'invoices.user_id', '=', 'users.id')
            ->whereBetween('invoices.created_at', [$fromDate->startOfDay(), $toDate->endOfDay()])
            ->select(
                'invoices.invoice_number',
                'customers.name as customer_name',
                'invoices.total',
                'invoices.status',
                'invoices.created_at',
                'users.name as user_name'
            )
            ->orderBy('invoices.created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($item) {
                $item->created_at = Carbon::parse($item->created_at)->format('Y/m/d H:i');
                return $item;
            });
    }

    private function getPaymentRegistrations($fromDate, $toDate)
    {
        return DB::table('invoice_payments')
            ->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'invoice_payments.created_by', '=', 'users.id')
            ->whereBetween('invoice_payments.created_at', [$fromDate->startOfDay(), $toDate->endOfDay()])
            ->where('invoice_payments.status', 'completed')
            ->select(
                'invoices.invoice_number',
                'customers.name as customer_name',
                'invoice_payments.amount',
                'invoice_payments.payment_method',
                'invoice_payments.payment_date',
                'invoice_payments.created_at',
                'users.name as user_name'
            )
            ->orderBy('invoice_payments.created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($item) {
                $item->created_at   = Carbon::parse($item->created_at)->format('Y/m/d H:i');
                $item->payment_date = Carbon::parse($item->payment_date)->format('Y/m/d');

                // Convert payment method to Arabic
                $paymentMethods = [
                    'cash'          => 'نقداً',
                    'bank_transfer' => 'تحويل بنكي',
                    'check'         => 'شيك',
                    'credit_card'   => 'بطاقة ائتمان',
                    'other'         => 'أخرى',
                ];

                $item->payment_method_name = $paymentMethods[$item->payment_method] ?? $item->payment_method;

                return $item;
            });
    }
}
