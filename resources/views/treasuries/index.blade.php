@extends('layouts.app')

@section('title', 'الخزينة الرئيسية')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active" aria-current="page">الخزينة</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-cash me-2"></i>
                    الخزينة الرئيسية
                </h5>
            </div>

            <div class="card-body">
                @if($treasury)
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>الاسم</th>
                                <th>الرصيد الحالي</th>
                                <th>آخر تحديث</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $treasury->name }}</td>
                                <td class="fw-bold">{{ number_format($treasury->balance, 2) }}</td>
                                <td>{{ $treasury->updated_at?->format('Y/m/d H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning text-center">
                        لا توجد خزينة بعد.
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
