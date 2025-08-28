@extends('layouts.app')

@section('title', 'إدارة الكوبونات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active" aria-current="page">الكوبونات</li>
@endsection

@push('styles')
<style>
    .search-filters {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .badge-active { background-color: #d4edda; color: #155724; }
    .badge-inactive { background-color: #f8d7da; color: #721c24; }
    .badge-expired { background-color: #fff3cd; color: #856404; }
    .badge-used-up { background-color: #e2e3e5; color: #383d41; }
    
    .type-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .type-fixed { background-color: #e8c9bf; color: #b48b1e; }
    .type-percentage { background-color: #b48b1e; color: white; }
    
    .discount-value {
        font-family: 'Courier New', monospace;
        font-weight: 700;
        color: #b48b1e;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-ticket me-2"></i>
                    قائمة الكوبونات
                </h5>
                <div class="card-header-right">
                    <a href="{{ route('coupons.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus"></i> إضافة كوبون جديد
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Search & Filters -->
                <form id="searchForm" method="GET" action="{{ route('coupons.index') }}" class="search-filters">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">بحث</label>
                            <input type="text" name="q" class="form-control" 
                                   placeholder="ابحث بالاسم أو الكود..." 
                                   value="{{ request('q') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>الكل</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>منتهي</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">النوع</label>
                            <select name="type" class="form-select">
                                <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>الكل</option>
                                <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                                <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">ترتيب حسب</label>
                            <select name="sort_by" class="form-select">
                                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>تاريخ الإنشاء</option>
                                <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>الاسم</option>
                                <option value="code" {{ request('sort_by') === 'code' ? 'selected' : '' }}>الكود</option>
                                <option value="end_date" {{ request('sort_by') === 'end_date' ? 'selected' : '' }}>تاريخ الانتهاء</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ti ti-search"></i> بحث
                            </button>
                            <a href="{{ route('coupons.index') }}" class="btn btn-light">
                                <i class="ti ti-refresh"></i> إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Coupons Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>الكود</th>
                                <th>اسم الكوبون</th>
                                <th>النوع</th>
                                <th>قيمة الخصم</th>
                                <th>الاستخدام</th>
                                <th>تاريخ الانتهاء</th>
                                <th>الحالة</th>
                                <th width="200" class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-ticket text-primary me-2 fs-5"></i>
                                            <code class="bg-light px-2 py-1 rounded">{{ $coupon->code }}</code>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ $coupon->name }}</h6>
                                            @if($coupon->description)
                                                <small class="text-muted">{{ Str::limit($coupon->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="type-badge type-{{ $coupon->type }}">
                                            {{ $coupon->type === 'fixed' ? 'مبلغ ثابت' : 'نسبة مئوية' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="discount-value">{{ $coupon->formatted_discount }}</span>
                                        @if($coupon->minimum_amount)
                                            <br><small class="text-muted">حد أدنى: {{ number_format($coupon->minimum_amount, 2) }} د.ل</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $coupon->used_count }}</strong>
                                            @if($coupon->usage_limit)
                                                / {{ $coupon->usage_limit }}
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar" style="width: {{ ($coupon->used_count / $coupon->usage_limit) * 100 }}%"></div>
                                                </div>
                                            @else
                                                <small class="text-muted">غير محدود</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($coupon->end_date)
                                            <div>
                                                {{ $coupon->end_date->format('Y/m/d') }}
                                                @if($coupon->is_expired)
                                                    <br><small class="text-danger">منتهي</small>
                                                @elseif($coupon->end_date->diffInDays() <= 7)
                                                    <br><small class="text-warning">ينتهي قريباً</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">بلا انتهاء</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = 'badge-active';
                                            if (!$coupon->is_active) $statusClass = 'badge-inactive';
                                            elseif ($coupon->is_expired) $statusClass = 'badge-expired';
                                            elseif ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) $statusClass = 'badge-used-up';
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ $coupon->status_text }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="">
                                            <a href="{{ route('coupons.show', $coupon) }}" 
                                               class="btn btn-outline-info" title="عرض">
                                                <i class="ti ti-eye"></i>
                                            </a>

                                            <a href="{{ route('coupons.edit', $coupon) }}" 
                                               class="btn btn-outline-primary" title="تعديل">
                                                <i class="ti ti-edit"></i>
                                            </a>

                                            <form method="POST" 
                                                  action="{{ route('coupons.toggle', $coupon) }}" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-outline-{{ $coupon->is_active ? 'warning' : 'success' }}" 
                                                        title="{{ $coupon->is_active ? 'إلغاء تفعيل' : 'تفعيل' }}">
                                                    <i class="ti ti-{{ $coupon->is_active ? 'toggle-right' : 'toggle-left' }}"></i>
                                                </button>
                                            </form>

                                            @if($coupon->used_count == 0)
                                                <form method="POST" 
                                                      action="{{ route('coupons.destroy', $coupon) }}" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger" 
                                                            title="حذف"
                                                            onclick="return confirm('هل أنت متأكد من حذف هذا الكوبون؟')">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-ticket-off fs-1 d-block mb-2"></i>
                                            <p class="mb-0">لا توجد كوبونات</p>
                                            <a href="{{ route('coupons.create') }}" class="btn btn-primary btn-sm mt-2">
                                                إضافة كوبون جديد
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($coupons->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            عرض {{ $coupons->firstItem() }} إلى {{ $coupons->lastItem() }}
                            من أصل {{ $coupons->total() }} كوبون
                        </div>
                        <div>
                            {{ $coupons->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit on filters change
    ['status', 'type', 'sort_by'].forEach(name => {
        const el = document.querySelector(`[name="${name}"]`);
        if (el) {
            el.addEventListener('change', () => document.getElementById('searchForm').submit());
        }
    });

    // Auto-submit search with delay
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => document.getElementById('searchForm').submit(), 500);
        });
    }
});
</script>
@endpush