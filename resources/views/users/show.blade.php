@extends('layouts.app')
@section('title', 'تفاصيل المستخدم')
@section('content')

<div class="row">
    <!-- معلومات المستخدم الأساسية -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="avatar-lg mx-auto">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120&background=2d5a05&color=fff" 
                             alt="{{ $user->name }}" 
                             class="rounded-circle"
                             style="width: 120px; height: 120px;">
                    </div>
                </div>
                <h5 class="mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                <span class="badge {{ $user->status_class }}">{{ $user->status }}</span>
                
                <div class="row mt-4">
                    <div class="col-4">
                        <div class="d-flex flex-column">
                            <span class="text-muted small">المؤسسات</span>
                            <strong>{{ $user->institutions_count }}</strong>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex flex-column">
                            <span class="text-muted small">الصلاحيات</span>
                            <strong>{{ $user->permissions_count }}</strong>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex flex-column">
                            <span class="text-muted small">الأدوار</span>
                            <strong>{{ $user->roles_count }}</strong>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>تعديل
                    </a>
                    @if($user->id !== auth()->id())
                        <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }} btn-sm">
                                <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }} me-1"></i>
                                {{ $user->is_active ? 'إلغاء تفعيل' : 'تفعيل' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- معلومات النظام -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">معلومات النظام</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-2">
                        <small class="text-muted">تاريخ الإنشاء</small>
                        <div>{{ $user->created_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                    <div class="col-12 mb-2">
                        <small class="text-muted">آخر تحديث</small>
                        <div>{{ $user->updated_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                    @if($user->email_verified_at)
                        <div class="col-12 mb-2">
                            <small class="text-muted">تم التحقق من البريد</small>
                            <div>{{ $user->email_verified_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- تفاصيل المؤسسات والصلاحيات -->
    <div class="col-md-8">
        <!-- المؤسسات -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">المؤسسات المرتبطة ({{ $user->institutions->count() }})</h6>
            </div>
            <div class="card-body">
                @if($user->institutions->count() > 0)
                    <div class="row">
                        @foreach($user->institutions as $institution)
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-3 border rounded">
                                    <img src="{{ Storage::url($institution->logo) }}" 
                                         alt="{{ $institution->name }}" 
                                         class="rounded-circle me-3"
                                         style="width: 50px; height: 50px;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $institution->name }}</h6>
                                        <small class="text-muted">
                                            <span class="badge badge-success">{{ $institution->status }}</span>
                                            @if($institution->departments_count > 0)
                                                • {{ $institution->departments_count }} قسم
                                            @endif
                                            @if($institution->users_count > 0)
                                                • {{ $institution->users_count }} مستخدم
                                            @endif
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            {{ $institution->pivot->created_at ? $institution->pivot->created_at->format('Y-m-d') : '' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا يوجد مؤسسات مرتبطة بهذا المستخدم</p>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>إضافة مؤسسات
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- الصلاحيات -->
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">الصلاحيات ({{ $user->permissions->count() }})</h6>
                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit me-1"></i>تعديل
                </a>
            </div>
            <div class="card-body">
                @if($user->permissions->count() > 0)
                    <div class="row">
                        @foreach($user->permissions as $permission)
                            <div class="col-md-4 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-shield-alt text-success me-2"></i>
                                    <span>{{ $permission->name_ar ?? $permission->name }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا يوجد صلاحيات مخصصة لهذا المستخدم</p>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>إضافة صلاحيات
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- الأدوار (إذا كانت موجودة) -->
        @if($user->roles->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">الأدوار ({{ $user->roles->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($user->roles as $role)
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-tag text-info me-2"></i>
                                    <span>{{ $role->name }}</span>
                                    @if($role->permissions->count() > 0)
                                        <small class="text-muted ms-2">({{ $role->permissions->count() }} صلاحية)</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- أزرار العمليات -->
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <div>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>العودة للقائمة
                </a>
            </div>
            <div>
                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>تعديل المستخدم
                </a>
                @if($user->id !== auth()->id())
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline ms-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟ هذا الإجراء لا يمكن التراجع عنه.')">
                            <i class="fas fa-trash me-1"></i>حذف المستخدم
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection