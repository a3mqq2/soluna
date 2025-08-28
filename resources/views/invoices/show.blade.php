@extends('layouts.app')

@section('title', 'عرض الفاتورة - ' . $invoice->invoice_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">المناسبات</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $invoice->invoice_number }}</li>
@endsection

@push('styles')
<style>
    body {
        background-color: #faf8f6;
    }
    
    .invoice-header {
        background: linear-gradient(135deg, #b48b1e 0%, #8a6817 100%);
        color: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(180, 139, 30, 0.2);
    }
    
    .invoice-card {
        background: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(180, 139, 30, 0.1);
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .invoice-card .card-header {
        background: #faf8f6;
        border-bottom: 2px solid #e8c9c0;
        padding: 18px 24px;
        border-radius: 0;
    }
    
    .invoice-card .card-header h6 {
        color: #b48b1e;
        font-weight: 700;
        margin: 0;
        font-size: 16px;
    }
    
    .invoice-status-badge {
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 2px solid transparent;
    }
    
    .status-cancelled { 
        background-color: #f8d7da; 
        color: #721c24; 
        border-color: #f5c6cb;
    }
    .status-partial { 
        background-color: #fff3cd; 
        color: #856404; 
        border-color: #ffeaa7;
    }
    .status-unpaid { 
        background-color: #e8c9c0; 
        color: #b48b1e; 
        border-color: #b48b1e;
    }
    
    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 4px;
    }
    
    .info-value {
        font-size: 16px;
        color: #495057;
        margin-bottom: 15px;
    }
    
    .amount-display {
        font-family: 'Courier New', monospace;
        font-weight: 700;
        font-size: 18px;
        color: #b48b1e;
    }
    
    .table-elegant {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table-elegant thead th {
        background-color: #faf8f6;
        color: #5a5a5a;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 15px 12px;
        border: none;
        border-bottom: 2px solid #e8c9c0;
    }
    
    .table-elegant tbody td {
        padding: 12px;
        vertical-align: middle;
        border: none;
        border-bottom: 1px solid #f5f3f1;
    }
    
    .table-elegant tbody tr:hover {
        background-color: #faf8f6;
    }
    
    .payment-method-badge {
        background: #e8c9c0;
        color: #b48b1e;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .summary-card {
        background: linear-gradient(135deg, #b48b1e 0%, #8a6817 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    
    .summary-item:last-child {
        border-bottom: none;
        font-weight: 700;
        font-size: 18px;
        padding-top: 15px;
    }
    
    .btn-elegant {
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 20px;
        transition: all 0.2s ease;
        border: none;
    }
    
    .btn-elegant:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .btn-primary {
        background: #b48b1e;
        border-color: #b48b1e;
    }
    
    .btn-primary:hover {
        background: #8a6817;
        border-color: #8a6817;
    }
    
    .empty-payments {
        text-align: center;
        padding: 40px 20px;
        color: #8a6817;
    }
    
    .empty-payments i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.6;
        color: #b48b1e;
    }
    
    .actions-sticky {
        position: sticky;
        top: 20px;
        z-index: 100;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            background: white !important;
        }
        
        .invoice-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
    
    @media (max-width: 768px) {
        .invoice-header {
            text-align: center;
            padding: 20px;
        }
        
        .summary-card {
            margin-top: 20px;
        }
        
        .actions-sticky {
            position: static;
        }
        
        .table-responsive {
            font-size: 14px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-3 px-4">

    <div class="row">
        <!-- Left Column - Invoice Details -->
        <div class="col-lg-8">
            <!-- Customer Information -->
            <div class="invoice-card">
                <div class="card-header">
                    <h6><i class="ti ti-user me-2"></i>معلومات الزبون</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-elegant mb-0">
                            <tbody>
                                <tr>
                                    <th width="25%" style="background-color: #faf8f6; color: #b48b1e; font-weight: 600;">اسم الزبون</th>
                                    <td>{{ $invoice->customer->name }}</td>
                                </tr>
                                <tr>
                                    <th width="25%" style="background-color: #faf8f6; color: #b48b1e; font-weight: 600;">رقم الهاتف</th>
                                    <td>{{ $invoice->customer->phone }}</td>
                                </tr>
                                @if($invoice->customer->email)
                                <tr>
                                    <th width="25%" style="background-color: #faf8f6; color: #b48b1e; font-weight: 600;">البريد الإلكتروني</th>
                                    <td>{{ $invoice->customer->email }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th width="25%" style="background-color: #faf8f6; color: #b48b1e; font-weight: 600;">رقم الفاتورة</th>
                                    <td><span class="badge" style="background: #b48b1e; color: white; padding: 4px 8px; border-radius: 8px;">{{ $invoice->invoice_number }}</span></td>
                                </tr>
                                <tr>
                                    <th width="25%" style="background-color: #faf8f6; color: #b48b1e; font-weight: 600;">تاريخ الفاتورة</th>
                                    <td>{{ $invoice->invoice_date->format('Y/m/d') }}</td>
                                </tr>
                                <tr>
                                    <th width="25%" style="background-color: #faf8f6; color: #b48b1e; font-weight: 600;">حالة الفاتورة</th>
                                    <td>
                                        <span class="invoice-status-badge status-{{ $invoice->status }}">
                                            @switch($invoice->status)
                                                @case('cancelled') ملغية @break
                                                @case('partial') مدفوعة جزئي @break
                                                @case('unpaid') غير مدفوعة @break
                                                @case('paid')  مدفوعة @break
                                                @default {{ $invoice->status }} @break
                                            @endswitch
                                        </span>
                                    </td>
                                </tr>
                                @if($invoice->notes)
                                <tr>
                                    <th width="25%" style="background-color: #faf8f6; color: #b48b1e; font-weight: 600;">ملاحظات</th>
                                    <td>{{ $invoice->notes }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="invoice-card">
                <div class="card-header">
                    <h6><i class="ti ti-list me-2"></i>بنود الفاتورة</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-elegant">
                            <thead>
                                <tr>
                                    <th>الخدمة</th>
                                    <th width="100">الكمية</th>
                                    <th width="120">السعر</th>
                                    <th width="120">المجموع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->service->name }}</div>
                                        @if($item->service->description)
                                            <small class="text-muted">{{ $item->service->description }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($item->quantity) }}</td>
                                    <td class="amount-display">{{ number_format($item->unit_price, 3) }} د.ل</td>
                                    <td class="amount-display">{{ number_format($item->total_price, 3) }} د.ل</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payments History -->
            <div class="invoice-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6><i class="ti ti-credit-card me-2"></i>سجل المدفوعات</h6>
                    @if($invoice->status !== 'cancelled')
                        <button class="btn btn-sm btn-primary btn-elegant no-print" 
                                onclick="showPaymentModal({{ $invoice->id }}, {{ $invoice->remaining_amount }})">
                            <i class="ti ti-plus me-1"></i>
                            إضافة دفعة
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($invoice->payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-elegant">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>المبلغ</th>
                                        <th>طريقة الدفع</th>
                                        <th>رقم المرجع</th>
                                        <th>الملاحظات</th>
                                        <th width="100" class="no-print">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->payments()->latest('payment_date')->get() as $payment)
                                    <tr>
                                        <td>{{ $payment->formatted_date }}</td>
                                        <td class="amount-display">{{ $payment->formatted_amount }}</td>
                                        <td>
                                            <span class="payment-method-badge">{{ $payment->payment_method_name }}</span>
                                        </td>
                                        <td>{{ $payment->reference_number ?: '-' }}</td>
                                        <td>{{ Str::limit($payment->notes, 30) ?: '-' }}</td>
                                        <td class="no-print">
                                          <a href="{{route('payments.receipt', $payment)}}" class="btn btn-primary btn-sm text-light"><i class="fa fa-print"></i></a>
                                            @if($payment->status === 'completed')
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDeletePayment({{ $payment->id }})"
                                                        title="حذف الدفعة">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-payments">
                            <i class="ti ti-cash-off"></i>
                            <h6>لا توجد مدفوعات</h6>
                            <p class="text-muted">لم يتم تسجيل أي دفعة لهذه الفاتورة بعد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Summary & Actions -->
        <div class="col-lg-4">
            <div class="actions-sticky">
                <!-- Actions Card -->
                <div class="invoice-card no-print mb-3">
                    <div class="card-body text-center">
                        <div class="d-grid gap-2">
                            <a href="{{ route('invoices.show', $invoice) }}?print=1" 
                               target="_blank" 
                               class="btn btn-primary btn-elegant">
                                <i class="ti ti-printer me-2"></i>طباعة الفاتورة
                            </a>
                            
                            @if($invoice->status !== 'cancelled')
                                <button class="btn btn-success btn-elegant" 
                                        onclick="showPaymentModal({{ $invoice->id }}, {{ $invoice->remaining_amount }})">
                                    <i class="ti ti-cash me-2"></i>إضافة دفعة
                                </button>
                            @endif
                            
                            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-elegant">
                                <i class="ti ti-arrow-right me-2"></i>العودة للقائمة
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="summary-card">
                    <h6 class="mb-3 text-white">
                        <i class="ti ti-calculator me-2"></i>
                        ملخص الفاتورة
                    </h6>
                    
                    <div class="summary-item">
                        <span>المجموع الفرعي:</span>
                        <span>{{ number_format($invoice->subtotal, 3) }} د.ل</span>
                    </div>
                    
                    @if($invoice->discount > 0)
                    <div class="summary-item">
                        <span>الخصم:</span>
                        <span>- {{ number_format($invoice->discount, 3) }} د.ل</span>
                    </div>
                    @endif
                    
                    <div class="summary-item">
                        <span>إجمالي الفاتورة:</span>
                        <span>{{ number_format($invoice->total, 3) }} د.ل</span>
                    </div>
                    
                    <div class="summary-item">
                        <span>المبلغ المدفوع:</span>
                        <span>{{ number_format($invoice->total_paid, 3) }} د.ل</span>
                    </div>
                    
                    <div class="summary-item">
                        <span>المبلغ المتبقي:</span>
                        <span>{{ number_format($invoice->remaining_amount, 3) }} د.ل</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <form id="paymentForm" method="POST">
                @csrf
                <div class="modal-header" style="background: #b48b1e; color: white; border: none; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title text-white fw-bold">
                        <i class="ti ti-cash me-2"></i>
                        إضافة دفعة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">المبلغ المتبقي</label>
                        <input type="text" id="remainingAmount" class="form-control" readonly style="background-color: #faf8f6; border-color: #e8c9c0;">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">مبلغ الدفعة</label>
                                <input type="number" name="amount" step="0.001" min="0.001" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">طريقة الدفع</label>
                                <select name="payment_method" class="form-select" required onchange="toggleReferenceField()">
                                    <option value="">اختر طريقة الدفع</option>
                                    <option value="cash">نقداً</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="check">شيك</option>
                                    <option value="credit_card">بطاقة ائتمان</option>
                                    <option value="other">أخرى</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="referenceField" style="display: none;">
                        <label class="form-label fw-semibold">
                            <span id="referenceLabel">رقم المرجع</span>
                        </label>
                        <input type="text" name="reference_number" class="form-control" placeholder="أدخل رقم المرجع/المعاملة">
                        <small class="text-muted" id="referenceHelper">اختياري - يمكن تركه فارغاً</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">تاريخ الدفعة</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ملاحظات الدفعة</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="أضف ملاحظات الدفعة (اختياري)"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-outline-secondary btn-elegant" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i> إلغاء
                    </button>
                    <button type="submit" class="btn btn-success btn-elegant">
                        <i class="ti ti-check"></i> حفظ الدفعة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showPaymentModal(invoiceId, remainingAmount) {
    document.getElementById('paymentForm').reset();
    document.getElementById('referenceField').style.display = 'none';
    document.getElementById('remainingAmount').value = remainingAmount.toFixed(3) + ' د.ل';
    document.getElementById('paymentForm').action = `/invoices/${invoiceId}/payment`;
    document.querySelector('input[name="payment_date"]').value = new Date().toISOString().split('T')[0];
    
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

function toggleReferenceField() {
    const paymentMethod = document.querySelector('select[name="payment_method"]').value;
    const referenceField = document.getElementById('referenceField');
    const referenceLabel = document.getElementById('referenceLabel');
    const referenceHelper = document.getElementById('referenceHelper');
    const referenceInput = document.querySelector('input[name="reference_number"]');
    
    if (paymentMethod === 'bank_transfer') {
        referenceField.style.display = 'block';
        referenceLabel.textContent = 'رقم التحويل البنكي';
        referenceHelper.textContent = 'أدخل رقم المعاملة أو المرجع من البنك';
        referenceInput.placeholder = 'رقم التحويل البنكي';
    } else if (paymentMethod === 'check') {
        referenceField.style.display = 'block';
        referenceLabel.textContent = 'رقم الشيك';
        referenceHelper.textContent = 'أدخل رقم الشيك';
        referenceInput.placeholder = 'رقم الشيك';
    } else if (paymentMethod === 'credit_card') {
        referenceField.style.display = 'block';
        referenceLabel.textContent = 'رقم المعاملة';
        referenceHelper.textContent = 'آخر 4 أرقام من البطاقة أو رقم المعاملة';
        referenceInput.placeholder = 'رقم المعاملة';
    } else if (paymentMethod === 'other') {
        referenceField.style.display = 'block';
        referenceLabel.textContent = 'رقم المرجع';
        referenceHelper.textContent = 'أضف رقم مرجع أو ملاحظة تعريفية';
        referenceInput.placeholder = 'رقم المرجع أو الملاحظة';
    } else {
        referenceField.style.display = 'none';
        referenceInput.value = '';
    }
}

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const amount = parseFloat(formData.get('amount'));
    const remainingAmountText = document.getElementById('remainingAmount').value;
    const remainingAmount = parseFloat(remainingAmountText.replace(/[^\d.]/g, ''));
    
    if (!amount || amount <= 0) {
        if (window.toastr) {
            toastr.error('يرجى إدخال مبلغ صالح');
        } else {
            alert('يرجى إدخال مبلغ صالح');
        }
        return;
    }
    
    if (amount > remainingAmount) {
        if (window.toastr) {
            toastr.error('لا يمكن أن يكون مبلغ الدفعة أكبر من المبلغ المتبقي');
        } else {
            alert('لا يمكن أن يكون مبلغ الدفعة أكبر من المبلغ المتبقي');
        }
        return;
    }
    
    if (!formData.get('payment_method')) {
        if (window.toastr) {
            toastr.error('يرجى اختيار طريقة الدفع');
        } else {
            alert('يرجى اختيار طريقة الدفع');
        }
        return;
    }
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>جاري الحفظ...';
    submitBtn.disabled = true;
    
    this.submit();
});

function confirmDeletePayment(paymentId) {
    if (confirm('هل أنت متأكد من حذف هذه الدفعة؟ هذا الإجراء لا يمكن التراجع عنه.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/payments/${paymentId}`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush