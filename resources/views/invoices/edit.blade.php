@extends('layouts.app')

@section('title', 'تعديل فاتورة')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">الفواتير</a></li>
    <li class="breadcrumb-item active" aria-current="page">
        تعديل فاتورة {{ $invoice->invoice_number ?? ('#' . $invoice->id) }}
    </li>
@endsection

@section('content')
<div class="container-fluid">
    <invoice-component :invoice-id="{{ $invoice->id }}"></invoice-component>
</div>
@endsection

@push('scripts')
@vite(['resources/js/app.js'])
@endpush
