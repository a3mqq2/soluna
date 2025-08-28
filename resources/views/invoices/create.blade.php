@extends('layouts.app')

@section('title', 'إنشاء فاتورة جديدة')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">المناسبات</a></li>
    <li class="breadcrumb-item active" aria-current="page">إنشاء فاتورة</li>
@endsection

@section('content')
<div class="container-fluid">
    <invoice-component></invoice-component>
</div>
@endsection

@push('scripts')
@vite(['resources/js/app.js'])
@endpush