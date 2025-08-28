@extends('layouts.app')

@section('title', 'إدارة الخدمات')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active" aria-current="page">الخدمات</li>
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
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .badge-success {
        background-color: #d4edda;
        color: #155724;
    }
    .badge-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    .price-cell {
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-receipt me-2"></i>
                    قائمة الخدمات
                </h5>
                <div class="card-header-right">
                    <a href="{{ route('services.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus"></i> إضافة خدمة جديدة
                    </a>
                </div>
            </div>

            <div class="card-body">

                <!-- Search & Filters -->
                <form id="searchForm" method="GET" action="{{ route('services.index') }}" class="search-filters">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">بحث</label>
                            <input type="text" name="q" class="form-control" placeholder="ابحث باسم الخدمة..."
                                   value="{{ request('q') }}">
                        </div>

                    

                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ti ti-search"></i> بحث
                            </button>
                            <a href="{{ route('services.index') }}" class="btn btn-light">
                                <i class="ti ti-refresh"></i> إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Bulk Actions -->
                <div class="bulk-actions" id="bulkActions">
                    <form method="POST" action="{{ route('services.bulk-action') }}" id="bulkForm">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <span class="me-3">العمليات الجماعية:</span>
                                <select class="form-select d-inline-block w-auto me-2" name="action" required>
                                    <option value="">اختر عملية</option>
                                    <option value="activate">تفعيل</option>
                                    <option value="deactivate">إلغاء تفعيل</option>
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
                                <th>اسم الخدمة</th>
                                <th>السعر</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th width="200" class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input service-checkbox"
                                                   type="checkbox"
                                                   value="{{ $service->id }}">
                                        </div>
                                    </td>
                                    <td>{{ $service->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-list-details text-primary me-2 fs-5"></i>
                                            <div>
                                                <h6 class="mb-0">{{ $service->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="price-cell">
                                        {{ number_format($service->price, 2) }}
                                    </td>
                                    <td>
                                        @if($service->is_active)
                                            <span class="status-badge badge-success">مفعل</span>
                                        @else
                                            <span class="status-badge badge-danger">غير مفعل</span>
                                        @endif
                                    </td>
                                    <td>{{ $service->created_at?->format('Y/m/d H:i') }}</td>
                                    <td class="table-actions text-center">
                                        <a href="{{ route('services.edit', $service) }}"
                                           class="btn btn-sm btn-outline-primary" title="تعديل">
                                            <i class="ti ti-edit"></i>
                                        </a>

                                        <form method="POST"
                                              action="{{ route('services.toggle', $service) }}"
                                              class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm {{ $service->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                    title="{{ $service->is_active ? 'إلغاء تفعيل' : 'تفعيل' }}"
                                                    onclick="return confirm('تأكيد تغيير الحالة؟')">
                                                <i class="ti {{ $service->is_active ? 'ti-eye-off' : 'ti-eye' }}"></i>
                                            </button>
                                        </form>

                                        <form method="POST"
                                              action="{{ route('services.destroy', $service) }}"
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
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-file-off fs-1 d-block mb-2"></i>
                                            <p class="mb-0">لا توجد خدمات مطابقة للبحث</p>
                                            <a href="{{ route('services.create') }}" class="btn btn-primary btn-sm mt-2">
                                                إضافة خدمة جديدة
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($services->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            عرض {{ $services->firstItem() }} إلى {{ $services->lastItem() }}
                            من أصل {{ $services->total() }} نتيجة
                        </div>
                        <div>
                            {{ $services->withQueryString()->links() }}
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
    const itemCheckboxSelector = '.service-checkbox';
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
    ['status','sort_by','sort_dir','per_page'].forEach(name => {
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
