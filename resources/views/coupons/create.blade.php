@extends('layouts.app')

@section('title', 'إضافة كوبون جديد')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('coupons.index') }}">الكوبونات</a></li>
    <li class="breadcrumb-item active" aria-current="page">إضافة كوبون جديد</li>
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
    .required {
        color: #dc3545;
    }
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
    .input-group-text {
        background-color: #e8c9bf;
        border-color: #e8c9bf;
        color: #b48b1e;
    }
    .code-display {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        background: #e8c9bf;
        color: #b48b1e;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 1.1rem;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">

        <div class="card form-card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('coupons.store') }}" novalidate>
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">
                                كود الكوبون
                                <small class="text-muted">(اختياري - سيتم توليده تلقائياً)</small>
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code') }}"
                                       placeholder="اتركه فارغاً للتوليد التلقائي"
                                       style="text-transform: uppercase;">
                                <button type="button" class="btn btn-outline-primary" id="generateCode">
                                    <i class="ti ti-refresh"></i>
                                </button>
                            </div>
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
                                   value="{{ old('name') }}"
                                   placeholder="مثال: خصم الجمعة البيضاء"
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
                                  rows="3"
                                  placeholder="وصف مختصر للكوبون">{{ old('description') }}</textarea>
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
                                <option value="">اختر نوع الخصم</option>
                                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                                <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
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
                                       value="{{ old('value') }}"
                                       step="0.001"
                                       min="0"
                                       placeholder="0.000"
                                       required>
                                <span class="input-group-text" id="valueUnit">د.ل</span>
                            </div>
                            @error('value')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small id="valueHelp">أدخل المبلغ بالدينار الليبي</small>
                            </div>
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
                                       value="{{ old('minimum_amount') }}"
                                       step="0.001"
                                       min="0"
                                       placeholder="0.000">
                                <span class="input-group-text">د.ل</span>
                            </div>
                            @error('minimum_amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small>الحد الأدنى لمبلغ الفاتورة لاستخدام الكوبون</small>
                            </div>
                        </div>

                        <!-- Usage Limit -->
                        <div class="col-md-6 mb-3">
                            <label for="usage_limit" class="form-label">حد الاستخدام</label>
                            <input type="number" 
                                   class="form-control @error('usage_limit') is-invalid @enderror" 
                                   id="usage_limit" 
                                   name="usage_limit" 
                                   value="{{ old('usage_limit') }}"
                                   min="1"
                                   placeholder="غير محدود">
                            @error('usage_limit')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small>اترك فارغاً للاستخدام غير المحدود</small>
                            </div>
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
                                   value="{{ old('start_date') }}"
                                   min="{{ date('Y-m-d') }}">
                            @error('start_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small>اترك فارغاً للبدء فوراً</small>
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">تاريخ الانتهاء</label>
                            <input type="date" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small>اترك فارغاً للاستخدام بلا انتهاء</small>
                            </div>
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
                                   {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                تفعيل الكوبون
                            </label>
                        </div>
                        <div class="form-text">
                            <small>يمكن تعديل الحالة لاحقاً</small>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-device-floppy me-2"></i>
                                حفظ الكوبون
                            </button>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('coupons.index') }}" class="btn btn-secondary w-100">
                                <i class="ti ti-arrow-left me-2"></i>
                                العودة للقائمة
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="card mt-4">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="ti ti-bulb text-warning me-2"></i>
                    نصائح مفيدة
                </h6>
                <ul class="mb-0 text-muted small">
                    <li>استخدم أكواد واضحة ومعبرة مثل "SUMMER2025" أو "WELCOME10"</li>
                    <li>حدد تاريخ انتهاء لتحفيز العملاء على الاستخدام</li>
                    <li>استخدم الحد الأدنى للمبلغ لزيادة قيمة الطلبات</li>
                    <li>راقب استخدام الكوبونات وعدل الحدود حسب الحاجة</li>
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const valueInput = document.getElementById('value');
    const valueUnit = document.getElementById('valueUnit');
    const valueHelp = document.getElementById('valueHelp');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    // Update value unit and help text based on type
    function updateValueDisplay() {
        if (typeSelect.value === 'percentage') {
            valueUnit.textContent = '%';
            valueHelp.textContent = 'أدخل النسبة المئوية (من 0 إلى 100)';
            valueInput.max = '100';
        } else {
            valueUnit.textContent = 'د.ل';
            valueHelp.textContent = 'أدخل المبلغ بالدينار الليبي';
            valueInput.removeAttribute('max');
        }
    }

    typeSelect.addEventListener('change', updateValueDisplay);
    
    // Initialize on page load
    updateValueDisplay();

    // Generate coupon code
    document.getElementById('generateCode').addEventListener('click', async function() {
        try {
            const response = await fetch('/api/generate-coupon-code');
            const data = await response.json();
            document.getElementById('code').value = data.code;
        } catch (error) {
            console.error('Error generating code:', error);
        }
    });

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