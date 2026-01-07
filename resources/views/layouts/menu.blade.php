<li class="pc-item">
  <a href="{{ route('dashboard') }}" class="pc-link">
    <span class="pc-micon">
      <svg class="pc-icon"><use xlink:href="#custom-home"></use></svg>
    </span>
    <span class="pc-mtext">الصفحة الرئيسة</span>
  </a>
</li>

@can('invoices.view')
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ti ti-file-invoice"></i>
    </span>
    <span class="pc-mtext">المناسبات</span>
    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    @can('invoices.create')
    <li class="pc-item">
      <a class="pc-link" href="{{ route('invoices.create') }}">إنشاء فاتورة جديدة</a>
    </li>
    @endcan
    <li class="pc-item">
      <a class="pc-link" href="{{ route('invoices.index') }}">قائمة المناسبات</a>
    </li>
  </ul>
</li>
@endcan

@can('treasury.view')
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="fa fa-cash-register"></i>
    </span>
    <span class="pc-mtext">الخزينة المالية</span>
    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    @can('treasury.create')
    <li class="pc-item">
      <a class="pc-link" href="{{ route('transactions.create') }}">إضافة معاملة جديدة</a>
    </li>
    @endcan
    <li class="pc-item">
      <a class="pc-link" href="{{ route('transactions.index') }}">قائمة المعاملات</a>
    </li>
  </ul>
</li>
@endcan

@can('services.view')
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ti ti-receipt"></i>
    </span>
    <span class="pc-mtext">الخدمات</span>
    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    @can('services.create')
    <li class="pc-item">
      <a class="pc-link" href="{{ route('services.create') }}">إضافة خدمة جديدة</a>
    </li>
    @endcan
    <li class="pc-item">
      <a class="pc-link" href="{{ route('services.index') }}">قائمة الخدمات</a>
    </li>
  </ul>
</li>
@endcan

@can('customers.view')
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ti ti-users"></i>
    </span>
    <span class="pc-mtext">الزبائن</span>
    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    @can('customers.create')
    <li class="pc-item">
      <a class="pc-link" href="{{ route('customers.create') }}">إضافة زبون جديد</a>
    </li>
    @endcan
    <li class="pc-item">
      <a class="pc-link" href="{{ route('customers.index') }}">قائمة الزبائن</a>
    </li>
  </ul>
</li>
@endcan

@can('coupons.view')
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ti ti-ticket"></i>
    </span>
    <span class="pc-mtext">الكوبونات</span>
    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    @can('coupons.create')
    <li class="pc-item">
      <a class="pc-link" href="{{ route('coupons.create') }}">إضافة كوبون جديد</a>
    </li>
    @endcan
    <li class="pc-item">
      <a class="pc-link" href="{{ route('coupons.index') }}">قائمة الكوبونات</a>
    </li>
  </ul>
</li>
@endcan

@can('users.view')
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ti ti-user-cog"></i>
    </span>
    <span class="pc-mtext">المستخدمين</span>
    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    @can('users.create')
    <li class="pc-item">
      <a class="pc-link" href="{{ route('users.create') }}">إضافة مستخدم جديد</a>
    </li>
    @endcan
    <li class="pc-item">
      <a class="pc-link" href="{{ route('users.index') }}">قائمة المستخدمين</a>
    </li>
  </ul>
</li>
@endcan

@can('reports.view')
<li class="pc-item">
  <a href="{{ route('reports.index') }}" class="pc-link">
    <span class="pc-micon">
      <svg class="pc-icon"><use xlink:href="#custom-document"></use></svg>
    </span>
    <span class="pc-mtext">التقارير</span>
  </a>
</li>
@endcan

<li class="pc-item">
  <a href="{{ route('logout') }}" class="pc-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    <span class="pc-micon"><i class="ti ti-power"></i></span>
    <span class="pc-mtext">تسجيل خروج</span>
  </a>
  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
  </form>
</li>
