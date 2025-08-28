<li class="pc-item">
  <a href="{{ route('dashboard') }}" class="pc-link">
    <span class="pc-micon">
      <svg class="pc-icon"><use xlink:href="#custom-home"></use></svg>
    </span>
    <span class="pc-mtext">الصفحة الرئيسة</span>
  </a>
 </li>
 
 <li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ti ti-file-invoice"></i>
    </span>
    <span class="pc-mtext">المناسبات</span>
    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item">
      <a class="pc-link" href="{{ route('invoices.create') }}">إنشاء فاتورة جديدة</a>
    </li>
    <li class="pc-item">
      <a class="pc-link" href="{{ route('invoices.index') }}">قائمة المناسبات</a>
    </li>
  </ul>
</li>




 <li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ti ti-receipt"></i>
    </span>
    <span class="pc-mtext">الخدمات</span>
    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item">
      <a class="pc-link" href="{{ route('services.create') }}">إضافة خدمة جديدة</a>
    </li>
    <li class="pc-item">
      <a class="pc-link" href="{{ route('services.index') }}">قائمة الخدمات</a>
    </li>
  </ul>
 </li>
 
 <li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ti ti-users"></i>
    </span>
    <span class="pc-mtext">الزبائن</span>
    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item">
      <a class="pc-link" href="{{ route('customers.create') }}">إضافة زبون جديد</a>
    </li>
    <li class="pc-item">
      <a class="pc-link" href="{{ route('customers.index') }}">قائمة الزبائن</a>
    </li>
  </ul>
 </li>
 

 <!-- في القائمة الجانبية -->

 <li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ti ti-ticket"></i>
    </span>
    <span class="pc-mtext">الكوبونات</span>
    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item">
      <a class="pc-link" href="{{ route('coupons.create') }}">إضافة كوبون جديد</a>
    </li>
    <li class="pc-item">
      <a class="pc-link" href="{{ route('coupons.index') }}">قائمة الكوبونات</a>
    </li>
  </ul>
</li>



<li class="pc-item">
  <a href="{{ route('reports.index') }}" class="pc-link">
    <span class="pc-micon">
      <svg class="pc-icon"><use xlink:href="#custom-document"></use></svg>
    </span>
    <span class="pc-mtext"> التقارير </span>
  </a>
 </li>


 <li class="pc-item">
  <a href="{{ route('logout') }}" class="pc-link">
    <span class="pc-micon"><i class="ti ti-power"></i></span>
    <span class="pc-mtext">تسجيل خروج</span>
  </a>
 </li>