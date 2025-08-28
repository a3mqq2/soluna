@extends('layouts.app')
@section('title', 'اضافة مستخدم جديد')
@section('content')
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-body">
               <form action="{{ route('users.store') }}" method="POST">
                  @csrf
                  @method('POST')
                  <div class="row">
                     <div class="col-md-6 mt-2">
                        <label for="">اسم المستخدم</label>
                        <input type="text" name="name" required class="form-control" value="{{ old('name') }}">
                        @error('name')
                           <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                     
                     <div class="col-md-6 mt-2">
                        <label for=""> البريد الالكتروني </label>
                        <input type="email" name="email" required class="form-control" value="{{ old('email') }}">
                        @error('email')
                           <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                     
                     <div class="col-md-6 mt-2">
                        <label for="">كلمة المرور</label>
                        <input type="password" name="password" required class="form-control">
                        @error('password')
                           <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                     
                     <div class="col-md-6 mt-2">
                        <label for="">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" required class="form-control">
                     </div>

                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="">حدد مؤسسة</label>
                           <select name="institution_id" id="" class="form-control">
                              <option value="">حدد مؤسسة</option>
                              @foreach ($institutions as $institution)
                                  <option value="{{$institution->id}}">{{$institution->name}}</option>
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
                                    @foreach ($permissions as $permission)
                                       <div class="col-md-3 mt-2">
                                          <div class="form-check form-switch">
                                             <input class="form-check-input permission-checkbox" 
                                                    type="checkbox" 
                                                    name="permissions[]" 
                                                    value="{{ $permission->name }}" 
                                                    id="perm_{{ $permission->id }}"
                                                    {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
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

                     <div class="col-md-12 mt-4">
                        <button type="submit" class="btn btn-primary text-light">
                           <i class="fas fa-save me-2"></i>حفظ
                        </button>
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
@endsection
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديد/إلغاء تحديد جميع المؤسسات
    const selectAllInstitutions = document.getElementById('select_all_institutions');
    const institutionCheckboxes = document.querySelectorAll('.institution-checkbox');
    
    selectAllInstitutions.addEventListener('change', function() {
        institutionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // تحديث حالة "تحديد الكل" عند تغيير المؤسسات
    institutionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.institution-checkbox:checked').length;
            selectAllInstitutions.checked = checkedCount === institutionCheckboxes.length;
            selectAllInstitutions.indeterminate = checkedCount > 0 && checkedCount < institutionCheckboxes.length;
        });
    });

    // تحديد/إلغاء تحديد جميع الصلاحيات
    const selectAllPermissions = document.getElementById('select_all_permissions');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    
    selectAllPermissions.addEventListener('change', function() {
        permissionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // تحديث حالة "تحديد الكل" عند تغيير الصلاحيات
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.permission-checkbox:checked').length;
            selectAllPermissions.checked = checkedCount === permissionCheckboxes.length;
            selectAllPermissions.indeterminate = checkedCount > 0 && checkedCount < permissionCheckboxes.length;
        });
    });
});
</script>
