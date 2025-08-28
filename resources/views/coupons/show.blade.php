@extends('layouts.app')

@section('title', 'عرض الكوبون')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('coupons.index') }}">الكوبونات</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $coupon->name }}</li>
@endsection

@push('styles')
<style>
    body {
        background-color: #faf8f6;
    }
    
    .coupon-header {
        background: linear-gradient(135deg, #b48b1e 0%, #8a6817 100%);
        color: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(180, 139, 30, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    .coupon-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
        animation: float 20s linear infinite;
    }
    
    @keyframes float {
        0% { transform: translateX(-50px) translateY(-50px); }
        100% { transform: translateX(50px) translateY(50px); }
    }
    
    .coupon-code {
        font-family: 'Courier New', monospace;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 12px 25px;
        border-radius: 25px;
        font-size: 20px;
        font-weight: 700;
        display: inline-block;
        letter-spacing: 3px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
    }
    
    .info-card {
        background: white;
        border: none;
        box-shadow: 0 2px 15px rgba(180, 139, 30, 0.1);
        border-radius: 15px;
        margin-bottom: 20px;
        border-top: 4px solid #e8c9c0;
        transition: transform 0.2s ease;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(180, 139, 30, 0.15);
    }
    
    .info-card .card-header {
        background: linear-gradient(135deg, #faf8f6 0%, #f5f3f1 100%);
        border-bottom: 2px solid #e8c9c0;
        border-radius: 15px 15px 0 0;
        padding: 18px 25px;
    }
    
    .info-card .card-header h5,
    .info-card .card-header h6 {
        color: #b48b1e;
        font-weight: 700;
        margin: 0;
    }
    
    .coupon-table {
        margin: 0;
    }
    
    .coupon-table td {
        padding: 15px 0;
        border-bottom: 1px solid #f1f3f4;
        vertical-align: middle;
    }
    
    .coupon-table td:first-child {
        font-weight: 600;
        color: #6c757d;
        width: 160px;
    }
    
    .coupon-table td:last-child {
        color: #495057;
    }
    
    .discount-value {
        font-size: 24px;
        font-weight: 700;
        color: #b48b1e;
        font-family: 'Tajawal', Arial, sans-serif;
    }
    
    .type-badge {
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .type-fixed {
        background: #e8c9c0;
        color: #b48b1e;
    }
    
    .type-percentage {
        background: #b48b1e;
        color: white;
    }
    
    .usage-progress {
        width: 200px;
        height: 8px;
        background: #f1f3f4;
        border-radius: 10px;
        overflow: hidden;
        margin-top: 8px;
    }
    
    .usage-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #b48b1e 0%, #8a6817 100%);
        border-radius: 10px;
        transition: width 0.3s ease;
    }
    
    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-active {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-inactive {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .status-expired {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .action-buttons .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 12px 20px;
        transition: all 0.2s ease;
        margin-bottom: 8px;
    }
    
    .action-buttons .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-primary {
        background: #b48b1e;
        border-color: #b48b1e;
    }
    
    .btn-primary:hover {
        background: #8a6817;
        border-color: #8a6817;
    }
    
    .btn-outline-primary {
        color: #b48b1e;
        border-color: #b48b1e;
    }
    
    .btn-outline-primary:hover {
        background: #b48b1e;
        border-color: #b48b1e;
    }
    
    .print-btn {
        background: #e8c9c0;
        color: #b48b1e;
        border: 2px solid #b48b1e;
    }
    
    .print-btn:hover {
        background: #b48b1e;
        color: white;
        border-color: #8a6817;
    }
    
    @media (max-width: 768px) {
        .coupon-header {
            text-align: center;
            padding: 20px;
        }
        
        .coupon-table td:first-child {
            width: 120px;
            font-size: 14px;
        }
        
        .action-buttons {
            margin-top: 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Coupon Header -->
    <div class="coupon-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="text-white mb-3 fw-bold">{{ $coupon->name }}</h2>
                <div class="coupon-code mb-3">{{ $coupon->code }}</div>
                @if($coupon->description)
                    <p class="mb-0 opacity-75 fs-5">{{ $coupon->description }}</p>
                @endif
            </div>
            <div class="col-md-4 text-md-end">
                @php
                    $statusClass = 'status-active';
                    $statusText = 'نشط';
                    if (!$coupon->is_active) {
                        $statusClass = 'status-inactive';
                        $statusText = 'غير نشط';
                    } elseif ($coupon->is_expired) {
                        $statusClass = 'status-expired'; 
                        $statusText = 'منتهي';
                    }
                @endphp
                <div class="status-badge {{ $statusClass }}">{{ $statusText }}</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Coupon Details -->
        <div class="col-lg-8">
            <div class="card info-card">
                <div class="card-header">
                    <h5>تفاصيل الكوبون</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless coupon-table">
                        <tr>
                            <td>نوع الخصم:</td>
                            <td>
                                <span class="type-badge {{ $coupon->type === 'fixed' ? 'type-fixed' : 'type-percentage' }}">
                                    {{ $coupon->type === 'fixed' ? 'مبلغ ثابت' : 'نسبة مئوية' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>قيمة الخصم:</td>
                            <td>
                                <span class="discount-value">{{ $coupon->formatted_discount }}</span>
                            </td>
                        </tr>
                        @if($coupon->minimum_amount)
                        <tr>
                            <td>الحد الأدنى:</td>
                            <td><strong>{{ number_format($coupon->minimum_amount, 3) }} د.ل</strong></td>
                        </tr>
                        @endif
                        <tr>
                            <td>الاستخدام:</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fw-bold fs-5">{{ $coupon->used_count }}</span>
                                    @if($coupon->usage_limit)
                                        <span>/ {{ $coupon->usage_limit }}</span>
                                        <div class="usage-progress">
                                            <div class="usage-progress-bar" style="width: {{ ($coupon->used_count / $coupon->usage_limit) * 100 }}%"></div>
                                        </div>
                                    @else
                                        <span class="text-muted">(غير محدود)</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>تاريخ البداية:</td>
                            <td>{{ $coupon->start_date ? $coupon->start_date->format('Y/m/d') : 'فوري' }}</td>
                        </tr>
                        <tr>
                            <td>تاريخ الانتهاء:</td>
                            <td>
                                @if($coupon->end_date)
                                    <strong>{{ $coupon->end_date->format('Y/m/d') }}</strong>
                                    @if($coupon->is_expired)
                                        <span class="badge bg-danger ms-2">منتهي</span>
                                    @elseif($coupon->end_date->diffInDays() <= 7)
                                        <span class="badge bg-warning ms-2">ينتهي قريباً</span>
                                    @endif
                                @else
                                    <span class="text-success fw-bold">بلا انتهاء</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>تاريخ الإنشاء:</td>
                            <td>{{ $coupon->created_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        @if($coupon->updated_at != $coupon->created_at)
                        <tr>
                            <td>آخر تحديث:</td>
                            <td>{{ $coupon->updated_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="col-lg-4">
            <div class="card info-card">
                <div class="card-header">
                    <h6>الإجراءات</h6>
                </div>
                <div class="card-body">
                    <div class="action-buttons d-grid gap-2">
                        <a href="{{ route('coupons.show', ['print' => 1, 'coupon' => $coupon]) }}" 
                           target="_blank"
                           class="btn print-btn">
                            <i class="ti ti-printer me-2"></i>طباعة الكوبون
                        </a>
                        
                        <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-outline-primary">
                            <i class="ti ti-edit me-2"></i>تعديل الكوبون
                        </a>
                        
                        <form method="POST" action="{{ route('coupons.toggle', $coupon) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-{{ $coupon->is_active ? 'warning' : 'success' }} w-100">
                                <i class="ti ti-toggle-{{ $coupon->is_active ? 'right' : 'left' }} me-2"></i>
                                {{ $coupon->is_active ? 'إلغاء التفعيل' : 'تفعيل الكوبون' }}
                            </button>
                        </form>

                        <a href="{{ route('coupons.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-2"></i>العودة للقائمة
                        </a>

                        @if($coupon->used_count == 0)
                            <form method="POST" action="{{ route('coupons.destroy', $coupon) }}" 
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الكوبون؟ لا يمكن التراجع!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="ti ti-trash me-2"></i>حذف الكوبون
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Usage Statistics -->
            @if($coupon->used_count > 0)
            <div class="card info-card">
                <div class="card-header">
                    <h6>إحصائيات الاستخدام</h6>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-6">
                            <div class="fw-bold fs-4 text-primary">{{ $coupon->used_count }}</div>
                            <small class="text-muted">مرات الاستخدام</small>
                        </div>
                        <div class="col-6">
                            @if($coupon->usage_limit)
                                <div class="fw-bold fs-4 text-warning">{{ $coupon->usage_limit - $coupon->used_count }}</div>
                                <small class="text-muted">متبقي</small>
                            @else
                                <div class="fw-bold fs-4 text-success">∞</div>
                                <small class="text-muted">غير محدود</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection