<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Service;
use App\Models\InvoiceItem;
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
        $toDate = $dateRange['to'];

        // Get main statistics
        $stats = $this->getMainStats($fromDate, $toDate);
        
        // Get top services
        $topServices = $this->getTopServices($fromDate, $toDate);
        
        // Get invoice status distribution
        $invoiceStats = $this->getInvoiceStats($fromDate, $toDate);
        
        // Get top customers
        $topCustomers = $this->getTopCustomers($fromDate, $toDate);
        
        // Get monthly revenue trend
        $monthlyRevenue = $this->getMonthlyRevenue($fromDate, $toDate);
        
        // Get invoice registrations
        $invoiceRegistrations = $this->getInvoiceRegistrations($fromDate, $toDate);
        
        // Get payment registrations
        $paymentRegistrations = $this->getPaymentRegistrations($fromDate, $toDate);

        return view('reports.index', compact(
            'stats', 
            'topServices', 
            'invoiceStats', 
            'topCustomers', 
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
                'to' => Carbon::today()
            ];
        } elseif ($period === 'week') {
            return [
                'from' => Carbon::now()->startOfWeek(),
                'to' => Carbon::now()->endOfWeek()
            ];
        } elseif ($period === 'month') {
            return [
                'from' => Carbon::now()->startOfMonth(),
                'to' => Carbon::now()->endOfMonth()
            ];
        }

        // Custom date range
        $fromDate = $request->input('from_date') ? 
            Carbon::parse($request->input('from_date')) : 
            Carbon::now()->startOfMonth();
            
        $toDate = $request->input('to_date') ? 
            Carbon::parse($request->input('to_date')) : 
            Carbon::now();

        return [
            'from' => $fromDate,
            'to' => $toDate
        ];
    }

    private function getMainStats($fromDate, $toDate)
    {
        $invoicesQuery = Invoice::whereBetween('invoice_date', [$fromDate, $toDate]);
        
        $totalInvoices = $invoicesQuery->count();
        $totalRevenue = $invoicesQuery->sum('total');
        $totalCustomers = Customer::count();
        $outstandingAmount = Invoice::where('remaining_amount', '>', 0)->sum('remaining_amount');
        
        // Calculate average invoice amount
        $avgInvoiceAmount = $totalInvoices > 0 ? $totalRevenue / $totalInvoices : 0;
        
        // Count invoices by status
        $paidInvoices = $invoicesQuery->where('status', 'paid')->count();
        $partialInvoices = $invoicesQuery->where('status', 'partial')->count();
        $unpaidInvoices = $invoicesQuery->where('status', 'unpaid')->count();

        return [
            'total_invoices' => $totalInvoices,
            'total_revenue' => $totalRevenue,
            'total_customers' => $totalCustomers,
            'outstanding_amount' => $outstandingAmount,
            'avg_invoice_amount' => $avgInvoiceAmount,
            'paid_invoices' => $paidInvoices,
            'partial_invoices' => $partialInvoices,
            'unpaid_invoices' => $unpaidInvoices,
        ];
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

    private function getTopCustomers($fromDate, $toDate)
    {
        return DB::table('invoices')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->whereBetween('invoices.invoice_date', [$fromDate, $toDate])
            ->select(
                'customers.name as customer_name',
                DB::raw('COUNT(invoices.id) as invoice_count'),
                DB::raw('SUM(invoices.total) as total_spent')
            )
            ->groupBy('customers.id', 'customers.name')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();
    }

    private function getMonthlyRevenue($fromDate, $toDate)
    {
        // Get monthly data for the last 6 months or within the specified range
        $startDate = max($fromDate, Carbon::now()->subMonths(6)->startOfMonth());
        
        return DB::table('invoices')
            ->whereBetween('invoice_date', [$startDate, $toDate])
            ->select(
                DB::raw('YEAR(invoice_date) as year'),
                DB::raw('MONTH(invoice_date) as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as invoice_count')
            )
            ->groupBy(DB::raw('YEAR(invoice_date)'), DB::raw('MONTH(invoice_date)'))
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
                $item->created_at = Carbon::parse($item->created_at)->format('Y/m/d H:i');
                $item->payment_date = Carbon::parse($item->payment_date)->format('Y/m/d');
                
                // Convert payment method to Arabic
                $paymentMethods = [
                    'cash' => 'نقداً',
                    'bank_transfer' => 'تحويل بنكي',
                    'check' => 'شيك',
                    'credit_card' => 'بطاقة ائتمان',
                    'other' => 'أخرى'
                ];
                
                $item->payment_method_name = $paymentMethods[$item->payment_method] ?? $item->payment_method;
                
                return $item;
            });
    }
}