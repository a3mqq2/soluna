@extends('layouts.app')

@section('title', 'لوحة التحكم')

@push('styles')
<style>
    .stats-kpi { display:flex; align-items:center; gap:14px; }
    .stats-kpi .label { color:#6c757d; font-size:13px; }
    .badge-round { border-radius:50px; padding:6px 10px; font-size:12px; }
    .table-sm th, .table-sm td { padding:.4rem .6rem; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card welcome-banner" style="background:#894b39!important;">
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
