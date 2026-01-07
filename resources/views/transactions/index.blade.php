@extends('layouts.app')

@section('title', 'إدارة المعاملات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active" aria-current="page">المعاملات</li>
@endsection

@section('content')
<div class="row g-4">
   <!-- Treasury Balance -->
   <div class="col-md-4">
      @if($treasury)
      <div class="card shadow-sm border-0">
         <div class="card-body text-center">
            <h6 class="text-muted mb-2">💰 رصيد الخزينة</h6>
            <h4 class="fw-bold text-primary mb-1">{{ number_format($treasury->balance, 2) }} د.ل</h4>
            <small class="text-muted">{{ $treasury->name }}</small>
         </div>
      </div>
      @endif
   </div>

   <!-- Filters -->
   <div class="col-md-8">
      <div class="card shadow-sm border-0">
         <div class="card-body">
            <form method="GET" action="{{ route('transactions.index') }}" class="row g-3 align-items-end">
               <div class="col-md-3">
                  <label class="form-label">النوع</label>
                  <select name="type" class="form-select">
                      <option value="">الكل</option>
                      <option value="deposit" {{ request('type')=='deposit' ? 'selected' : '' }}>إيداع</option>
                      <option value="withdrawal" {{ request('type')=='withdrawal' ? 'selected' : '' }}>سحب</option>
                  </select>
               </div>
               <div class="col-md-3">
                  <label class="form-label">من تاريخ</label>
                  <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
               </div>
               <div class="col-md-3">
                  <label class="form-label">إلى تاريخ</label>
                  <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
               </div>
               <div class="col-md-3">
                  <button type="submit" class="btn btn-primary w-100">
                     <i class="ti ti-search"></i> بحث
                  </button>
               </div>
            </form>
         </div>
      </div>
   </div>

   <!-- Transactions Table -->
   <div class="col-12">
      <div class="card shadow-sm border-0">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="ti ti-transfer me-2"></i>
                قائمة المعاملات
            </h5>
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> إضافة معاملة
            </a>
         </div>

         <div class="card-body">
            <div class="table-responsive">
               <table class="table table-hover align-middle">
                   <thead class="table-light">
                       <tr>
                           <th>#</th>
                           <th>الخزينة</th>
                           <th>النوع</th>
                           <th>المبلغ</th>
                           <th>الوصف</th>
                           <th>الفاتورة / الزبون</th>
                           <th>المُعِد</th>
                           <th>التاريخ</th>
                       </tr>
                   </thead>
                   <tbody>
                       @forelse($transactions as $transaction)
                           <tr>
                               <td>{{ $transaction->id }}</td>
                               <td>{{ $transaction->treasury->name }}</td>
                               <td>
                                   @if($transaction->type === 'deposit')
                                       <span class="badge bg-success">إيداع</span>
                                   @else
                                       <span class="badge bg-danger">سحب</span>
                                   @endif
                               </td>
                               <td class="fw-bold text-dark">{{ number_format($transaction->amount, 2) }}</td>
                               <td>{{ $transaction->description ?? '-' }}</td>
                               <td>
                                   @if($transaction->invoice_id)
                                       <div>
                                           <a href="{{ route('invoices.show', $transaction->invoice_id) }}" class="text-primary fw-bold">
                                               فاتورة #{{ $transaction->invoice->invoice_number ?? $transaction->invoice_id }}
                                           </a>
                                           @if(optional($transaction->invoice->customer)->name)
                                               <div class="small text-muted">
                                                   {{ $transaction->invoice->customer->name }}
                                               </div>
                                           @endif
                                       </div>
                                   @else
                                       -
                                   @endif
                               </td>
                               <td>
                                   @if($transaction->user)
                                       <span class="badge bg-light text-dark">
                                           <i class="ti ti-user me-1"></i>
                                           {{ $transaction->user->name }}
                                       </span>
                                   @else
                                       <span class="text-muted">-</span>
                                   @endif
                               </td>
                               <td>{{ $transaction->created_at?->format('Y/m/d H:i') }}</td>
                           </tr>
                       @empty
                           <tr>
                               <td colspan="8" class="text-center py-4 text-muted">
                                   <i class="ti ti-file-off fs-1 d-block mb-2"></i>
                                   لا توجد معاملات مطابقة
                               </td>
                           </tr>
                       @endforelse
                   </tbody>
               </table>
            </div>

            @if($transactions->hasPages())
                <div class="mt-3">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
         </div>
      </div>
   </div>
</div>
@endsection
