@extends('layouts.app')

@section('title', 'عرض الزبون')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">الزبائن</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $customer->name }}</li>
@endsection

@push('styles')
<style>
    body {
        background-color: #faf8f6;
    }
    
    .customer-card {
        background: white;
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 15px rgba(180, 139, 30, 0.1);
        margin-bottom: 20px;
        border-top: 4px solid #b48b1e;
    }
    
    .customer-card .card-header {
        background: linear-gradient(135deg, #b48b1e 0%, #8a6817 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 20px 25px;
        border: none;
    }
    
    .customer-info-table {
        margin: 0;
    }
    
    .customer-info-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #f1f3f4;
        font-size: 15px;
    }
    .customer-info-table td:first-child {
        color: #6c757d;
        font-weight: 600;
        background: #faf8f6;
        width: 140px;
    }
    
    .notes-box {
        background: #faf8f6;
        border: 2px solid #e8c9c0;
        border-radius: 12px;
        padding: 20px;
        line-height: 1.8;
        font-size: 14px;
    }
    
    .phone-number {
        font-family: 'Courier New', monospace;
        background: #e8f5e8;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
    }
    
    .stats-card {
        background: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: transform 0.2s ease;
        margin-bottom: 15px;
    }
    
    .stats-card:hover {
        transform: translateY(-2px);
    }
    
    .stats-card .card-body {
        padding: 20px;
        text-align: center;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 24px;
    }
    
    .stat-value {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
        font-family: 'Courier New', monospace;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 13px;
        font-weight: 500;
    }
    
    .invoices-table {
        background: white;
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 15px rgba(180, 139, 30, 0.1);
        overflow: hidden;
    }
    
    .table-elegant {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table-elegant thead th {
        background: linear-gradient(135deg, #b48b1e 0%, #8a6817 100%);
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 16px 12px;
        border: none;
    }
    
    .table-elegant tbody td {
        padding: 14px 12px;
        border-bottom: 1px solid #f1f3f4;
        vertical-align: middle;
    }
    
    .table-elegant tbody tr:hover {
        background-color: rgba(180, 139, 30, 0.04);
    }
    
    .invoice-status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid transparent;
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
    
    .amount-display {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        font-size: 13px;
    }
    
    .action-btn {
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.2s ease;
        border: 1px solid #e8c9c0;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .contact-buttons {
        display: grid;
        gap: 10px;
    }
    
    .contact-buttons .btn {
        border-radius: 10px;
        padding: 12px 20px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .contact-buttons .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    
    .empty-invoices {
        text-align: center;
        padding: 40px 20px;
        color: #8a6817;
        background: #faf8f6;
    }
    
    .empty-invoices i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.6;
    }
    
    @media (max-width: 768px) {
        .customer-info-table td:first-child {
            width: 120px;
        }
        
        .contact-buttons {
            grid-template-columns: 1fr;
        }
        
        .stats-card {
            margin-bottom: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 mt-3">
    <div class="row">
        <!-- Customer Info -->
        <div class="col-lg-8">
            <div class="card customer-card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold text-white">
                        <i class="ti ti-user me-2"></i>
                        بيانات الزبون
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <table class="table table-borderless customer-info-table">
                                <tr>
                                    <td>رقم الزبون:</td>
                                    <td><span class="badge bg-primary fs-6">#{{ $customer->id }}</span></td>
                                </tr>
                                <tr>
                                    <td>اسم الزبون:</td>
                                    <td><strong class="fs-5">{{ $customer->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td>رقم الهاتف:</td>
                                    <td><span class="phone-number">{{ $customer->phone }}</span></td>
                                </tr>
                                @if($customer->email)
                                <tr>
                                    <td>البريد الإلكتروني:</td>
                                    <td>{{ $customer->email }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>تاريخ الإنشاء:</td>
                                    <td>{{ $customer->created_at?->format('Y/m/d H:i') }}</td>
                                </tr>
                                @if($customer->updated_at && $customer->updated_at != $customer->created_at)
                                <tr>
                                    <td>آخر تحديث:</td>
                                    <td>{{ $customer->updated_at->format('Y/m/d H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        <div class="col-md-5">
                            @if($customer->notes)
                            <div class="mb-4">
                                <h6 class="text-muted mb-2 fw-bold">
                                    <i class="ti ti-notes me-1"></i> الملاحظات:
                                </h6>
                                <div class="notes-box">
                                    {{ $customer->notes }}
                                </div>
                            </div>
                            @endif

                            <div class="contact-buttons">
                                <a href="tel:{{ $customer->phone }}" class="btn btn-success">
                                    <i class="ti ti-phone me-2"></i>
                                    اتصال مباشر
                                </a>
                                
                                <a href="https://wa.me/218{{ ltrim(preg_replace('/[^0-9]/', '', $customer->phone), '0') }}" 
                                   target="_blank" 
                                   class="btn btn-outline-success">
                                    <i class="ti ti-brand-whatsapp me-2"></i>
                                    إرسال واتساب
                                </a>
                                
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
                                    <i class="ti ti-edit me-2"></i>
                                    تعديل البيانات
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="col-lg-4">
            @php
                $totalInvoices = $customer->invoices->count();
                $totalAmount = $customer->invoices->sum('total');
                $paidAmount = $customer->invoices->sum('paid_amount');
                $remainingAmount = $totalAmount - $paidAmount;
                $unpaidCount = $customer->invoices->where('status', 'unpaid')->count();
                $partialCount = $customer->invoices->where('status', 'partial')->count();
            @endphp
            
            <div class="row">
                <div class="col-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stat-icon bg-primary text-white">
                                <i class="ti ti-receipt"></i>
                            </div>
                            <div class="stat-value text-primary">{{ $totalInvoices }}</div>
                            <div class="stat-label">إجمالي الفواتير</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stat-icon bg-success text-white">
                                <i class="ti ti-currency-dollar"></i>
                            </div>
                            <div class="stat-value text-success">{{ number_format($totalAmount, 0) }}</div>
                            <div class="stat-label">إجمالي المبالغ (د.ل)</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stat-icon bg-warning text-white">
                                <i class="ti ti-alert-circle"></i>
                            </div>
                            <div class="stat-value text-warning">{{ number_format($remainingAmount, 0) }}</div>
                            <div class="stat-label">المستحقات (د.ل)</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stat-icon bg-info text-white">
                                <i class="ti ti-clock"></i>
                            </div>
                            <div class="stat-value text-info">{{ $unpaidCount + $partialCount }}</div>
                            <div class="stat-label">فواتير معلقة</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card invoices-table">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="ti ti-receipt me-2"></i>
                            فواتير الزبون
                        </h5>
                        <a href="{{ route('invoices.create') }}?customer_id={{ $customer->id }}" class="btn btn-light btn-sm">
                            <i class="ti ti-plus me-1"></i>
                            إنشاء فاتورة جديدة
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($customer->invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-elegant">
                                <thead>
                                    <tr>
                                        <th>رقم الفاتورة</th>
                                        <th>التاريخ</th>
                                        <th>المبلغ الكلي</th>
                                        <th>المبلغ المدفوع</th>
                                        <th>المبلغ المتبقي</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->invoices->sortByDesc('created_at') as $invoice)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">{{ $invoice->invoice_number }}</span>
                                            </td>
                                            <td class="text-muted">{{ $invoice->invoice_date->format('Y/m/d') }}</td>
                                            <td class="amount-display">{{ number_format($invoice->total, 3) }} د.ل</td>
                                            <td class="amount-display text-success">{{ number_format($invoice->paid_amount, 3) }} د.ل</td>
                                            <td class="amount-display text-warning">{{ number_format($invoice->remaining_amount, 3) }} د.ل</td>
                                            <td>
                                                <span class="invoice-status-badge status-{{ $invoice->status }}">
                                                    @switch($invoice->status)
                                                        @case('cancelled') ملغية @break
                                                        @case('partial') مدفوعة جزئي @break
                                                        @case('unpaid') غير مدفوعة @break
                                                        @default {{ $invoice->status }} @break
                                                    @endswitch
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('invoices.show', $invoice) }}" 
                                                       class="btn btn-sm btn-outline-info action-btn" 
                                                       title="عرض">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    
                                                    @if($invoice->status !== 'cancelled')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-success action-btn"
                                                                onclick="showPaymentModal({{ $invoice->id }}, {{ $invoice->remaining_amount }})"
                                                                title="إضافة دفعة">
                                                            <i class="ti ti-cash"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    <a href="{{ route('invoices.show', $invoice) }}?print=1" 
                                                       class="btn btn-sm btn-outline-secondary action-btn" 
                                                       target="_blank" 
                                                       title="طباعة">
                                                        <i class="ti ti-printer"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-invoices">
                            <i class="ti ti-file-off"></i>
                            <h5 class="fw-bold">لا توجد فواتير</h5>
                            <p class="text-muted mb-3">لم يتم إنشاء أي فاتورة لهذا الزبون بعد</p>
                            <a href="{{ route('invoices.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary">
                                <i class="ti ti-plus"></i> إنشاء فاتورة جديدة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <div class="d-flex gap-2 justify-content-center flex-wrap">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left me-2"></i>
                    العودة للقائمة
                </a>
                
                <form method="POST" 
                      action="{{ route('customers.destroy', $customer) }}" 
                      class="d-inline"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الزبون؟ سيتم حذف جميع فواتيره أيضاً!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="ti ti-trash me-2"></i>
                        حذف الزبون
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal (same as in invoices page) -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <form id="paymentForm" method="POST">
                @csrf
                <div class="modal-header" style="background: #b48b1e; color: white; border: none; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title fw-bold">
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
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i> إلغاء
                    </button>
                    <button type="submit" class="btn btn-success">
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
</script>
@endpush