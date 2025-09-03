@extends('layouts.app')

@section('title', 'المستخدمين')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">المستخدمين</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-users me-2"></i>
                    قائمة المستخدمين
                </h5>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        إضافة مستخدم جديد
                    </a>
            </div>

            <!-- Filter Bar -->
            <div class="card-body border-bottom">
                <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">البحث</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="البحث بالاسم أو البريد الإلكتروني..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                نشط
                            </option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                غير نشط
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ترتيب حسب</label>
                        <select name="sort" class="form-select">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>
                                الأحدث
                            </option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                الأقدم
                            </option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>
                                الاسم أ-ي
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary flex-fill">
                                <i class="ti ti-filter me-1"></i>
                                تصفية
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Bulk Actions -->
            <div class="card-body border-bottom">
                <form id="bulkActionForm" method="POST" action="{{ route('users.bulk-action') }}">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    تحديد الكل
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="action" class="form-select" id="bulkAction">
                                <option value="">اختر إجراء جماعي</option>
                                <option value="activate">تفعيل المحدد</option>
                                <option value="deactivate">إلغاء تفعيل المحدد</option>
                                <option value="delete">حذف المحدد</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-primary" id="bulkSubmit" disabled>
                                <i class="ti ti-check me-1"></i>
                                تطبيق
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAllTable">
                                    </div>
                                </th>
                                <th>المستخدم</th>
                                <th>البريد الإلكتروني</th>
                                <th>الأدوار</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th width="120">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" type="checkbox" 
                                                   name="users[]" value="{{ $user->id }}" form="bulkActionForm">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=2d5a05&color=fff&size=40') }}" 
                                                     alt="{{ $user->name }}" class="rounded-circle" width="40" height="40">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">ID: {{ $user->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="ti ti-mail me-1 text-muted"></i>
                                        {{ $user->email }}
                                    </td>
                                    <td>
                                       @if($user->hasAnyPermissions())
                                            @foreach($user->permissions as $role)
                                                <span class="badge bg-light-info text-info me-1">
                                                    <i class="ti ti-shield me-1"></i>
                                                    {{ $role->name_ar ?? $role->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">
                                                <i class="ti ti-shield-off me-1"></i>
                                                لا يوجد أدوار
                                            </span>
                                        @endif
                               
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->is_active ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">
                                            <i class="ti {{ $user->is_active ? 'ti-circle-check' : 'ti-circle-x' }} me-1"></i>
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <i class="ti ti-calendar me-1 text-muted"></i>
                                        {{ $user->created_at->format('Y-m-d') }}
                                        <br>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>

                                        <a href="{{ route('users.edit', $user) }}" 
                                        class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="tooltip" title="عرض">
                                        <i class="ti ti-eye"></i>
                                     </a>

                                       <a href="{{ route('users.edit', $user) }}" 
                                          class="btn btn-sm btn-outline-primary" 
                                          data-bs-toggle="tooltip" title="تعديل">
                                          <i class="ti ti-edit"></i>
                                       </a>
                                       <button type="button" 
                                       class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#toggleStatusModal"
                                       data-user-id="{{ $user->id }}"
                                       data-user-name="{{ $user->name }}"
                                       data-current-status="{{ $user->is_active ? 'active' : 'inactive' }}"
                                       title="{{ $user->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                   <i class="ti {{ $user->is_active ? 'ti-user-x' : 'ti-user-check' }}"></i>
                               </button>
                           
                                   <button type="button" 
                                           class="btn btn-sm btn-outline-danger" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#deleteModal"
                                           data-user-id="{{ $user->id }}"
                                           data-user-name="{{ $user->name }}"
                                           title="حذف">
                                       <i class="ti ti-trash"></i>
                                   </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ti ti-users-off display-1 text-muted mb-3"></i>
                                            <h5 class="text-muted">لا توجد بيانات</h5>
                                            <p class="text-muted">لم يتم العثور على مستخدمين</p>
                                                <a href="{{ route('users.create') }}" class="btn btn-primary">
                                                    <i class="ti ti-plus me-1"></i>
                                                    إضافة أول مستخدم
                                                </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            عرض {{ $users->firstItem() }} إلى {{ $users->lastItem() }} من أصل {{ $users->total() }} نتيجة
                        </div>
                        <div>
                            {{ $users->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger" id="deleteModalLabel">
                    <i class="ti ti-alert-triangle me-2"></i>
                    تأكيد الحذف
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="ti ti-trash display-1 text-danger mb-3"></i>
                    <h6>هل أنت متأكد من حذف المستخدم؟</h6>
                    <p class="text-muted mb-0">سيتم حذف المستخدم <strong id="deleteUserName"></strong> نهائياً ولا يمكن التراجع عن هذا الإجراء.</p>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>
                    إلغاء
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash me-1"></i>
                        حذف نهائي
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Status Modal -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="toggleStatusModalLabel">
                    <i class="ti ti-user-check me-2"></i>
                    تغيير حالة المستخدم
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="ti ti-user-question display-1 text-warning mb-3"></i>
                    <h6 id="toggleStatusTitle"></h6>
                    <p class="text-muted mb-0" id="toggleStatusMessage"></p>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>
                    إلغاء
                </button>
                <form id="toggleStatusForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn" id="toggleStatusBtn">
                        <i class="ti ti-check me-1"></i>
                        تأكيد
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Bulk selection
    const selectAll = document.getElementById('selectAll');
    const selectAllTable = document.getElementById('selectAllTable');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkAction = document.getElementById('bulkAction');
    const bulkSubmit = document.getElementById('bulkSubmit');

    // Select all functionality
    [selectAll, selectAllTable].forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            userCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActions();
            // Sync both select all checkboxes
            selectAll.checked = this.checked;
            selectAllTable.checked = this.checked;
        });
    });

    // Individual checkbox change
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    // Bulk action change
    bulkAction.addEventListener('change', updateBulkActions);

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const hasSelection = checkedBoxes.length > 0;
        const hasAction = bulkAction.value != '';
        
        bulkSubmit.disabled = !(hasSelection && hasAction);
        
        // Update select all checkbox state
        const allChecked = userCheckboxes.length > 0 && checkedBoxes.length == userCheckboxes.length;
        selectAll.checked = allChecked;
        selectAllTable.checked = allChecked;
    }

    // Delete modal
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const userId = button.getAttribute('data-user-id');
        const userName = button.getAttribute('data-user-name');
        
        document.getElementById('deleteUserName').textContent = userName;
        document.getElementById('deleteForm').action = `/users/${userId}`;
    });

    // Toggle status modal
    const toggleStatusModal = document.getElementById('toggleStatusModal');
    toggleStatusModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const userId = button.getAttribute('data-user-id');
        const userName = button.getAttribute('data-user-name');
        const currentStatus = button.getAttribute('data-current-status');
        
        const isActive = currentStatus == 'active';
        const action = isActive ? 'deactivate' : 'activate';
        const actionText = isActive ? 'إلغاء تفعيل' : 'تفعيل';
        const btnClass = isActive ? 'btn-warning' : 'btn-success';
        const icon = isActive ? 'ti-user-x' : 'ti-user-check';
        
        document.getElementById('toggleStatusTitle').textContent = `${actionText} المستخدم ${userName}`;
        document.getElementById('toggleStatusMessage').textContent = 
            `هل أنت متأكد من ${actionText} المستخدم ${userName}؟`;
        
        const toggleBtn = document.getElementById('toggleStatusBtn');
        toggleBtn.className = `btn ${btnClass}`;
        toggleBtn.innerHTML = `<i class="ti ${icon} me-1"></i>${actionText}`;
        
        document.getElementById('toggleStatusForm').action = `/users/${userId}/toggle-status`;
    });

    // Bulk action form submission
    document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const action = bulkAction.value;
        
        if (checkedBoxes.length == 0) {
            e.preventDefault();
            alert('يرجى تحديد مستخدم واحد على الأقل');
            return;
        }
        
        if (!action) {
            e.preventDefault();
            alert('يرجى اختيار إجراء');
            return;
        }
        
        const actionText = action == 'delete' ? 'حذف' : 
                          action == 'activate' ? 'تفعيل' : 'إلغاء تفعيل';
        
        if (!confirm(`هل أنت متأكد من ${actionText} ${checkedBoxes.length} مستخدم؟`)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush