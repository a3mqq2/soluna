@extends('layouts.app')
@section('title', 'تعديل المستخدم')
@section('content')
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header">
            <h5>تعديل المستخدم: {{ $user->name }}</h5>
         </div>
         <div class="card-body">
               <form action="{{ route('users.update', $user) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="row">
                     <div class="col-md-6 mt-2">
                        <label for="">اسم المستخدم</label>
                        <input type="text" name="name" required class="form-control" value="{{ old('name', $user->name) }}">
                        @error('name')
                           <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                     
                     <div class="col-md-6 mt-2">
                        <label for=""> البريد الالكتروني </label>
                        <input type="email" name="email" required class="form-control" value="{{ old('email', $user->email) }}">
                        @error('email')
                           <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                     
                     <div class="col-md-6 mt-2">
                        <label for="">كلمة المرور الجديدة <small class="text-muted">(اتركها فارغة إذا كنت لا تريد تغييرها)</small></label>
                        <input type="password" name="password" class="form-control">
                        @error('password')
                           <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                     
                     <div class="col-md-6 mt-2">
                        <label for="">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" name="password_confirmation" class="form-control">
                     </div>

                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="">حدد مؤسسة</label>
                           <select name="institution_id" id="" class="form-control">
                              <option value="">حدد مؤسسة</option>
                              @foreach ($institutions as $institution)
                                  <option value="{{$institution->id}}" {{$user->institution_id == $institution->id ? "selected" : ""}} >{{$institution->name}}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>


                     <!-- قسم الصلاحيات -->
                     <div class="col-md-12 mt-4">
                        <label for="">صلاحيات الوصول</label>
                        <div class="card">
                           <div class="card-header">
                              <div class="form-check">
                                 <input class="form-check-input" type="checkbox" id="select_all_permissions">
                                 <label class="form-check-label" for="select_all_permissions">
                                    تحديد الكل / إلغاء تحديد الكل
                                 </label>
                              </div>
                           </div>
                           <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                              @if($permissions->count() > 0)
                                 <div class="row">
                                    @php
                                       $userPermissions = old('permissions', $user->permissions->pluck('name')->toArray());
                                    @endphp
                                    @foreach ($permissions as $permission)
                                       <div class="col-md-3 mt-2">
                                          <div class="form-check form-switch">
                                             <input class="form-check-input permission-checkbox" 
                                                    type="checkbox" 
                                                    name="permissions[]" 
                                                    value="{{ $permission->name }}" 
                                                    id="perm_{{ $permission->id }}"
                                                    {{ in_array($permission->name, $userPermissions) ? 'checked' : '' }}>
                                             <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ $permission->name_ar ?? $permission->name }}
                                             </label>
                                          </div>
                                       </div>
                                    @endforeach
                                 </div>
                              @else
                                 <p class="text-muted text-center">لا توجد صلاحيات متاحة</p>
                              @endif
                           </div>
                        </div>
                        @error('permissions')
                           <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>

                     <!-- معلومات إضافية -->
                     <div class="col-md-12 mt-4">
                        <div class="alert alert-info">
                           <strong>معلومات إضافية:</strong>
                           <ul class="mb-0 mt-2">
                              <li>تاريخ الإنشاء: {{ $user->created_at->format('Y-m-d H:i:s') }}</li>
                              <li>آخر تحديث: {{ $user->updated_at->format('Y-m-d H:i:s') }}</li>
                              <li>عدد المؤسسات الحالية: {{ $user->institutions_count }}</li>
                              <li>عدد الصلاحيات الحالية: {{ $user->permissions_count }}</li>
                           </ul>
                        </div>
                     </div>

                     <div class="col-md-12 mt-4">
                        <button type="submit" class="btn btn-primary text-light">
                           <i class="fas fa-save me-2"></i>حفظ التعديلات
                        </button>
                        <a href="{{ route('users.show', $user) }}" class="btn btn-info text-light">
                           <i class="fas fa-eye me-2"></i>عرض المستخدم
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                           <i class="fas fa-arrow-left me-2"></i>رجوع
                        </a>
                     </div>
                  </div>
               </form>
         </div>
      </div>
   </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديد/إلغاء تحديد جميع المؤسسات
    const selectAllInstitutions = document.getElementById('select_all_institutions');
    const institutionCheckboxes = document.querySelectorAll('.institution-checkbox');
    
    // تحديث حالة "تحديد الكل" عند تحميل الصفحة
    updateSelectAllStatus(institutionCheckboxes, selectAllInstitutions);
    
    selectAllInstitutions.addEventListener('change', function() {
        institutionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // تحديث حالة "تحديد الكل" عند تغيير المؤسسات
    institutionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllStatus(institutionCheckboxes, selectAllInstitutions);
        });
    });

    // تحديد/إلغاء تحديد جميع الصلاحيات
    const selectAllPermissions = document.getElementById('select_all_permissions');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    
    // تحديث حالة "تحديد الكل" عند تحميل الصفحة
    updateSelectAllStatus(permissionCheckboxes, selectAllPermissions);
    
    selectAllPermissions.addEventListener('change', function() {
        permissionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // تحديث حالة "تحديد الكل" عند تغيير الصلاحيات
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllStatus(permissionCheckboxes, selectAllPermissions);
        });
    });

    function updateSelectAllStatus(checkboxes, selectAllCheckbox) {
        const checkedCount = document.querySelectorAll(checkboxes[0].className.split(' ')[0] + ':checked').length;
        const totalCount = checkboxes.length;
        
        selectAllCheckbox.checked = checkedCount === totalCount;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
    }
});
</script>

@endsection