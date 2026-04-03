        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">
              <li class="nav-item">
                <div class="d-flex sidebar-profile">
                  <div class="sidebar-profile-image">
                    <img src=" {{ $loggedInUser && $loggedInUser->profile_picture ? asset('storage/profile-picture/' . $loggedInUser->profile_picture) : asset('assets/images/user-icon.png') }}" alt="image">
                    <span class="sidebar-status-indicator"></span>
                  </div>
                  <div class="sidebar-profile-name">
                    <p class="sidebar-name">
                      {{ $loggedInUser->first_name }}
                    </p>
                    <p class="sidebar-designation">
                      Admin
                    </p>
                  </div>
                </div>
              </li>


              <li class="nav-item {{ request()->route()->named('admin.dashboard') ? 'active-nav' : '' }} ">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fa fa-desktop menu-icon"></i>
                  <span class="menu-title"><x-bi en="Dashboard" ar="لوحة التحكم" /></span>
                </a>
            </li>
            
 
 
      
          <li class="nav-item {{ Request::is('admin/order*') ? 'active-nav' : '' }}">
            <a class="nav-link" href="{{ route('admin.orders.index') }}">
                <i class="fa fa-file menu-icon"></i>
                <span class="menu-title"><x-bi en="Manage Orders" ar="إدارة الطلبات" /></span>
                <span class="badge badge-danger ml-2" data-realtime-field="pending_orders_count">{{ $pending_orders_count ?? 0 }}</span>
            </a>
        </li>

          <li class="nav-item {{ request()->route()->named('admin.loyalty.index') ? 'active-nav' : '' }}">
            <a class="nav-link" href="{{ route('admin.loyalty.index') }}">
              <i class="fa fa-gift menu-icon"></i>
              <span class="menu-title"><x-bi en="Customer Loyalty" ar="ولاء العملاء" /></span>
              <span class="badge badge-danger ml-2" data-realtime-field="loyalty_eligible_orders_count">{{ $loyalty_eligible_orders_count ?? 0 }}</span>
            </a>
          </li>

          <li class="nav-item {{ request()->route()->named('admin.stocks.*') ? 'active-nav' : '' }}">
            <a class="nav-link" href="{{ route('admin.stocks.index') }}">
              <i class="fa fa-cubes menu-icon"></i>
              <span class="menu-title"><x-bi en="Manage Stock" ar="إدارة المخزون" /></span>
            </a>
          </li>
        


        @if ($loggedInUser->role == "global_admin")

        <li class="nav-item {{ request()->route()->named('admin.users.index') ? 'active-nav' : '' }}">
          <a class="nav-link" href="{{ route('admin.users.index') }}">
              <i class="fa fa-users menu-icon"></i>
              <span class="menu-title"><x-bi en="Manage Admins" ar="إدارة المسؤولين" /></span>
          </a>
        </li>
              
        <li class="nav-item">
            <a class="nav-link collapsed" data-toggle="collapse" href="#site-settings" aria-expanded="false" aria-controls="site-settings">
                <i class="fa fa-cog menu-icon"></i>
                <span class="menu-title"><x-bi en="Site Settings" ar="إعدادات الموقع" /></span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="site-settings">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.menus.index') }}"><x-bi en="Menu" ar="المنيو" /></a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.categories.index') }}"><x-bi en="Category" ar="الفئات" /></a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.sauces.index') }}"><x-bi en="Sauces" ar="الصوصات" /></a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.sides.index') }}"><x-bi en="Sides" ar="الأصناف الجانبية" /></a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.general-settings') }}"><x-bi en="General Settings" ar="الإعدادات العامة" /></a>
                    </li>
                </ul>
            </div>
        </li>
        @elseif ($loggedInUser->role == "cashier")
        <li class="nav-item {{ request()->route()->named('admin.menus.index') ? 'active-nav' : '' }}">
          <a class="nav-link" href="{{ route('admin.menus.index') }}">
              <i class="fa fa-eye menu-icon"></i>
              <span class="menu-title"><x-bi en="Menu Visibility" ar="إظهار/إخفاء المنيو" /></span>
          </a>
        </li>
        @endif
    


              <li class="nav-item {{ request()->route()->named('admin.view.myprofile') ? 'active-nav' : '' }}">
                <a class="nav-link" href="{{ route('admin.view.myprofile') }}">
                  <i class="fa fa-user menu-icon"></i>
                  <span class="menu-title"><x-bi en="My Profile" ar="ملفي الشخصي" /></span>
                </a>
              </li>

              <li class="nav-item {{ request()->route()->named('change.password.form') ? 'active-nav' : '' }}">
                <a class="nav-link" href="{{ route('change.password.form') }}">
                  <i class="fa fa-lock menu-icon"></i>
                  <span class="menu-title"><x-bi en="Change Password" ar="تغيير كلمة المرور" /></span>
                </a>
              </li>     


              <li class="nav-item">
                <a target="_blank" class="nav-link" href="{{ route('home') }}">
                  <i class="fa fa-globe menu-icon"></i>
                  <span class="menu-title"><x-bi en="Main Website" ar="الموقع الرئيسي" /></span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fa fa-power-off menu-icon"></i>
                  <span class="menu-title"><x-bi en="Logout" ar="تسجيل الخروج" /></span>
                </a>
            </li>
              
            </ul>
  
          </nav>
