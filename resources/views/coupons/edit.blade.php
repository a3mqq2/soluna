@extends('layouts.app')

@section('title', 'تعديل الكوبون')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('coupons.index') }}">الكوبونات</a></li>
    <li class="breadcrumb-item"><a href="{{ route('coupons.show', $coupon) }}">{{ $coupon->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">تعديل</li>
@endsection

@push('styles')
<style>
    .form-card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    .form-header {
        background: #b48b1e;
        color: white;
        border-radius: 0.375rem 0.375rem 0 0;
    }
    .required { color: #dc3545; }
    .form-control:focus {
        border-color: #b48b1e;
        box-shadow: 0 0 0 0.2rem rgba(180, 139, 30, 0.25);
    }
    .btn-primary {
        background: #b48b1e;
        border-color: #b48b1e;
    }
    .btn-primary:hover {
        background: #9a7319;
        border-color: #9a7319;
    }
    .coupon-info {
        background: #e8c9bf;
        border-left: 4px solid #b48b1e;
        padding: 15px;
        border-radius: 0.375rem;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">

        <!-- Current Coupon Info -->
        <div class="coupon-info">
            <h6 class="mb-2">
                <i class="ti ti-info-circle me-2" style="color: #b48b1e;"></i>
                تعديل الكوبون
            </h6>
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">الكود الحالي:</small>
                    <div class="fw-bold">{{ $coupon->code }}</div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">مرات الاستخدام:</small>
                    <div class="fw-bold">{{ $coupon->used_count }} @if($coupon->usage_limit)/ {{ $coupon->usage_limit }}@endif</div>
                </div>
            </div>
        </div>

        <div class="card form-card">
            <div class="card-header form-header">
                <h5 class="mb-0 text-white">
                    <i class="ti ti-edit me-2"></i>
                    تعديل الكوبون: {{ $coupon->name }}
                </h5>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('coupons.update', $coupon) }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Coupon Code -->
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">
                                كود الكوبون <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('code') is-invalid @enderror" 
                                   id="code" 
                                   name="code" 
                                   value="{{ old('code', $coupon->code) }}"
                                   style="text-transform: uppercase;"
                                   required>
                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Coupon Name -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                اسم الكوبون <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $coupon->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3">{{ old('description', $coupon->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Type -->
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">
                                نوع الخصم <span class="required">*</span>
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                                <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Value -->
                        <div class="col-md-6 mb-3">
                            <label for="value" class="form-label">
                                قيمة الخصم <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('value') is-invalid @enderror" 
                                       id="value" 
                                       name="value" 
                                       value="{{ old('value', $coupon->value) }}"
                                       step="0.001"
                                       min="0"
                                       required>
                                <span class="input-group-text" id="valueUnit">
                                    {{ $coupon->type === 'percentage' ? '%' : 'د.ل' }}
                                </span>
                            </div>
                            @error('value')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Minimum Amount -->
                        <div class="col-md-6 mb-3">
                            <label for="minimum_amount" class="form-label">الحد الأدنى للمبلغ</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('minimum_amount') is-invalid @enderror" 
                                       id="minimum_amount" 
                                       name="minimum_amount" 
                                       value="{{ old('minimum_amount', $coupon->minimum_amount) }}"
                                       step="0.001"
                                       min="0">
                                <span class="input-group-text">د.ل</span>
                            </div>
                            @error('minimum_amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Usage Limit -->
                        <div class="col-md-6 mb-3">
                            <label for="usage_limit" class="form-label">حد الاستخدام</label>
                            <input type="number" 
                                   class="form-control @error('usage_limit') is-invalid @enderror" 
                                   id="usage_limit" 
                                   name="usage_limit" 
                                   value="{{ old('usage_limit', $coupon->usage_limit) }}"
                                   min="{{ $coupon->used_count ?: 1 }}">
                            @error('usage_limit')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @if($coupon->used_count > 0)
                                <div class="form-text">
                                    <small>الحد الأدنى: {{ $coupon->used_count }} (مستخدم حالياً)</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <!-- Start Date -->
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">تاريخ البداية</label>
                            <input type="date" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date', $coupon->start_date?->format('Y-m-d')) }}">
                            @error('start_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">تاريخ الانتهاء</label>
                            <input type="date" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date', $coupon->end_date?->format('Y-m-d')) }}">
                            @error('end_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Is Active -->
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                تفعيل الكوبون
                            </label>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row">
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-device-floppy me-2"></i>
                                حفظ التغييرات
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('coupons.show', $coupon) }}" class="btn btn-info w-100">
                                <i class="ti ti-eye me-2"></i>
                                عرض الكوبون
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('coupons.index') }}" class="btn btn-secondary w-100">
                                <i class="ti ti-arrow-left me-2"></i>
                                العودة للقائمة
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const valueUnit = document.getElementById('valueUnit');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    // Update value unit based on type
    function updateValueDisplay() {
        if (typeSelect.value === 'percentage') {
            valueUnit.textContent = '%';
        } else {
            valueUnit.textContent = 'د.ل';
        }
    }

    typeSelect.addEventListener('change', updateValueDisplay);

    // Auto-uppercase code input
    document.getElementById('code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Update end date minimum when start date changes
    startDateInput.addEventListener('change', function() {
        if (this.value) {
            endDateInput.min = this.value;
        }
    });
});
</script>
@endpush