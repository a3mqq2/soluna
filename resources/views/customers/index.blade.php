@extends('layouts.app')

@section('title', 'إدارة الزبائن')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active" aria-current="page">الزبائن</li>
@endsection

@push('styles')
<style>
    .search-filters {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .bulk-actions {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        display: none;
    }
    .table-actions {
        white-space: nowrap;
    }
    .phone-cell {
        font-family: monospace;
        font-weight: 600;
    }
    .notes-preview {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #666;
        font-style: italic;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-users me-2"></i>
                    قائمة الزبائن
                </h5>
                <div class="card-header-right">
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus"></i> إضافة زبون جديد
                    </a>
                </div>
            </div>

            <div class="card-body">

                <!-- Search & Filters -->
                <form id="searchForm" method="GET" action="{{ route('customers.index') }}" class="search-filters">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">بحث</label>
                            <input type="text" name="q" class="form-control" placeholder="ابحث بالاسم أو رقم الهاتف..."
                                   value="{{ request('q') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">ترتيب حسب</label>
                            <select name="sort_by" class="form-select">
                                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>تاريخ الإنشاء</option>
                                <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>الاسم</option>
                                <option value="phone" {{ request('sort_by') === 'phone' ? 'selected' : '' }}>رقم الهاتف</option>
                                <option value="receivable" {{ request('sort_by') === 'receivable' ? 'selected' : '' }}>القيمة المستحقة</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">الترتيب</label>
                            <select name="sort_dir" class="form-select">
                                <option value="desc" {{ request('sort_dir') === 'desc' ? 'selected' : '' }}>تنازلي</option>
                                <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>تصاعدي</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">عدد النتائج</label>
                            <select name="per_page" class="form-select">
                                <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ti ti-search"></i> بحث
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-light">
                                <i class="ti ti-refresh"></i> إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Bulk Actions -->
                <div class="bulk-actions" id="bulkActions">
                    <form method="POST" action="{{ route('customers.bulk-action') }}" id="bulkForm">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <span class="me-3">العمليات الجماعية:</span>
                                <select class="form-select d-inline-block w-auto me-2" name="action" required>
                                    <option value="">اختر عملية</option>
                                    <option value="delete">حذف</option>
                                </select>
                                <button type="submit" class="btn btn-warning btn-sm"
                                    onclick="return confirm('هل أنت متأكد من تنفيذ هذه العملية؟')">
                                    تنفيذ
                                </button>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="text-muted">تم اختيار <span id="selectedCount">0</span> عنصر</span>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th>الرقم</th>
                                <th>اسم الزبون</th>
                                <th>رقم الهاتف</th>
                                <th>الملاحظات</th>
                                <th>المستحق</th>
                                <th>تاريخ الإنشاء</th>
                                <th width="200" class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input customer-checkbox"
                                                   type="checkbox"
                                                   value="{{ $customer->id }}">
                                        </div>
                                    </td>
                                    <td>{{ $customer->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-user text-primary me-2 fs-5"></i>
                                            <div>
                                                <h6 class="mb-0">{{ $customer->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="phone-cell">
                                        {{ $customer->phone }}
                                    </td>
                                    <td>
                                        @if($customer->notes)
                                            <div class="notes-preview" title="{{ $customer->notes }}">
                                                {{ $customer->notes }}
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ number_format($customer->receivable ?? 0, 2) }}</strong>
                                    </td>
                                    <td>{{ $customer->created_at?->format('Y/m/d H:i') }}</td>
                                    <td class="table-actions text-center">
                                        <a href="{{ route('customers.show', $customer) }}"
                                           class="btn btn-sm btn-outline-info" title="عرض">
                                            <i class="ti ti-eye"></i>
                                        </a>

                                        <a href="{{ route('customers.edit', $customer) }}"
                                           class="btn btn-sm btn-outline-primary" title="تعديل">
                                            <i class="ti ti-edit"></i>
                                        </a>

                                        <form method="POST"
                                              action="{{ route('customers.destroy', $customer) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="حذف"
                                                    onclick="return confirm('هل أنت متأكد من الحذف؟ لا يمكن التراجع!')">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-user-off fs-1 d-block mb-2"></i>
                                            <p class="mb-0">لا توجد زبائن مطابقة للبحث</p>
                                            <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm mt-2">
                                                إضافة زبون جديد
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($customers->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            عرض {{ $customers->firstItem() }} إلى {{ $customers->lastItem() }}
                            من أصل {{ $customers->total() }} نتيجة
                        </div>
                        <div>
                            {{ $customers->withQueryString()->links() }}
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxSelector = '.customer-checkbox';
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    const bulkForm = document.getElementById('bulkForm');

    function updateSelectAllState() {
        const checkboxes = document.querySelectorAll(itemCheckboxSelector);
        const checked = document.querySelectorAll(itemCheckboxSelector + ':checked');
        selectAllCheckbox.checked = checkboxes.length && checked.length === checkboxes.length;
        selectAllCheckbox.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
    }

    function updateBulkActions() {
        const checkedCheckboxes = document.querySelectorAll(itemCheckboxSelector + ':checked');
        const count = checkedCheckboxes.length;

        selectedCount.textContent = count;

        if (count > 0) {
            bulkActions.style.display = 'block';

            // remove old hidden inputs
            bulkForm.querySelectorAll('input[name="ids[]"]').forEach(n => n.remove());
            // add selected ids
            checkedCheckboxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                bulkForm.appendChild(input);
            });
        } else {
            bulkActions.style.display = 'none';
        }
    }

    // bind events
    selectAllCheckbox.addEventListener('change', function () {
        document.querySelectorAll(itemCheckboxSelector).forEach(cb => cb.checked = selectAllCheckbox.checked);
        updateSelectAllState();
        updateBulkActions();
    });

    document.querySelectorAll(itemCheckboxSelector).forEach(cb => {
        cb.addEventListener('change', function () {
            updateSelectAllState();
            updateBulkActions();
        });
    });

    // auto-submit on filters change
    ['sort_by','sort_dir','per_page'].forEach(name => {
        const el = document.querySelector(`[name="${name}"]`);
        if (el) {
            el.addEventListener('change', () => document.getElementById('searchForm').submit());
        }
    });

    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        let t;
        searchInput.addEventListener('input', function () {
            clearTimeout(t);
            t = setTimeout(() => document.getElementById('searchForm').submit(), 500);
        });
    }
});
</script>
@endpush
