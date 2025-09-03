@extends('layouts.app')

@section('title', 'لوحة التحكم')

@push('styles')
<style>
    .stats-kpi { 
        display: flex; 
        align-items: center; 
        gap: 14px; 
    }
    .stats-kpi .label { 
        color: #6c757d; 
        font-size: 13px; 
    }
    .badge-round { 
        border-radius: 50px; 
        padding: 6px 10px; 
        font-size: 12px; 
    }
    .table-sm th, .table-sm td { 
        padding: .4rem .6rem; 
    }
    
    /* Custom color scheme */
    .welcome-banner {
        background-color: #e8c9c0 !important;
        border: none;
    }
    .welcome-banner .text-white {
        color: #5a4037 !important;
    }
    .welcome-banner .btn-outline-light {
        border-color: #b48b1e;
        color: #b48b1e;
        background: transparent;
    }
    .welcome-banner .btn-outline-light:hover {
        background-color: #b48b1e;
        border-color: #b48b1e;
        color: white;
    }
    
    /* Stats cards */
    .stats-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s ease;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .stats-card.primary {
        background-color: #e8c9c0;
        color: #5a4037;
    }
    .stats-card.success {
        background-color: #b48b1e;
        color: white;
    }
    .stats-card.warning {
        background-color: #e8c9c0;
        color: #5a4037;
    }
    .stats-card.info {
        background-color: #b48b1e;
        color: white;
    }
    .stats-card.danger {
        background-color: #e8c9c0;
        color: #5a4037;
    }
    .stats-card.teal {
        background-color: #b48b1e;
        color: white;
    }
    .stats-card.purple {
        background-color: #e8c9c0;
        color: #5a4037;
    }
    
    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .stats-icon.primary {
        background-color: rgba(90, 64, 55, 0.1);
        color: #5a4037;
    }
    .stats-icon.success,
    .stats-icon.info,
    .stats-icon.teal {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }
    .stats-icon.warning,
    .stats-icon.danger,
    .stats-icon.purple {
        background-color: rgba(90, 64, 55, 0.1);
        color: #5a4037;
    }
    
    .stats-number {
        font-size: 24px;
        font-weight: bold;
        line-height: 1;
        margin-bottom: 4px;
    }
    .stats-label {
        font-size: 14px;
        opacity: 0.8;
        margin-bottom: 8px;
    }
    
    /* Chart cards */
    .chart-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .chart-header {
        padding: 1rem 1.5rem 0;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 1rem;
    }
    .chart-header h5 {
        color: #5a4037;
        font-weight: 600;
    }
    
    /* Alert styling */
    .alert-warning {
        background-color: #e8c9c0;
        border-color: #b48b1e;
        color: #5a4037;
    }
    .alert-warning strong {
        color: #b48b1e;
    }
    .alert-warning a {
        color: #b48b1e;
        text-decoration: none;
        font-weight: 500;
    }
    .alert-warning a:hover {
        color: #8b6917;
        text-decoration: underline;
    }
    
    /* Badge improvements */
    .bg-success {
        background-color: #b48b1e !important;
    }
    .bg-warning {
        background-color: #e8c9c0 !important;
        color: #5a4037 !important;
    }
    .bg-danger {
        background-color: #dc3545 !important;
    }
    .bg-secondary {
        background-color: #6c757d !important;
    }
    
    .text-success {
        color: #b48b1e !important;
    }
    .text-warning {
        color: #b48b1e !important;
    }
    .text-info {
        color: #b48b1e !important;
    }
    .text-teal {
        color: #b48b1e !important;
    }
    .text-primary {
        color: #b48b1e !important;
    }
    
    /* Table improvements */
    .table thead th {
        background-color: #f8f9fa;
        color: #5a4037;
        font-weight: 600;
        border-bottom: 2px solid #e8c9c0;
    }
    
    .table tbody tr:hover {
        background-color: rgba(232, 201, 192, 0.1);
    }
    
    /* Button improvements */
    .btn-outline-secondary {
        border-color: #b48b1e;
        color: #b48b1e;
    }
    .btn-outline-secondary:hover {
        background-color: #b48b1e;
        border-color: #b48b1e;
        color: white;
    }
    
    /* Links */
    a {
        color: #b48b1e;
        text-decoration: none;
    }
    a:hover {
        color: #8b6917;
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        @if($todayInvoicesAlerts->count() || $tomorrowInvoicesAlerts->count())
            <div class="alert alert-warning">
                @if($todayInvoicesAlerts->count())
                    <p class="mb-1">
                        <strong>⚠️ فواتير اليوم ({{ now()->format('Y/m/d') }})</strong><br>
                        @foreach($todayInvoicesAlerts as $inv)
                            • <a href="{{ route('invoices.show', $inv->id) }}">{{ $inv->invoice_number }}</a>
                            @if($inv->customer) - {{ $inv->customer->name }} @endif
                            <br>
                        @endforeach
                    </p>
                @endif

                @if($tomorrowInvoicesAlerts->count())
                    <p class="mb-0 mt-2">
                        <strong>🔔 فواتير الغد ({{ now()->addDay()->format('Y/m/d') }})</strong><br>
                        @foreach($tomorrowInvoicesAlerts as $inv)
                            • <a href="{{ route('invoices.show', $inv->id) }}">{{ $inv->invoice_number }}</a>
                            @if($inv->customer) - {{ $inv->customer->name }} @endif
                            <br>
                        @endforeach
                    </p>
                @endif
            </div>
        @endif
    </div>
    
    <div class="col-md-12">
        <div class="card welcome-banner">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="p-4">
                            <h2 class="text-white">مرحباََ بعودتك</h2>
                            <p class="text-white mb-3">
                                اللهم إني أسألأك أن توفقني وتبارك لي فيما رزقتني. اللهم ارزقني رزقاً حلالاً طيباً مباركاً فيه. اللهم وسّع لي في رزقي، واكتب لي التوفيق واليُسر في عملي يا رب
                            </p>
                            <a href="{{ route('invoices.create') }}" class="btn btn-outline-light">اضافة فاتورة جديدة</a>
                        </div>
                    </div>
                    <div class="col-sm-6 text-center">
                        <div class="img-welcome-banner">
                            <img src="{{ asset('assets/images/widget/welcome-banner.png') }}" alt="img" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPIs Row 1 --}}
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card primary">
            <div class="card-body">
                <div class="stats-kpi">
                    <div class="stats-icon primary"><i class="ti ti-file-invoice"></i></div>
                    <div>
                        <div class="stats-number">{{ number_format($invoicesCount) }}</div>
                        <div class="stats-label">إجمالي الفواتير</div>
                        <span class="badge-round bg-light text-muted">{{ $today->format('Y/m/d') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card success">
            <div class="card-body">
                <div class="stats-kpi">
                    <div class="stats-icon success"><i class="ti ti-cash"></i></div>
                    <div>
                        <div class="stats-number">{{ number_format($totalPaidAmount, 3) }} د.ل</div>
                        <div class="stats-label">إجمالي المدفوع</div>
                        <span class="badge-round bg-light text-success">تحصيلات</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card warning">
            <div class="card-body">
                <div class="stats-kpi">
                    <div class="stats-icon warning"><i class="ti ti-receipt-2"></i></div>
                    <div>
                        <div class="stats-number">{{ number_format($totalOutstanding, 3) }} د.ل</div>
                        <div class="stats-label">إجمالي المستحق</div>
                        <span class="badge-round bg-light text-warning">غير محصل</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card info">
            <div class="card-body">
                <div class="stats-kpi">
                    <div class="stats-icon info"><i class="ti ti-users"></i></div>
                    <div>
                        <div class="stats-number">{{ number_format($customersCount) }}</div>
                        <div class="stats-label">عدد الزبائن</div>
                        <span class="badge-round bg-light text-info">نشط</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPIs Row 2 --}}
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card danger">
            <div class="card-body">
                <div class="stats-kpi">
                    <div class="stats-icon danger"><i class="ti ti-alert-triangle"></i></div>
                    <div>
                        <div class="stats-number">{{ number_format($overdueCount) }}</div>
                        <div class="stats-label">فواتير متأخرة</div>
                        <span class="badge-round bg-light text-danger">{{ number_format($overdueAmount, 3) }} د.ل</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card teal">
            <div class="card-body">
                <div class="stats-kpi">
                    <div class="stats-icon teal"><i class="ti ti-calendar-stats"></i></div>
                    <div>
                        <div class="stats-number">{{ number_format($todayInvoicesCount) }}</div>
                        <div class="stats-label">فواتير اليوم</div>
                        <span class="badge-round bg-light text-teal">اليوم</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stats-card purple">
            <div class="card-body">
                <div class="stats-kpi">
                    <div class="stats-icon purple"><i class="ti ti-calendar"></i></div>
                    <div>
                        <div class="stats-number">{{ number_format($mtdTotal, 3) }} د.ل</div>
                        <div class="stats-label">مبيعات هذا الشهر</div>
                        <span class="badge-round bg-light text-primary">{{ $monthStart->format('Y/m/d') }} - {{ $today->format('Y/m/d') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Invoices --}}
    <div class="col-lg-6 mb-3">
        <div class="card chart-card">
            <div class="chart-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">آخر الفواتير</h5>
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary">كل الفواتير</a>
            </div>
            <div class="card-body">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الزبون</th>
                            <th class="text-end">الإجمالي</th>
                            <th class="text-end">المتبقي</th>
                            <th class="text-center">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInvoices as $inv)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $inv->id) }}">
                                        {{ $inv->invoice_number }}
                                    </a>
                                </td>
                                <td>
                                    @if($inv->customer)
                                        <a href="{{ route('customers.show', $inv->customer->id) }}">{{ $inv->customer->name }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($inv->total, 3) }} د.ل</td>
                                <td class="text-end">{{ number_format(max(0,$inv->remaining_sum), 3) }} د.ل</td>
                                <td class="text-center">
                                    @php
                                        $status = $inv->status;
                                        $map = ['paid'=>'success','partial'=>'warning','unpaid'=>'danger'];
                                        $trans = ['paid'=>'مدفوعة','partial'=>'مدفوعة جزئياً','unpaid'=>'غير مدفوعة'];
                                        $cls = $map[$status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $cls }}">{{ $trans[$status] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">لا بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="col-lg-6 mb-3">
        <div class="card chart-card">
            <div class="chart-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">آخر المدفوعات</h5>
            </div>
            <div class="card-body">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>الزبون</th>
                            <th>الطريقة</th>
                            <th class="text-end">المبلغ</th>
                            <th class="text-center">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $p)
                            <tr>
                                <td>{{ $p->payment_date?->format('Y/m/d') }}</td>
                                <td>
                                    @if($p->invoice && $p->invoice->customer)
                                        <a href="{{ route('customers.show', $p->invoice->customer->id) }}">
                                            {{ $p->invoice->customer->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ \App\Models\InvoicePayment::PAYMENT_METHODS[$p->payment_method] ?? $p->payment_method }}</td>
                                <td class="text-end"><strong>{{ number_format($p->amount, 3) }} د.ل</strong></td>
                                <td class="text-center">
                                    @php
                                        $map = ['completed'=>'success','pending'=>'warning','failed'=>'danger','cancelled'=>'secondary'];
                                        $cls = $map[$p->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $cls }}">{{ \App\Models\InvoicePayment::STATUSES[$p->status] ?? $p->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">لا بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection