@extends('layouts.app')

@section('title', 'إضافة معاملة')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">المعاملات</a></li>
    <li class="breadcrumb-item active" aria-current="page">إضافة معاملة</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-plus me-2"></i>
                    إضافة معاملة جديدة
                </h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('transactions.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">الخزينة</label>
                        <select name="treasury_id" class="form-select" required>
                            @foreach($treasuries as $treasury)
                                <option value="{{ $treasury->id }}">{{ $treasury->name }}</option>
                            @endforeach
                        </select>
                        @error('treasury_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">النوع</label>
                        <select name="type" class="form-select" required>
                            <option value="deposit">إيداع</option>
                            <option value="withdrawal">سحب</option>
                        </select>
                        @error('type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المبلغ</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                        @error('amount')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الوصف (اختياري)</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                        @error('description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <a href="{{ route('transactions.index') }}" class="btn btn-light">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check"></i> حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
