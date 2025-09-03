@extends('layouts.app')

@section('title', 'إدارة المناسبات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active" aria-current="page">المناسبات</li>
@endsection

@push('styles')
<style>
    body {
        background-color: #faf8f6;
    }
    
    .invoice-status-badge {
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 12px;
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
        color: #694c00; 
        border-color: #b48b1e;
    }


    .status-paid { 
        background-color: #379627; 
        color: #ffffff; 
        border-color: #10691f;
    }

    
    .amount-cell { 
        font-family: 'Courier New', monospace; 
        font-weight: 600; 
        font-size: 14px;
    }
    
    .filter-card {
        background: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(180, 139, 30, 0.1);
        margin-bottom: 20px;
        border-top: 3px solid #b48b1e;
    }
    
    .filter-section {
        padding: 20px;
    }
    
    .filter-toggle {
        background: none;
        border: none;
        color: #b48b1e;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        font-size: 16px;
    }
    
    .filter-toggle:hover {
        color: #8a6817;
    }
    
    .filter-toggle[aria-expanded="true"] .toggle-icon {
        transform: rotate(180deg);
    }
    
    .toggle-icon {
        transition: transform 0.2s ease;
        color: #b48b1e;
    }
    
    .main-table-card {
        background: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(180, 139, 30, 0.1);
        overflow: hidden;
    }
    
    .main-table-card .card-header {
        background: linear-gradient(135deg, #b48b1e 0%, #8a6817 100%);
        border: none;
        color: white;
        padding: 20px 24px;
    }
    
    .table-elegant {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table-elegant thead th {
        background-color: #faf8f6;
        border: none;
        color: #5a5a5a;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 18px 16px;
        vertical-align: middle;
        border-bottom: 2px solid #e8c9c0;
    }
    
    .table-elegant tbody td {
        border: none;
        border-bottom: 1px solid #f5f3f1;
        padding: 16px;
        vertical-align: middle;
        background: white;
    }
    
    .table-elegant tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-elegant tbody tr:hover {
        background-color: #faf8f6;
        transform: translateX(-2px);
        box-shadow: 4px 0 8px rgba(180, 139, 30, 0.1);
    }
    
    .action-buttons .btn {
        border-radius: 8px;
        padding: 8px 12px;
        transition: all 0.2s ease;
        margin: 0 2px;
        border: 1px solid #e8c9c0;
    }
    
    .action-buttons .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .btn-outline-info {
        color: #b48b1e;
        border-color: #b48b1e;
    }
    
    .btn-outline-info:hover {
        background-color: #b48b1e;
        border-color: #b48b1e;
        color: white;
    }
    
    .btn-outline-success {
        color: #28a745;
        border-color: #28a745;
    }
    
    .btn-outline-success:hover {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    .invoice-number-badge {
        background: #b48b1e;
        color: white;
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    
    .customer-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .customer-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e8c9c0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #b48b1e;
        font-weight: 700;
        font-size: 14px;
        border: 2px solid #b48b1e;
    }
    
    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: #8a6817;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 20px;
        opacity: 0.6;
        color: #b48b1e;
    }
    
    .filter-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #e8c9c0;
        color: #b48b1e;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        margin: 2px;
        font-weight: 600;
    }
    
    .filter-badge .remove-filter {
        background: none;
        border: none;
        color: inherit;
        padding: 0;
        margin-left: 5px;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
    }
    
    .search-input {
        border-radius: 10px;
        border: 2px solid #e8c9c0;
        padding: 10px 16px;
        transition: all 0.2s ease;
    }
    
    .search-input:focus {
        border-color: #b48b1e;
        box-shadow: 0 0 0 3px rgba(180, 139, 30, 0.1);
        outline: none;
    }
    
    .btn-primary {
        background: #b48b1e;
        border-color: #b48b1e;
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 20px;
        transition: all 0.2s ease;
    }
    
    .btn-primary:hover {
        background: #8a6817;
        border-color: #8a6817;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(180, 139, 30, 0.3);
    }
    
    .btn-outline-secondary {
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 20px;
    }
    
    .form-select, .form-control {
        border-radius: 8px;
        border: 1px solid #e8c9c0;
        transition: all 0.2s ease;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #b48b1e;
        box-shadow: 0 0 0 2px rgba(180, 139, 30, 0.1);
    }
    
    @media (max-width: 768px) {
        .filter-section {
            padding: 15px;
        }
        
        .table-responsive {
            border-radius: 0;
        }
        
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .action-buttons .btn {
            font-size: 12px;
            padding: 6px 8px;
        }
        
        .customer-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Filters Card -->
    <div class="card filter-card">
        <div class="card-body p-0">
            <div class="filter-section">
                <button class="filter-toggle w-100 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#filterSection" aria-expanded="false">
                    <i class="ti ti-filter"></i>
                    <span>البحث والتصفية</span>
                    <i class="ti ti-chevron-down toggle-icon ms-auto"></i>
                </button>
                
                <div class="collapse" id="filterSection">
                    <hr class="my-3" style="border-color: #e8c9c0;">
                    <form method="GET" id="filterForm" class="row g-3">
                        <!-- Search -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">البحث</label>
                            <div class="position-relative">
                                <i class="ti ti-search position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); z-index: 10; color: #b48b1e;"></i>
                                <input type="text" 
                                       name="search" 
                                       class="form-control search-input" 
                                       placeholder="رقم الفاتورة، اسم الزبون، أو الهاتف..."
                                       value="{{ request('search') }}"
                                       style="padding-right: 40px;">
                            </div>
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">جميع الحالات</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>مدفوعة جزئي</option>
                                <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>غير مدفوعة</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}> مدفوعة</option>
                            </select>
                        </div>
                        
                        <!-- Date Range -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">من تاريخ</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">إلى تاريخ</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="col-12">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> تطبيق التصفية
                                </button>
                                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh"></i> مسح التصفية
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Active Filters -->
                    @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                        <div class="mt-3">
                            <small class="text-muted fw-semibold">التصفيات النشطة:</small>
                            <div class="mt-2">
                                @if(request('search'))
                                    <span class="filter-badge">
                                        البحث: {{ request('search') }}
                                        <button type="button" class="remove-filter" onclick="removeFilter('search')">×</button>
                                    </span>
                                @endif
                                @if(request('status'))
                                    <span class="filter-badge">
                                        الحالة: {{ request('status') }}
                                        <button type="button" class="remove-filter" onclick="removeFilter('status')">×</button>
                                    </span>
                                @endif
                                @if(request('date_from') || request('date_to'))
                                    <span class="filter-badge">
                                        التاريخ: {{ request('date_from') ?: '...' }} - {{ request('date_to') ?: '...' }}
                                        <button type="button" class="remove-filter" onclick="removeFilter(['date_from', 'date_to'])">×</button>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="card main-table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 fw-bold text-white">
                        <i class="ti ti-receipt me-2"></i>
                        قائمة المناسبات
                    </h5>
                    <small class="opacity-75">عرض {{ $invoices->count() }} من أصل {{ $invoices->total() }} فاتورة</small>
                </div>
                <a href="{{ route('invoices.create') }}" class="btn btn-light">
                    <i class="ti ti-plus"></i> إنشاء فاتورة جديدة
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-elegant">
                    <thead>
                        <tr>
                            <th>رقم الفاتورة</th>
                            <th>الزبون</th>
                            <th>التاريخ</th>
                            <th>المبلغ الكلي</th>
                            <th>المبلغ المدفوع</th>
                            <th>المبلغ المتبقي</th>
                            <th> صافي الربح </th>
                            <th>الحالة</th>
                            <th width="220" class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>
                                    <span class="invoice-number-badge">
                                        {{ $invoice->invoice_number }}
                                    </span>
                                </td>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-avatar">
                                            {{ mb_substr($invoice->customer->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold" style="color: #5a5a5a;">{{ $invoice->customer->name }}</div>
                                            <small class="text-muted">{{ $invoice->customer->phone }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $invoice->invoice_date }}</td>
                                <td class="amount-cell" style="color: #b48b1e;">{{ number_format($invoice->total, 2) }} د.ل</td>
                                <td class="amount-cell text-success">{{ number_format($invoice->paid_amount, 2) }} د.ل</td>
                                <td class="amount-cell text-warning">{{ number_format($invoice->remaining_amount, 2) }} د.ل</td>
                                <td class="amount-cell" style="color: #379627;">{{ number_format($invoice->net_profit, 2) }} د.ل</td>
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
                                <td class="text-center">
                                    <div class="action-buttons d-flex gap-1 justify-content-center">
                                        <a href="{{ route('invoices.show', $invoice) }}" 
                                           class="btn btn-outline-info btn-sm" title="عرض">
                                            <i class="ti ti-eye"></i>
                                        </a>
            
                                        <a href="javascript:void(0)"
                                           class="btn btn-outline-primary btn-sm"
                                           title="تعديل"
                                           onclick="openEditModal({{ $invoice->id }}, `{{ $invoice->invoice_number }}`)">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        
                                        @if($invoice->status != 'cancelled')
                                            <button type="button" 
                                                    class="btn btn-outline-success btn-sm"
                                                    onclick="showPaymentModal({{ $invoice->id }}, {{ $invoice->remaining_amount }})"
                                                    title="إضافة دفعة">
                                                <i class="ti ti-cash"></i>
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('invoices.show', $invoice) }}?print=1" 
                                           class="btn btn-outline-secondary btn-sm" 
                                           target="_blank" 
                                           title="طباعة">
                                            <i class="ti ti-printer"></i>
                                        </a>
            
                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                title="حذف"
                                                onclick="openDeleteModal({{ $invoice->id }}, `{{ $invoice->invoice_number }}`)">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="ti ti-file-off"></i>
                                        <h5 class="fw-bold">لا توجد فواتير</h5>
                                        <p class="text-muted mb-3">لم يتم العثور على فواتير تطابق معايير البحث</p>
                                        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus"></i> إنشاء فاتورة جديدة
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Edit Confirm Modal (ينقلك لمحرر التعديل الكامل) --}}
            <div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-labelledby="editInvoiceModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header" style="background:#b48b1e;">
                    <h5 class="modal-title text-white" id="editInvoiceModalLabel">
                        <i class="ti ti-edit me-1"></i> تعديل الفاتورة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                  </div>
                  <div class="modal-body">
                    <p class="mb-1">هل تريد فتح صفحة تعديل الفاتورة التالية؟</p>
                    <div class="border rounded p-2 bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary" id="edit-invoice-number">--</span>
                            <small class="text-muted">سيتم فتح المحرر الكامل لتعديل العناصر والخصومات والمدفوعات.</small>
                        </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <a href="#" id="goToEditLink" class="btn btn-primary">
                        <i class="ti ti-external-link"></i> فتح صفحة التعديل
                    </a>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        إلغاء
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="modal fade" id="deleteInvoiceModal" tabindex="-1" aria-labelledby="deleteInvoiceModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <form method="POST" id="deleteInvoiceForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                      <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteInvoiceModalLabel">
                            <i class="ti ti-alert-triangle me-1"></i> تأكيد حذف الفاتورة
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                      </div>
                      <div class="modal-body">
                        <p class="mb-1">هل أنت متأكد من حذف الفاتورة التالية؟ لا يمكن التراجع عن هذا الإجراء.</p>
                        <div class="border rounded p-2 bg-light">
                            <span class="badge bg-secondary" id="delete-invoice-number">--</span>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">
                            <i class="ti ti-trash"></i> نعم، حذف
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            إلغاء
                        </button>
                      </div>
                    </div>
                </form>
              </div>
            </div>
            
            @push('scripts')
            <script>
            (function () {
                const baseInvoicesUrl = "{{ url('invoices') }}";
            
                window.openEditModal = function (id, number) {
                    const link = document.getElementById('goToEditLink');
                    const badge = document.getElementById('edit-invoice-number');
                    badge.textContent = number || ('#' + id);
                    link.setAttribute('href', `${baseInvoicesUrl}/${id}/edit`);
                    const modalEl = document.getElementById('editInvoiceModal');
                    const m = window.bootstrap ? new bootstrap.Modal(modalEl) : null;
                    m ? m.show() : modalEl.classList.add('show');
                };
            
                window.openDeleteModal = function (id, number) {
                    const form = document.getElementById('deleteInvoiceForm');
                    const badge = document.getElementById('delete-invoice-number');
                    badge.textContent = number || ('#' + id);
                    form.setAttribute('action', `${baseInvoicesUrl}/${id}`);
                    const modalEl = document.getElementById('deleteInvoiceModal');
                    const m = window.bootstrap ? new bootstrap.Modal(modalEl) : null;
                    m ? m.show() : modalEl.classList.add('show');
                };
            })();
            </script>
            @endpush
            

            <!-- Pagination -->
            @if($invoices->hasPages())
                <div class="p-4 border-top" style="background-color: #faf8f6;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            عرض {{ $invoices->firstItem() }} إلى {{ $invoices->lastItem() }}
                            من أصل {{ $invoices->total() }} فاتورة
                        </div>
                        <div>
                            {{ $invoices->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            @endif
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

<style>
#paymentModal .form-select, 
#paymentModal .form-control {
    border-radius: 8px;
    border: 1px solid #e8c9c0;
    transition: all 0.2s ease;
}

#paymentModal .form-select:focus, 
#paymentModal .form-control:focus {
    border-color: #b48b1e;
    box-shadow: 0 0 0 2px rgba(180, 139, 30, 0.1);
}

#paymentModal .btn-success {
    background-color: #28a745;
    border-color: #28a745;
    border-radius: 8px;
    font-weight: 600;
}

#paymentModal .btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-1px);
}

#paymentModal .btn-outline-secondary {
    border-radius: 8px;
    font-weight: 600;
}

#referenceField {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #b48b1e;
}

#referenceField.show {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
        padding: 0 15px;
    }
    to {
        opacity: 1;
        max-height: 200px;
        padding: 15px;
    }
}
</style>

<script>
function showPaymentModal(invoiceId, remainingAmount) {
    // Reset form
    document.getElementById('paymentForm').reset();
    document.getElementById('referenceField').style.display = 'none';
    
    // Set values
    document.getElementById('remainingAmount').value = remainingAmount.toFixed(3) + ' د.ل';
    document.getElementById('paymentForm').action = `/invoices/${invoiceId}/payment`;
    
    // Set today's date as default
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
        referenceField.classList.add('show');
        referenceLabel.textContent = 'رقم التحويل البنكي';
        referenceHelper.textContent = 'أدخل رقم المعاملة أو المرجع من البنك';
        referenceInput.placeholder = 'رقم التحويل البنكي';
        referenceInput.required = false;
    } else if (paymentMethod === 'check') {
        referenceField.style.display = 'block';
        referenceField.classList.add('show');
        referenceLabel.textContent = 'رقم الشيك';
        referenceHelper.textContent = 'أدخل رقم الشيك';
        referenceInput.placeholder = 'رقم الشيك';
        referenceInput.required = false;
    } else if (paymentMethod === 'credit_card') {
        referenceField.style.display = 'block';
        referenceField.classList.add('show');
        referenceLabel.textContent = 'رقم المعاملة';
        referenceHelper.textContent = 'آخر 4 أرقام من البطاقة أو رقم المعاملة';
        referenceInput.placeholder = 'رقم المعاملة';
        referenceInput.required = false;
    } else if (paymentMethod === 'other') {
        referenceField.style.display = 'block';
        referenceField.classList.add('show');
        referenceLabel.textContent = 'رقم المرجع';
        referenceHelper.textContent = 'أضف رقم مرجع أو ملاحظة تعريفية';
        referenceInput.placeholder = 'رقم المرجع أو الملاحظة';
        referenceInput.required = false;
    } else {
        referenceField.style.display = 'none';
        referenceField.classList.remove('show');
        referenceInput.required = false;
        referenceInput.value = '';
    }
}

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const amount = parseFloat(formData.get('amount'));
    const remainingAmountText = document.getElementById('remainingAmount').value;
    const remainingAmount = parseFloat(remainingAmountText.replace(/[^\d.]/g, ''));
    
    // Validation
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
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>جاري الحفظ...';
    submitBtn.disabled = true;
    
    // Submit form
    this.submit();
});

// Reset loading state when modal is hidden
document.getElementById('paymentModal').addEventListener('hidden.bs.modal', function() {
    const submitBtn = document.querySelector('#paymentForm button[type="submit"]');
    submitBtn.innerHTML = '<i class="ti ti-check"></i> حفظ الدفعة';
    submitBtn.disabled = false;
});
</script>
@endsection

@push('scripts')
<script>
function showPaymentModal(invoiceId, remainingAmount) {
    document.getElementById('remainingAmount').value = remainingAmount.toFixed(2) + ' د.ل';
    document.getElementById('paymentForm').action = `/invoices/${invoiceId}/payment`;
    
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const amount = parseFloat(formData.get('amount'));
    const remainingAmount = parseFloat(document.getElementById('remainingAmount').value);
    
    if (amount > remainingAmount) {
        if (window.toastr) {
            toastr.error('لا يمكن أن يكون مبلغ الدفعة أكبر من المبلغ المتبقي');
        } else {
            alert('لا يمكن أن يكون مبلغ الدفعة أكبر من المبلغ المتبقي');
        }
        return;
    }
    
    this.submit();
});

function removeFilter(filterName) {
    const url = new URL(window.location.href);
    if (Array.isArray(filterName)) {
        filterName.forEach(name => url.searchParams.delete(name));
    } else {
        url.searchParams.delete(filterName);
    }
    window.location.href = url.toString();
}
</script>
@endpush