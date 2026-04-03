<div class="account_box">
    <div class="account_box_header">
        <h3>
            @auth
                <x-bi :en="'Hi, ' . Auth::user()->first_name" :ar="'أهلاً، ' . Auth::user()->first_name" />
            @else
                <x-bi en="Account" ar="الحساب" />
            @endauth
        </h3>
    </div>
    <hr/>
    <div class="account_box_body">
        @guest
            <ul class="cart_list">
                <li><a href="{{ route('auth.login') }}"><x-bi en="Login" ar="تسجيل الدخول" /></a></li>
                <li><a href="{{ route('home') }}"><x-bi en="Home" ar="الرئيسية" /></a></li>
            </ul>
        @else
            <ul class="cart_list">
                @if (in_array(Auth::user()->role, ['admin', 'cashier', 'global_admin']))
                    <li><a href="{{ route('admin.dashboard') }}"><x-bi en="Dashboard" ar="لوحة التحكم" /></a></li>
                @elseif (Auth::user()->role === 'customer')
                    <li><a href="{{ route('customer.account') }}"><x-bi en="My Account" ar="حسابي" /></a></li>
                    <li><a href="{{ route('customer.orders') }}"><x-bi en="My Orders" ar="طلباتي" /></a></li>
                    <li><a href="{{ route('customer.change.password') }}"><x-bi en="Change Password" ar="تغيير كلمة المرور" /></a></li>

                @endif
                <li><a href="{{ route('auth.logout') }}"><x-bi en="Logout" ar="تسجيل الخروج" /></a></li>
                <li><a href="{{ route('home') }}"><x-bi en="Home" ar="الرئيسية" /></a></li>
            </ul>
        @endauth
    </div>
</div>
