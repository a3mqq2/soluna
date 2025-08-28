@extends('layouts.app')

@section('title', 'إضافة زبون جديد')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">الزبائن</a></li>
    <li class="breadcrumb-item active" aria-current="page">إضافة زبون جديد</li>
@endsection


@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">

        <div class="card form-card">

            <div class="card-body p-4">
                <form method="POST" action="{{ route('customers.store') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            اسم الزبون <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ti ti-user"></i>
                            </span>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="أدخل اسم الزبون"
                                   required>
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- رقم الهاتف -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            رقم الهاتف <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ti ti-phone"></i>
                            </span>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   placeholder="أدخل رقم الهاتف"
                                   required>
                        </div>
                        @error('phone')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle me-1"></i>
                            مثال: 01234567890 أو +966501234567
                        </div>
                    </div>

                    <!-- الملاحظات -->
                    <div class="mb-4">
                        <label for="notes" class="form-label">
                            الملاحظات
                        </label>
                        <div class="input-group">
                            <span class="input-group-text align-items-start pt-2">
                                <i class="ti ti-notes"></i>
                            </span>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="4"
                                      placeholder="أضف أي ملاحظات إضافية حول الزبون (اختياري)">{{ old('notes') }}</textarea>
                        </div>
                        @error('notes')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">
                            <i class="ti ti-info-circle me-1"></i>
                            يمكنك إضافة معلومات إضافية مثل العنوان، تفضيلات الزبون، أو أي ملاحظات مهمة
                        </div>
                    </div>

                    <!-- أزرار التحكم -->
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-device-floppy me-2"></i>
                                حفظ الزبون
                            </button>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary w-100">
                                <i class="ti ti-arrow-left me-2"></i>
                                العودة للقائمة
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <!-- نصائح مفيدة -->
        <div class="card mt-4">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="ti ti-bulb text-warning me-2"></i>
                    نصائح مفيدة
                </h6>
                <ul class="mb-0 text-muted small">
                    <li>تأكد من كتابة اسم الزبون بشكل واضح ومفهوم</li>
                    <li>أدخل رقم هاتف صحيح للتواصل مع الزبون</li>
                    <li>استخدم حقل الملاحظات لحفظ معلومات مهمة عن الزبون</li>
                    <li>يمكنك تعديل هذه المعلومات لاحقاً من صفحة تحرير الزبون</li>
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تنسيق رقم الهاتف أثناء الكتابة
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // إزالة كل شيء عدا الأرقام
            
            // إضافة التنسيق حسب طول الرقم
            if (value.length > 0) {
                if (value.startsWith('966')) {
                    // رقم سعودي
                    value = '+966' + value.substring(3);
                } else if (value.startsWith('20')) {
                    // رقم مصري
                    value = '+20' + value.substring(2);
                }
            }
            
            e.target.value = value;
        });
    }

    // التركيز على حقل الاسم عند تحميل الصفحة
    const nameInput = document.getElementById('name');
    if (nameInput) {
        nameInput.focus();
    }

    // تأكيد قبل المغادرة إذا كانت هناك بيانات مدخلة
    let formChanged = false;
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            formChanged = true;
        });
    });

    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // إزالة التأكيد عند إرسال النموذج
    form.addEventListener('submit', () => {
        formChanged = false;
    });
});
</script>
@endpush