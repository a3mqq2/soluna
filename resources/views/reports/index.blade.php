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
    
    .stats-card-primary::before { background: linear-gradient(90deg, #b48b1e 0%, #8a6817 100%); }
    .stats-card-success::before { background: linear-gradient(90deg, #28a745 0%, #20c997 100%); }
    .stats-card-warning::before { background: linear-gradient(90deg, #ffc107 0%, #fd7e14 100%); }
    .stats-card-danger::before { background: linear-gradient(90deg, #dc3545 0%, #e83e8c 100%); }
    .stats-card-info::before { background: linear-gradient(90deg, #17a2b8 0%, #6f42c1 100%); }
    
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
        background: linear-gradient(135deg, #faf8f6 0%, #f5f3f1 100%);
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
        background: linear-gradient(135deg, #b48b1e 0%, #8a6817 100%);
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
        background: linear-gradient(90deg, #b48b1e 0%, #8a6817 100%);
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
                    @if(isset($stats['invoices_change']))
                        <div class="stats-change {{ $stats['invoices_change'] >= 0 ? 'positive' : 'negative' }}">
                            <i class="ti ti-trending-{{ $stats['invoices_change'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ abs($stats['invoices_change']) }}% عن الفترة السابقة
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-success">
                <div class="stats-card-body">
                    <div class="stats-icon">
                        <i class="ti ti-currency-dollar"></i>
                    </div>
                    <div class="stats-value">{{ number_format($stats['total_revenue'] ?? 0, 0) }}</div>
                    <div class="stats-label">إجمالي الإيرادات (د.ل)</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-warning">
                <div class="stats-card-body">
                    <div class="stats-icon">
                        <i class="ti ti-users"></i>
                    </div>
                    <div class="stats-value">{{ $stats['total_customers'] ?? 0 }}</div>
                    <div class="stats-label">إجمالي الزبائن</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-danger">
                <div class="stats-card-body">
                    <div class="stats-icon">
                        <i class="ti ti-alert-circle"></i>
                    </div>
                    <div class="stats-value">{{ number_format($stats['outstanding_amount'] ?? 0, 0) }}</div>
                    <div class="stats-label">المستحقات (د.ل)</div>
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

        <!-- Invoice Status Distribution -->
        <div class="col-lg-4">
            <div class="card report-card">
                <div class="card-header">
                    <h5>توزيع حالات الفواتير</h5>
                </div>
                <div class="card-body">
                    @if(isset($invoiceStats) && count($invoiceStats) > 0)
                        @foreach($invoiceStats as $stat)
                            <div class="progress-item">
                                <div class="progress-label">
                                    <span>
                                        @switch($stat->status)
                                            @case('unpaid') غير مدفوعة @break
                                            @case('partial') مدفوعة جزئي @break
                                            @case('cancelled') ملغية @break
                                            @default {{ $stat->status }} @break
                                        @endswitch
                                    </span>
                                    <span><strong>{{ $stat->count }}</strong></span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ ($stat->count / $stats['total_invoices']) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="ti ti-chart-pie-off"></i>
                            <h6>لا توجد بيانات</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Customers -->
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
                                        <th>إجمالي الإنفاق</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topCustomers as $customer)
                                        <tr>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-avatar me-2" style="width: 30px; height: 30px; background: #e8c9c0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #b48b1e; font-weight: 700; font-size: 12px;">
                                                        {{ substr($customer->customer_name, 0, 1) }}
                                                    </div>
                                                    {{ $customer->customer_name }}
                                                </div>
                                            </td>
                                            <td><span class="badge bg-info">{{ $customer->invoice_count }}</span></td>
                                            <td class="amount-cell">{{ number_format($customer->total_spent, 3) }} د.ل</td>
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

        <!-- Monthly Revenue Trend -->
        <div class="col-lg-6">
            <div class="card report-card">
                <div class="card-header">
                    <h5>الإيرادات الشهرية</h5>
                </div>
                <div class="card-body">
                    @if(isset($monthlyRevenue) && count($monthlyRevenue) > 0)
                        <div class="chart-container">
                            @foreach($monthlyRevenue as $month)
                                <div class="progress-item">
                                    <div class="progress-label">
                                        <span>{{ $month->month_name }}</span>
                                        <span class="amount-cell">{{ number_format($month->revenue, 0) }} د.ل</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ ($month->revenue / $monthlyRevenue->max('revenue')) * 100 }}%"></div>
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

    <div class="row">
        <!-- Invoice Registration Report -->
        <div class="col-12">
            <div class="card report-card">
                <div class="card-header">
                    <h5>تقرير تسجيل الفواتير</h5>
                </div>
                <div class="card-body">
                    @if(isset($invoiceRegistrations) && count($invoiceRegistrations) > 0)
                        <div class="table-responsive">
                            <table class="table table-elegant">
                                <thead>
                                    <tr>
                                        <th>رقم الفاتورة</th>
                                        <th>الزبون</th>
                                        <th>تاريخ التسجيل</th>
                                        <th>المبلغ</th>
                                        <th>الحالة</th>
                                        <th>المسجل بواسطة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoiceRegistrations as $invoice)
                                        <tr>
                                            <td class="text-start">
                                                <strong>{{ $invoice->invoice_number }}</strong>
                                            </td>
                                            <td class="text-start">{{ $invoice->customer_name }}</td>
                                            <td>{{ $invoice->created_at }}</td>
                                            <td class="amount-cell">{{ number_format($invoice->total, 3) }} د.ل</td>
                                            <td>
                                                <span class="badge 
                                                    @switch($invoice->status)
                                                        @case('unpaid') bg-warning @break
                                                        @case('partial') bg-info @break
                                                        @case('cancelled') bg-danger @break
                                                        @default bg-secondary @break
                                                    @endswitch
                                                ">
                                                    @switch($invoice->status)
                                                        @case('unpaid') غير مدفوعة @break
                                                        @case('partial') مدفوعة جزئي @break
                                                        @case('cancelled') ملغية @break
                                                        @default {{ $invoice->status }} @break
                                                    @endswitch
                                                </span>
                                            </td>
                                            <td class="text-start">{{ $invoice->user_name ?? 'غير محدد' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Registration Summary -->
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="fw-bold fs-5">{{ count($invoiceRegistrations) }}</div>
                                    <small class="text-muted">فاتورة مسجلة</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="fw-bold fs-5 amount-cell">{{ number_format($invoiceRegistrations->sum('total'), 0) }} د.ل</div>
                                    <small class="text-muted">إجمالي القيم</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="fw-bold fs-5">{{ $invoiceRegistrations->where('status', 'unpaid')->count() }}</div>
                                    <small class="text-muted">غير مدفوعة</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="fw-bold fs-5">{{ $invoiceRegistrations->where('status', 'partial')->count() }}</div>
                                    <small class="text-muted">مدفوعة جزئي</small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="ti ti-file-off"></i>
                            <h5>لا توجد فواتير مسجلة</h5>
                            <p class="text-muted">لا توجد فواتير في الفترة المحددة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Registration Report -->
    <div class="row">
        <div class="col-12">
            <div class="card report-card">
                <div class="card-header">
                    <h5>تقرير تسجيل المدفوعات</h5>
                </div>
                <div class="card-body">
                    @if(isset($paymentRegistrations) && count($paymentRegistrations) > 0)
                        <div class="table-responsive">
                            <table class="table table-elegant">
                                <thead>
                                    <tr>
                                        <th>رقم الفاتورة</th>
                                        <th>الزبون</th>
                                        <th>مبلغ الدفعة</th>
                                        <th>طريقة الدفع</th>
                                        <th>تاريخ الدفع</th>
                                        <th>تاريخ التسجيل</th>
                                        <th>المسجل بواسطة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paymentRegistrations as $payment)
                                        <tr>
                                            <td class="text-start">
                                                <strong>{{ $payment->invoice_number }}</strong>
                                            </td>
                                            <td class="text-start">{{ $payment->customer_name }}</td>
                                            <td class="amount-cell">{{ number_format($payment->amount, 3) }} د.ل</td>
                                            <td>
                                                <span class="badge bg-info">{{ $payment->payment_method_name }}</span>
                                            </td>
                                            <td>{{ $payment->payment_date }}</td>
                                            <td>{{ $payment->created_at }}</td>
                                            <td class="text-start">{{ $payment->user_name ?? 'غير محدد' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Payment Summary -->
                        <div class="row mt-4">
                            <div class="col-md-2">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="fw-bold fs-5">{{ count($paymentRegistrations) }}</div>
                                    <small class="text-muted">دفعة مسجلة</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="fw-bold fs-5 amount-cell">{{ number_format($paymentRegistrations->sum('amount'), 0) }} د.ل</div>
                                    <small class="text-muted">إجمالي المدفوعات</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="fw-bold fs-5">{{ $paymentRegistrations->where('payment_method', 'cash')->count() }}</div>
                                    <small class="text-muted">نقدي</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="fw-bold fs-5">{{ $paymentRegistrations->where('payment_method', 'bank_transfer')->count() }}</div>
                                    <small class="text-muted">تحويل بنكي</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="fw-bold fs-5">{{ $paymentRegistrations->where('payment_method', 'check')->count() }}</div>
                                    <small class="text-muted">شيك</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="fw-bold fs-5">{{ $paymentRegistrations->whereIn('payment_method', ['credit_card', 'other'])->count() }}</div>
                                    <small class="text-muted">أخرى</small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="ti ti-credit-card-off"></i>
                            <h5>لا توجد مدفوعات مسجلة</h5>
                            <p class="text-muted">لا توجد مدفوعات في الفترة المحددة</p>
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
    // Auto-submit period form on date change
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Add small delay to allow both dates to be selected
            setTimeout(() => {
                document.querySelector('form').submit();
            }, 100);
        });
    });
});
</script>
@endpush