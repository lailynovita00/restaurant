
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
    <style>
        .menu-category-tabs {
            display: flex;
            flex-wrap: nowrap;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 6px;
            margin-bottom: 18px;
        }
        .menu-category-tabs .nav-link {
            white-space: nowrap;
            border-radius: 0;
            border: 1px solid #92824e;
            background-color: #fff;
            color: #92824e;
            font-weight: 600;
            padding: 8px 14px;
            transition: all 0.2s ease-in-out;
        }
        .menu-category-tabs .nav-link:hover,
        .menu-category-tabs .nav-link:focus {
            background-color: #f6f1e7;
            color: #765e39;
            border-color: #92824e;
        }
        .menu-category-tabs .nav-link.active,
        .menu-category-tabs .show > .nav-link {
            background-color: #92824e !important;
            border-color: #92824e !important;
            color: #fff !important;
        }

        .tab-content > .tab-pane {
            display: none;
            height: auto;
            visibility: visible;
        }

        .tab-content > .active {
            display: block;
        }

        .menu-items-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 24px;
        }

        .menu-item-col {
            display: flex;
            min-width: 0;
        }

        .menu-item-col .single_product {
            width: 100%;
            margin-bottom: 0;
        }

        .menu-item-col .menu_product_img img {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
        }

        @media (max-width: 991.98px) {
            .menu-items-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
            }
        }

        @media (max-width: 767.98px) {
            .menu-items-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .menu-item-col .single_product {
                border-radius: 8px;
            }

            .menu-item-col .single_product .menu_product_info {
                padding: 10px 8px;
            }

            .menu-item-col .single_product .menu_product_info .title * {
                font-size: 14px;
                line-height: 1.35;
            }

            .menu-item-col .single_product .menu_product_info p {
                font-size: 12px;
                line-height: 1.4;
                margin-bottom: 8px;
            }

            .menu-item-col .single_product .form-control,
            .menu-item-col .single_product .btn,
            .menu-item-col .single_product .form-check-label,
            .menu-item-col .single_product label {
                font-size: 11px;
            }

            .menu-item-col .single_product .form-control {
                padding: 4px 6px;
                height: auto;
            }

            .menu-item-col .single_product .menu-qty-input {
                max-width: 44px !important;
                padding-left: 2px;
                padding-right: 2px;
            }

            .menu-item-col .single_product .menu-qty-minus,
            .menu-item-col .single_product .menu-qty-plus {
                min-width: 28px;
                padding-left: 0;
                padding-right: 0;
            }

            .menu-item-col .single_product .add-to-cart-menu {
                width: 100%;
                padding-left: 6px;
                padding-right: 6px;
            }
        }

        @media (min-width: 430px) and (max-width: 767.98px) {
            .menu-items-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 540px) and (max-width: 767.98px) {
            .menu-items-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

    </style>
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
        $(function () {
            function biText(en, ar) {
                return { en: en, ar: ar };
            }

            function showPopupMessage(message, type) {
                const popupType = type || 'error';
                const background = popupType === 'success' ? '#28a745' : '#dc3545';

                let container = $('#menu-popup-container');
                if (!container.length) {
                    $('body').append('<div id="menu-popup-container" style="position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px;"></div>');
                    container = $('#menu-popup-container');
                }

                const popup = $('<div></div>').attr('style',
                    'min-width:220px;max-width:360px;padding:12px 14px;border-radius:8px;color:#fff;background:' + background + ';box-shadow:0 8px 24px rgba(0,0,0,0.18);font-size:14px;line-height:1.4;display:none;'
                );

                if (typeof message === 'object' && message !== null && message.en && message.ar) {
                    popup.html(
                        '<div style="font-weight:600;">' + message.en + '</div>' +
                        '<div dir="rtl" lang="ar" style="margin-top:4px;opacity:0.95;">' + message.ar + '</div>'
                    );
                } else {
                    popup.text(message);
                }

                container.append(popup);
                popup.fadeIn(160);

                setTimeout(function () {
                    popup.fadeOut(180, function () {
                        popup.remove();
                    });
                }, 2400);
            }

            $('.menu-qty-plus').on('click', function () {
                const $input = $(this).siblings('.menu-qty-input');
                const current = parseInt($input.val(), 10) || 1;
                $input.val(current + 1);
            });

            $('.menu-qty-minus').on('click', function () {
                const $input = $(this).siblings('.menu-qty-input');
                const current = parseInt($input.val(), 10) || 1;
                $input.val(Math.max(1, current - 1));
            });

            $('.menu-qty-input').on('input', function () {
                let value = parseInt($(this).val(), 10);
                if (isNaN(value) || value < 1) {
                    value = 1;
                }
                $(this).val(value);
            });

            $(document).on('change', '.menu-side-option', function () {
                const $card = $(this).closest('.menu_product_info');
                const checked = $card.find('.menu-side-option:checked');
                if (checked.length > 2) {
                    $(this).prop('checked', false);
                    showPopupMessage(biText('You can select up to 2 sides.', 'يمكنك اختيار 2 سايد كحد أقصى.'), 'error');
                }
            });

            $('.add-to-cart-menu').on('click', function () {
                const $btn = $(this);
                const $card = $btn.closest('.menu_product_info');
                const quantity = parseInt($card.find('.menu-qty-input').val(), 10) || 1;
                const requiresSauce = Number($btn.data('requires_sauce')) === 1;
                const requiresSide = Number($btn.data('requires_side')) === 1;
                const $selectedSauce = $card.find('.menu-sauce-select option:selected');
                const sauceId = $selectedSauce.val() || null;
                const sauceName = $selectedSauce.data('sauce_name') || null;
                const sauceNameAr = $selectedSauce.data('sauce_name_ar') || null;
                const selectedSides = $card.find('.menu-side-option:checked');
                const sideIds = selectedSides.map(function () { return Number($(this).val()); }).get().filter(Boolean);
                const sideNames = selectedSides.map(function () { return $(this).data('side-name') || null; }).get().filter(Boolean);
                const sideNamesAr = selectedSides.map(function () { return $(this).data('side-name-ar') || null; }).get();

                if (requiresSauce && !sauceId) {
                    showPopupMessage(biText('Please choose a sauce first.', 'من فضلك اختر الصوص أولاً.'), 'error');
                    return;
                }

                if (requiresSide && sideIds.length !== 2) {
                    showPopupMessage(biText('Please choose exactly 2 sides.', 'من فضلك اختر 2 سايد بالضبط.'), 'error');
                    return;
                }

                $.ajax({
                    url: "{{ route('customer.cart.add') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        cartkey: "customer",
                        id: $btn.data('id'),
                        name: $btn.data('name'),
                        price: $btn.data('price'),
                        img_src: $btn.data('img_src'),
                        quantity: quantity,
                        sauce_id: sauceId,
                        sauce_name: sauceName,
                        sauce_name_ar: sauceNameAr,
                        side_ids: sideIds,
                        side_names: sideNames,
                        side_names_ar: sideNamesAr
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#cart_count').text(response.total_items);

                            const originalText = $btn.text();
                            $btn.text('Added (' + quantity + ')').prop('disabled', true);
                            setTimeout(function () {
                                $btn.text(originalText).prop('disabled', false);
                            }, 900);
                        } else {
                            showPopupMessage(response.message || biText('Failed to add item to cart.', 'فشل إضافة المنتج إلى السلة.'), 'error');
                        }
                    },
                    error: function (xhr) {
                        const message = xhr?.responseJSON?.message || biText('An error occurred while adding the item to the cart.', 'حدث خطأ أثناء إضافة المنتج إلى السلة.');
                        showPopupMessage(message, 'error');
                    }
                });
            });
        });
    </script>

@endpush


@section('title', 'Menus')


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
    <div class="breadcrumb_section background_bg overlay_bg_50 page_title_light" data-img-src="/assets/images/menu_bg2.jpg">
        <div class="container"><!-- STRART CONTAINER -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title">
                        <h1><x-bi en="Menu" ar="المنيو" /></h1>
                    </div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><x-bi en="Home" ar="الرئيسية" /></a></li>
                        <li class="breadcrumb-item active"><x-bi en="Our Menu" ar="المنيو" /></li>
                    </ol>
                </div>
            </div>
        </div><!-- END CONTAINER-->
    </div>
    <!-- END SECTION BREADCRUMB -->


    <!-- START SECTION OUR MENU -->
<div class="section pb_70">
    <div class="container">
        @include('partials.message-bag')




        <form action="{{ route('menu') }}" method="GET" class="mb-4">

            <div class="form-group">
                <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search menu items..." value="{{ request('search') }}">
                  <div class="input-group-append">
                    <button type="submit" class="btn btn-default btn-sm rounded-0"><i class="linearicons-magnifier"></i> Search</button>
                  </div>
                </div>
              </div>
  
              @if (request('search'))
              <div class="card mb-3">
                  <div class="card-body">
                      <p class="mb-2">
                          We found
                          <strong>{{ $categories->sum(fn($category) => $category->menus->count()) }}</strong>
                          {{ $categories->sum(fn($category) => $category->menus->count()) === 1 ? 'result' : 'results' }}
                          for your query: <em>"{{ request('search') }}"</em>.
                      </p>
                      <p class="mb-3" dir="rtl" lang="ar">
                          لقينا
                          <strong>{{ $categories->sum(fn($category) => $category->menus->count()) }}</strong>
                          نتيجة للبحث بتاعك: <em>"{{ request('search') }}"</em>.
                      </p>
                      <a href="{{ route('menu') }}" class="btn btn-default btn-sm rounded-0"><x-bi en="Return to Menu" ar="ارجع للمنيو" /></a>
                  </div>
              </div>
          @endif
              
        </form>

        @php
            $visibleCategories = $categories->filter(fn($category) => $category->menus->isNotEmpty())->values();
        @endphp

        @if ($visibleCategories->isNotEmpty())
            <ul class="nav nav-pills menu-category-tabs" id="menu-category-tab" role="tablist">
                @foreach ($visibleCategories as $category)
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $loop->first ? 'active' : '' }}"
                            id="cat-tab-{{ $category->id }}"
                            data-bs-toggle="pill"
                            data-bs-target="#cat-pane-{{ $category->id }}"
                            type="button"
                            role="tab"
                            aria-controls="cat-pane-{{ $category->id }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        >
                            <x-bi :en="$category->name" :ar="$category->name_ar ?: $category->name" />
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="menu-category-tabContent">
                @foreach ($visibleCategories as $category)
                    <div
                        class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                        id="cat-pane-{{ $category->id }}"
                        role="tabpanel"
                        aria-labelledby="cat-tab-{{ $category->id }}"
                    >
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="heading_tab_header animation" data-animation="fadeInUp" data-animation-delay="0.02s">
                                    <div class="heading_s1">
                                        <h2>
                                            <x-bi :en="$category->name" :ar="$category->name_ar ?: $category->name" />
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="menu-items-grid">
                            @foreach ($category->menus as $menu)
                                @php
                                    $categorySauces = $hasSauceTables ? $category->sauces : collect();
                                    $categorySides = $hasSideTables ? $category->sides : collect();
                                @endphp
                                <div class="menu-item-col">
                                    <div class="single_product">
                                        <a href="{{ route('menu.item', $menu->id) }}">
                                            <div class="menu_product_img">
                                                <img src="{{ $menu->image_url }}" alt="{{ $menu->name }} img">
                                            </div>
                                        </a>
                                        <div class="menu_product_info">
                                            <div class="title">
                                                <h5><a href="{{ route('menu.item', $menu->id) }}"><x-bi :en="$menu->name" :ar="$menu->name_ar ?: $menu->name" /></a></h5>
                                            </div>
                                            <p>{{ number_format($menu->price, 2) }} {!! $site_settings->currency_symbol !!}</p>
                                            @if($category->requires_sauce)
                                                <div class="mb-2">
                                                    <label class="mb-1" style="font-size: 12px; font-weight: 600; display:block;">
                                                        <x-bi en="Choose Sauce" ar="اختيار صوص" />
                                                    </label>
                                                    <select class="form-control form-control-sm menu-sauce-select">
                                                        <option value="">Select Sauce / اختر الصوص</option>
                                                        @foreach($categorySauces as $sauce)
                                                            <option
                                                                value="{{ $sauce->id }}"
                                                                data-sauce_name="{{ $sauce->name }}"
                                                                data-sauce_name_ar="{{ $sauce->name_ar }}"
                                                            >
                                                                {{ $sauce->name }}@if($sauce->name_ar) / {{ $sauce->name_ar }}@endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            @if($category->requires_side)
                                                <div class="mb-2">
                                                    <label class="mb-1" style="font-size: 12px; font-weight: 600; display:block;">
                                                        <x-bi en="Choose 2 Sides" ar="اختر 2 سايدز" />
                                                    </label>
                                                    @foreach($categorySides as $side)
                                                        <div class="form-check" style="font-size: 12px;">
                                                            <input
                                                                class="form-check-input menu-side-option"
                                                                type="checkbox"
                                                                value="{{ $side->id }}"
                                                                id="menu-side-{{ $menu->id }}-{{ $side->id }}"
                                                                data-side-name="{{ $side->name }}"
                                                                data-side-name-ar="{{ $side->name_ar }}"
                                                            >
                                                            <label class="form-check-label" for="menu-side-{{ $menu->id }}-{{ $side->id }}">
                                                                {{ $side->name }}@if($side->name_ar) / {{ $side->name_ar }}@endif
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="d-flex align-items-center mb-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-0 menu-qty-minus">-</button>
                                                <input
                                                    type="number"
                                                    min="1"
                                                    value="1"
                                                    class="form-control form-control-sm text-center mx-1 menu-qty-input"
                                                    style="max-width: 70px;"
                                                >
                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-0 menu-qty-plus">+</button>
                                            </div>
                                            <button
                                                type="button"
                                                class="btn btn-default btn-sm rounded-0 add-to-cart-menu"
                                                data-id="{{ $menu->id }}"
                                                data-name="{{ $menu->name_ar ?: $menu->name }}"
                                                data-price="{{ $menu->price }}"
                                                data-img_src="{{ $menu->image_url }}"
                                                data-requires_sauce="{{ $category->requires_sauce ? 1 : 0 }}"
                                                data-requires_side="{{ $category->requires_side ? 1 : 0 }}"
                                            >
                                                <x-bi en="Add To Cart" ar="أضف للسلة" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No categories found.</p>
        @endif
            
 

    </div>
</div>
<!-- END SECTION OUR MENU -->

 
@endsection



 
