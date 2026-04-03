<nav class="navbar navbar-expand-lg"> 
    <a class="navbar-brand" href="{{ route('home') }}">
        <img class="logo_light" src="/assets/images/palombini-logo.png" alt="Palombini Cafe Logo">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-expanded="false"> 
        <span class="ion-android-menu"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
        <ul class="navbar-nav">
            <li>  <a href="{{ route('home') }}" class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}"><x-bi en="Home" ar="الرئيسية" /></a> </li>
            <li>  <a href="{{ route('menu') }}" class="nav-link {{ Request::is('menu*') ? 'active' : '' }}"><x-bi en="Menu" ar="المنيو" /></a> </li>
            <li>  <a href="{{ route('about') }}" class="nav-link {{ Request::routeIs('about') ? 'active' : '' }}"><x-bi en="About" ar="عنّا" /></a> </li>
            <li> <a href="{{ route('contact') }}" class="nav-link {{ Request::routeIs('contact') ? 'active' : '' }}"><x-bi en="Contact" ar="اتصل بنا" /></a> </li>

        </ul>
        
    </div>
    <ul class="navbar-nav attr-nav align-items-center">
        <li>
            <a class="nav-link account_trigger" href="#"><i class="linearicons-user"></i></a>
        </li>

        @php
            $user = Auth::user();
            $showCart = !$user || in_array($user->role ?? null, ['customer', 'global_admin', 'admin', 'cashier', 'staff']); // show for guest and all logged-in users
        @endphp

        @if ($showCart)
            <li>
                <a class="nav-link {{ Request::routeIs('cart') ? 'active' : '' }}" href="{{ route('customer.cart') }}" style="color: black; font-size: 18px;">
                    🛒
                    <span class="cart_count" id="cart_count" style="background: red; color: white; border-radius: 50%; padding: 2px 6px;">{{ $customer_total_cart_items ?? 0 }}</span>
                </a>
            </li>
        @endif
    </ul>
    <div class="header_btn d-sm-block d-none">
        <a href="tel:{{ config('site.phone') }}" class="btn btn-default rounded-0 ml-2 btn-sm"><i class="fa fa-phone"></i> <x-bi en="CALL US" ar="اتصل بنا" /></a>
    </div>  

</nav>

