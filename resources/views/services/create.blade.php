@extends('layouts.app')

@section('title', 'إضافة خدمة جديدة')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('services.index') }}">الخدمات</a></li>
    <li class="breadcrumb-item active" aria-current="page">إضافة خدمة جديدة</li>
@endsection

@push('styles')
<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-plus me-2"></i>
                    إضافة خدمة جديدة
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('services.store') }}" method="POST">
                    @csrf

                    <div class="form-section">
                        <div class="mb-3">
                            <label class="form-label">اسم الخدمة <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="اكتب اسم الخدمة..." required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">السعر <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="form-control @error('price') is-invalid @enderror" placeholder="0.00" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">مفعل</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('services.index') }}" class="btn btn-light">
                            <i class="ti ti-arrow-right"></i> إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy"></i> حفظ الخدمة
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection
