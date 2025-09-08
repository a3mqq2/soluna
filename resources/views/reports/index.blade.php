@extends('layouts.app')

@section('title', 'التقارير والإحصائيات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active" aria-current="page">التقارير والإحصائيات</li>
@endsection

@push('styles')
<style>
    body {
        background-color: #faf8f6;
    }
    
    .stats-card {
        background: white;
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(180, 139, 30, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 20px;
        overflow: hidden;
        position: relative;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(180, 139, 30, 0.15);
    }
    
    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    
    .stats-card-primary::before { background: #b48b1e; }
    .stats-card-success::before { background: #28a745; }
    .stats-card-warning::before { background: #ffc107; }
    .stats-card-danger::before { background: #dc3545; }
    .stats-card-info::before { background: #17a2b8; }
    .stats-card-profit::before { background: #e8c9c0; }
    
    .stats-card-body {
        padding: 25px;
        text-align: center;
    }
    
    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 15px;
        background: rgba(180, 139, 30, 0.1);
        color: #b48b1e;
    }
    
    .stats-value {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
        font-family: 'Courier New', monospace;
        color: #333;
    }
    
    .stats-value.profit-positive {
        color: #28a745;
    }
    
    .stats-value.profit-negative {
        color: #dc3545;
    }
    
    .stats-label {
        color: #6c757d;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stats-change {
        font-size: 12px;
        margin-top: 8px;
    }
    
    .stats-change.positive {
        color: #28a745;
    }
    
    .stats-change.negative {
        color: #dc3545;
    }
    
    .report-card {
        background: white;
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 15px rgba(180, 139, 30, 0.1);
        margin-bottom: 25px;
        border-top: 4px solid #e8c9c0;
    }
    
    .report-card .card-header {
        background: #faf8f6;
        border-bottom: 2px solid #e8c9c0;
        border-radius: 15px 15px 0 0;
        padding: 20px 25px;
    }
    
    .report-card .card-header h5 {
        color: #b48b1e;
        font-weight: 700;
        margin: 0;
        font-size: 18px;
    }
    
    .period-selector {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        border: 2px solid #e8c9c0;
    }
    
    .table-elegant {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table-elegant thead th {
        background: #b48b1e;
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 15px 12px;
        border: none;
        text-align: center;
    }
    
    .table-elegant tbody td {
        padding: 12px;
        border-bottom: 1px solid #f1f3f4;
        vertical-align: middle;
        text-align: center;
    }
    
    .table-elegant tbody tr:hover {
        background-color: rgba(180, 139, 30, 0.04);
    }
    
    .amount-cell {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #b48b1e;
    }
    
    .profit-cell {
        font-family: 'Courier New', monospace;
        font-weight: 700;
    }
    
    .profit-positive {
        color: #28a745;
    }
    
    .profit-negative {
        color: #dc3545;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        padding: 20px;
    }
    
    .progress-item {
        margin-bottom: 15px;
    }
    
    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 14px;
        font-weight: 600;
    }
    
    .progress {
        height: 8px;
        border-radius: 10px;
        background: #f1f3f4;
    }
    
    .progress-bar {
        border-radius: 10px;
        background: #b48b1e;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #8a6817;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.6;
        color: #b48b1e;
    }
    
    .btn-filter {
        background: #e8c9c0;
        color: #b48b1e;
        border: 2px solid #b48b1e;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .btn-filter:hover,
    .btn-filter.active {
        background: #b48b1e;
        color: white;
        border-color: #8a6817;
    }
    
    .profitability-summary {
        background: linear-gradient(135deg, #e8c9c0 0%, #f5f3f1 100%);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border: 2px solid #b48b1e;
    }
    
    @media (max-width: 768px) {
        .stats-card-body {
            padding: 20px;
        }
        
        .stats-value {
            font-size: 24px;
        }
        
        .chart-container {
            height: 250px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    
    <!-- Period Selector -->
    <div class="period-selector">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">من تاريخ</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date', now()->startOfMonth()->toDateString()) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">إلى تاريخ</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date', now()->toDateString()) }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-filter">
                    <i class="ti ti-search me-2"></i>تطبيق الفترة
                </button>
            </div>
            <div class="col-md-3">
                <div class="btn-group w-100" role="group">
                    <a href="?period=today" class="btn btn-filter btn-sm {{ request('period') == 'today' ? 'active' : '' }}">اليوم</a>
                    <a href="?period=week" class="btn btn-filter btn-sm {{ request('period') == 'week' ? 'active' : '' }}">الأسبوع</a>
                    <a href="?period=month" class="btn btn-filter btn-sm {{ request('period') == 'month' ? 'active' : '' }}">الشهر</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Profitability Summary (اعتمادًا على الدفعات) -->
    @php
        $totalPayments = $stats['total_payments'] ?? 0;
        $totalExpenses = $stats['total_expenses'] ?? 0;
        $netProfit     = $stats['net_profit'] ?? ($totalPayments - $totalExpenses);
        $profitMargin  = $totalPayments > 0 ? ($netProfit / $totalPayments) * 100 : 0;
    @endphp
    
    <div class="profitability-summary">
        <div class="row text-center">
            <div class="col-md-3">
                <h4 class="amount-cell">{{ number_format($totalPayments, 3) }} د.ل</h4>
                <small class="text-muted">إجمالي الدفعات المُحصّلة</small>
            </div>
            <div class="col-md-3">
                <h4 class="text-danger">{{ number_format($totalExpenses, 3) }} د.ل</h4>
                <small class="text-muted">إجمالي المصاريف</small>
            </div>
            <div class="col-md-3">
                <h4 class="profit-cell {{ $netProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    {{ number_format($netProfit, 3) }} د.ل
                </h4>
                <small class="text-muted">صافي الربح</small>
            </div>
            <div class="col-md-3">
                <h4 class="profit-cell {{ $profitMargin >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    {{ number_format($profitMargin, 1) }}%
                </h4>
                <small class="text-muted">هامش الربح</small>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-primary">
                <div class="stats-card-body">
                    <div class="stats-icon">
                        <i class="ti ti-receipt"></i>
                    </div>
                    <div class="stats-value">{{ $stats['total_invoices'] ?? 0 }}</div>
                    <div class="stats-label">إجمالي الفواتير</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-success">
                <div class="stats-card-body">
                    <div class="stats-icon">
                        <i class="ti ti-currency-dollar"></i>
                    </div>
                    <div class="stats-value">{{ number_format($totalPayments, 0) }}</div>
                    <div class="stats-label">الدفعات المُحصّلة (د.ل)</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-warning">
                <div class="stats-card-body">
                    <div class="stats-icon">
                        <i class="ti ti-trending-down"></i>
                    </div>
                    <div class="stats-value">{{ number_format($totalExpenses, 0) }}</div>
                    <div class="stats-label">إجمالي المصاريف (د.ل)</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-profit">
                <div class="stats-card-body">
                    <div class="stats-icon">
                        <i class="ti ti-trending-up"></i>
                    </div>
                    <div class="stats-value {{ $netProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        {{ number_format($netProfit, 0) }}
                    </div>
                    <div class="stats-label">صافي الربح (د.ل)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Invoice Profitability Analysis (دفعات - مصاريف) -->
        <div class="col-lg-12">
            <div class="card report-card">
                <div class="card-header">
                    <h5>تحليل ربحية الفواتير</h5>
                </div>
                <div class="card-body">
                    @if(isset($invoiceProfitability) && count($invoiceProfitability) > 0)
                        <div class="table-responsive">
                            <table class="table table-elegant">
                                <thead>
                                    <tr>
                                        <th>رقم الفاتورة</th>
                                        <th>الزبون</th>
                                        <th>الدفعات</th>
                                        <th>المصاريف</th>
                                        <th>صافي الربح</th>
                                        <th>هامش الربح %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoiceProfitability as $row)
                                        @php
                                            $paidSum      = (float) ($row->paid_sum ?? 0);
                                            $expensesSum  = (float) ($row->expenses_sum ?? 0);
                                            $netP         = (float) ($row->net_profit ?? ($paidSum - $expensesSum));
                                            $margin       = $paidSum > 0 ? ($netP / $paidSum) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td class="text-start"><strong>{{ $row->invoice_number ?? '-' }}</strong></td>
                                            <td class="text-start">{{ $row->customer_name ?? '-' }}</td>
                                            <td class="amount-cell">{{ number_format($paidSum, 3) }} د.ل</td>
                                            <td class="text-danger">{{ number_format($expensesSum, 3) }} د.ل</td>
                                            <td class="profit-cell {{ $netP >= 0 ? 'profit-positive' : 'profit-negative' }}">
                                                {{ number_format($netP, 3) }} د.ل
                                            </td>
                                            <td class="profit-cell {{ $margin >= 0 ? 'profit-positive' : 'profit-negative' }}">
                                                {{ number_format($margin, 1) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="ti ti-chart-line"></i>
                            <h5>لا توجد بيانات ربحية</h5>
                            <p class="text-muted">لا توجد فواتير في الفترة المحددة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Services Report -->
        <div class="col-lg-8">
            <div class="card report-card">
                <div class="card-header">
                    <h5>أكثر الخدمات طلباً</h5>
                </div>
                <div class="card-body">
                    @if(isset($topServices) && count($topServices) > 0)
                        <div class="table-responsive">
                            <table class="table table-elegant">
                                <thead>
                                    <tr>
                                        <th>الخدمة</th>
                                        <th>عدد المرات</th>
                                        <th>إجمالي الكمية</th>
                                        <th>إجمالي الإيرادات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topServices as $service)
                                        <tr>
                                            <td class="text-start">{{ $service->service_name }}</td>
                                            <td><span class="badge bg-primary">{{ $service->order_count }}</span></td>
                                            <td><strong>{{ number_format($service->total_quantity) }}</strong></td>
                                            <td class="amount-cell">{{ number_format($service->total_revenue, 3) }} د.ل</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="ti ti-package-off"></i>
                            <h5>لا توجد بيانات خدمات</h5>
                            <p class="text-muted">لا توجد فواتير في الفترة المحددة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Expense Categories -->
        <div class="col-lg-4">
            <div class="card report-card">
                <div class="card-header">
                    <h5>توزيع المصاريف</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalExpensesForPct = max(1, (float)$totalExpenses);
                    @endphp
                    @if(isset($expenseCategories) && count($expenseCategories) > 0)
                        @foreach($expenseCategories as $expense)
                            <div class="progress-item">
                                <div class="progress-label">
                                    <span>{{ $expense->description }}</span>
                                    <span class="text-danger">{{ number_format($expense->total_amount, 0) }} د.ل</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-danger" style="width: {{ ($expense->total_amount / $totalExpensesForPct) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="ti ti-receipt-off"></i>
                            <h6>لا توجد مصاريف</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Customers (اعتمادًا على الدفعات) -->
        <div class="col-lg-6">
            <div class="card report-card">
                <div class="card-header">
                    <h5>أفضل الزبائن</h5>
                </div>
                <div class="card-body">
                    @if(isset($topCustomers) && count($topCustomers) > 0)
                        <div class="table-responsive">
                            <table class="table table-elegant">
                                <thead>
                                    <tr>
                                        <th>الزبون</th>
                                        <th>عدد الفواتير</th>
                                        <th>إجمالي الدفعات</th>
                                        <th>الربح المحقق</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topCustomers as $customer)
                                        <tr>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-avatar me-2" style="width: 30px; height: 30px; background: #e8c9c0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #b48b1e; font-weight: 700; font-size: 12px;">
                                                        {{ mb_substr($customer->customer_name, 0, 1) }}
                                                    </div>
                                                    {{ $customer->customer_name }}
                                                </div>
                                            </td>
                                            <td><span class="badge bg-info">{{ $customer->invoice_count }}</span></td>
                                            <td class="amount-cell">{{ number_format($customer->total_paid ?? 0, 3) }} د.ل</td>
                                            <td class="profit-cell {{ ($customer->total_profit ?? 0) >= 0 ? 'profit-positive' : 'profit-negative' }}">
                                                {{ number_format($customer->total_profit ?? 0, 3) }} د.ل
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="ti ti-users-off"></i>
                            <h6>لا توجد بيانات زبائن</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Monthly Profit Trend (دفعات - مصاريف) -->
        <div class="col-lg-6">
            <div class="card report-card">
                <div class="card-header">
                    <h5>اتجاه الربحية الشهرية</h5>
                </div>
                <div class="card-body">
                    @if(isset($monthlyProfit) && count($monthlyProfit) > 0)
                        @php
                            $maxAbs = max(1, collect($monthlyProfit)->map(fn($m) => abs((float)$m->net_profit))->max());
                        @endphp
                        <div class="chart-container">
                            @foreach($monthlyProfit as $month)
                                @php $np = (float)$month->net_profit; @endphp
                                <div class="progress-item">
                                    <div class="progress-label">
                                        <span>{{ $month->month_name }}</span>
                                        <span class="profit-cell {{ $np >= 0 ? 'profit-positive' : 'profit-negative' }}">
                                            {{ number_format($np, 0) }} د.ل
                                        </span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar {{ $np >= 0 ? 'bg-success' : 'bg-danger' }}" 
                                             style="width: {{ (abs($np) / $maxAbs) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="ti ti-chart-bar-off"></i>
                            <h6>لا توجد بيانات شهرية</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            setTimeout(() => {
                this.closest('form').submit();
            }, 100);
        });
    });
});
</script>
@endpush
