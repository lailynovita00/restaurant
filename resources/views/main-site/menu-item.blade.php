
@extends('layouts.main-site')

@push('styles')
    
    
    <!-- Animation CSS -->
    <link rel="stylesheet" href="/assets/css/animate.css">	
    <!-- Latest Bootstrap min CSS -->
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script&amp;display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:100,100i,300,300i,400,400i,600,600i,700,700i&amp;display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&amp;display=swap" rel="stylesheet"> 
    <!-- Icon Font CSS -->
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/ionicons.min.css">
    <link rel="stylesheet" href="/assets/css/themify-icons.css">
    <link rel="stylesheet" href="/assets/css/linearicons.css">
    <link rel="stylesheet" href="/assets/css/flaticon.css">
    <!--- owl carousel CSS-->
    <link rel="stylesheet" href="/assets/owlcarousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="/assets/owlcarousel/css/owl.theme.css">
    <link rel="stylesheet" href="/assets/owlcarousel/css/owl.theme.default.min.css">
    <!-- Slick CSS -->
    <link rel="stylesheet" href="/assets/css/slick.css">
    <link rel="stylesheet" href="/assets/css/slick-theme.css">
    <!-- Magnific Popup CSS -->
    <link rel="stylesheet" href="/assets/css/magnific-popup.css">
    <!-- DatePicker CSS -->
    <link href="/assets/css/datepicker.min.css" rel="stylesheet">
    <!-- TimePicker CSS -->
    <link href="/assets/css/mdtimepicker.min.css" rel="stylesheet">
    <!-- Style CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
    <link id="layoutstyle" rel="stylesheet" href="/assets/color/theme-brown.css">
@endpush

@push('scripts')
    <!-- Latest jQuery --> 
    <script src="/assets/js/jquery-1.12.4.min.js"></script> 
    <!-- Latest compiled and minified Bootstrap --> 
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script> 
    <!-- owl-carousel min js  --> 
    <script src="/assets/owlcarousel/js/owl.carousel.min.js"></script> 
    <!-- magnific-popup min js  --> 
    <script src="/assets/js/magnific-popup.min.js"></script> 
    <!-- waypoints min js  --> 
    <script src="/assets/js/waypoints.min.js"></script> 
    <!-- parallax js  --> 
    <script src="/assets/js/parallax.js"></script> 
    <!-- countdown js  --> 
    <script src="/assets/js/jquery.countdown.min.js"></script> 
    <!-- jquery.countTo js  -->
    <script src="/assets/js/jquery.countTo.js"></script>
    <!-- imagesloaded js --> 
    <script src="/assets/js/imagesloaded.pkgd.min.js"></script>
    <!-- isotope min js --> 
    <script src="/assets/js/isotope.min.js"></script>
    <!-- jquery.appear js  -->
    <script src="/assets/js/jquery.appear.js"></script>
    <!-- jquery.dd.min js -->
    <script src="/assets/js/jquery.dd.min.js"></script>
    <!-- slick js -->
    <script src="/assets/js/slick.min.js"></script>
    <!-- DatePicker js -->
    <script src="/assets/js/datepicker.min.js"></script>
    <!-- TimePicker js -->
    <script src="/assets/js/mdtimepicker.min.js"></script>
    <!-- scripts js --> 
    <script src="/assets/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        const csrfToken = "{{ csrf_token() }}";
        const addToCartUrl = "{{ route('customer.cart.add') }}";
        const removeFromCartUrl = "{{ route('customer.cart.remove') }}";
        const updateCartUrl = "{{ route('customer.cart.update') }}"; 
    </script>
    <script src="{{ asset('/assets/js/customer-cart-menu-route.js') }}"></script>

 
@endpush


@section('title', 'Menu Details')


@section('header')
    <!-- START HEADER -->
        <header class="header_wrap fixed-top header_with_topbar light_skin main_menu_uppercase">
        <div class="container">
            @include('partials.nav')
        </div>
    </header>
    <!-- END HEADER -->
@endsection


@section('content')

 <!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section background_bg overlay_bg_50 page_title_light" data-img-src="/assets/images/product_bg.jpg">
    <div class="container"><!-- STRART CONTAINER -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title">
					<h1><x-bi en="Product Detail" ar="تفاصيل المنتج" /></h1>
                </div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><x-bi en="Home" ar="الرئيسية" /></a></li>
                    <li class="breadcrumb-item active"><x-bi en="Product Detail" ar="تفاصيل المنتج" /></li>
                </ol>
            </div>
        </div>
    </div><!-- END CONTAINER-->
</div>
<!-- END SECTION BREADCRUMB -->

<!-- START SECTION SHOP -->
<div class="section">
	<div class="container">
		<div class="row">
            <div class="col-lg-6 col-md-6 mb-4 mb-md-0">
              <div class="product-image">
                    <img src='{{ $menu->image_url }}' alt="product_img1" />
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="pr_detail">
                    <div class="product_description">
                        <h4 class="product_title"><a href="#">{{ $menu->name }} @if($menu->name_ar) / {{ $menu->name_ar }} @endif</a></h4>
                        <div class="product_price"> 
                            <span class="price">{{ number_format($menu->price, 2) }} {!! $site_settings->currency_symbol !!}</span> 
                        </div>
                        <div class="rating_wrap">
                                <div class="rating">
                                    <div class="product_rate" style="width:100%"></div>
                                </div>
                                
                            </div>
                            <br/>
                            <hr/>
                        <div class="pr_desc">
                            <p>{{ $menu->description }} @if($menu->description_ar)<br>{{ $menu->description_ar }}@endif</p>
                        </div>
                        <ul class="product-meta">
                            <li>
                                Category: {{ $menu->category->name }}
                                @if($menu->category && $menu->category->name_ar)
                                    / <span dir="rtl" lang="ar">{{ $menu->category->name_ar }}</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                    <hr />
                    @if($menu->category && $menu->category->requires_sauce)
                        <div class="mb-3">
                            <label class="form-label"><strong><x-bi en="Choose Sauce" ar="اختيار صوص" /></strong></label>
                            @if($menu->category->sauces->isNotEmpty())
                                @foreach($menu->category->sauces as $sauce)
                                    <div class="form-check">
                                        <input
                                            class="form-check-input sauce-option"
                                            type="radio"
                                            name="sauce_id"
                                            id="sauce-{{ $sauce->id }}"
                                            value="{{ $sauce->id }}"
                                            data-sauce-name="{{ $sauce->name }}"
                                            data-sauce-name-ar="{{ $sauce->name_ar }}"
                                        >
                                        <label class="form-check-label" for="sauce-{{ $sauce->id }}">
                                            {{ $sauce->name }} @if($sauce->name_ar) / <span dir="rtl" lang="ar">{{ $sauce->name_ar }}</span>@endif
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-danger mb-0"><x-bi en="No sauce options are currently available for this category." ar="لا توجد خيارات صوص متاحة حالياً لهذه الفئة." /></p>
                            @endif
                        </div>
                        <hr />
                    @endif
                    @if($menu->category && $menu->category->requires_side)
                        <div class="mb-3">
                            <label class="form-label"><strong><x-bi en="Choose 2 Sides" ar="اختر 2 سايدز" /></strong></label>
                            @if($menu->category->sides->isNotEmpty())
                                @foreach($menu->category->sides as $side)
                                    <div class="form-check">
                                        <input
                                            class="form-check-input side-option"
                                            type="checkbox"
                                            name="side_ids[]"
                                            id="side-{{ $side->id }}"
                                            value="{{ $side->id }}"
                                            data-side-name="{{ $side->name }}"
                                            data-side-name-ar="{{ $side->name_ar }}"
                                        >
                                        <label class="form-check-label" for="side-{{ $side->id }}">
                                            {{ $side->name }} @if($side->name_ar) / <span dir="rtl" lang="ar">{{ $side->name_ar }}</span>@endif
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-danger mb-0"><x-bi en="No side options are currently available for this category." ar="لا توجد خيارات سايد متاحة حالياً لهذه الفئة." /></p>
                            @endif
                        </div>
                        <hr />
                    @endif
                    <div class="cart_extra">
                        @if(!($menu->category && ($menu->category->requires_sauce || $menu->category->requires_side)))
                            <div class="cart-product-quantity">
                                <div class="quantity {{ $quantity==0? 'd-none':'' }}"  >
                                    <input type="button" value="-" class="minus">
                                    <input type="text" min="0" name="quantity" value="{{ $quantity }}" title="Qty" class="qty quantity-input" size="4" data-id="{{ $menu->id }}">
                                    <input type="button" value="+" class="plus">
                                </div>
                            </div>
                        @endif
                        <div class="cart_btn">
                            <button data-id="{{ $menu->id }}"
                                data-name="{{ $menu->name_ar ?: $menu->name }}"
                                data-price="{{ $menu->price }}" 
                                data-img_src="{{ $menu->image_url }}"                                            
                                data-requires-sauce="{{ ($menu->category && $menu->category->requires_sauce) ? 1 : 0 }}"
                                data-requires-side="{{ ($menu->category && $menu->category->requires_side) ? 1 : 0 }}"
                                type="button"  class="{{ ($menu->category && ($menu->category->requires_sauce || $menu->category->requires_side)) ? '' : ($quantity==0 ? '' : 'd-none') }} btn btn-default rounded-0 add-to-cart"  ><x-bi en="Add To Cart" ar="أضف للسلة"></x-bi></button>


                                <a href="{{ route('customer.cart') }}" class="{{ $quantity == 0 ? 'd-none' : '' }} btn checkout-btn btn-secondary rounded-0"><x-bi en="Proceed To Cart" ar="المتابعة إلى السلة"></x-bi></a>

                        </div>
                    </div>
                    <hr />
                    <div class="product_share">
                        <span>Share:</span>
                        <ul class="social_icons">
                            <!-- Facebook Share -->
                            <li>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::url()) }}" target="_blank">
                                    <i class="ion-social-facebook"></i>
                                </a>
                            </li>
                            
                            <!-- Twitter Share -->
                            <li>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::url()) }}&text=Check+this+out!" target="_blank">
                                    <i class="ion-social-twitter"></i>
                                </a>
                            </li>

                            
                            <!-- WhatsApp Share -->
                            <li>
                                <a href="https://api.whatsapp.com/send?text=Check+this+out!+{{ urlencode(Request::url()) }}" target="_blank">
                                    <i class="ion-social-whatsapp"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="row">
        	<div class="col-12">
            	<div class="medium_divider clearfix"></div>
            </div>
        </div>

        @if(filled($menu->video_url) || filled($menu->video_embed_url))
            <div class="row">
                <div class="col-12">
                    <div class="heading_s1">
                        <h3>How It's Made</h3>
                        <h3 dir="rtl" lang="ar">إزاي بيتعمل</h3>
                    </div>
                    @if(filled($menu->video_embed_url))
                        <div class="embed-responsive embed-responsive-16by9 mb-3">
                            <iframe
                                class="embed-responsive-item"
                                src="{{ $menu->video_embed_url }}"
                                title="Menu Preparation Video"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen
                                loading="lazy"
                            ></iframe>
                        </div>
                    @endif
                    @if(filled($menu->video_url))
                        <a href="{{ $menu->video_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-default btn-sm rounded-0">
                            <x-bi en="Open Video Link" ar="افتح رابط الفيديو"></x-bi>
                        </a>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="medium_divider"></div>
                </div>
            </div>
        @endif
    </div>
</div>
<!-- END SECTION SHOP -->
@endsection



 
