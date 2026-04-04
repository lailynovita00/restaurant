
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .order-success-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1055;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .order-success-card {
            width: 100%;
            max-width: 460px;
            background: linear-gradient(180deg, #fffdf9 0%, #ffffff 100%);
            border-radius: 18px;
            padding: 28px 24px;
            text-align: center;
            border: 1px solid rgba(123, 63, 0, 0.12);
            box-shadow: 0 18px 48px rgba(0, 0, 0, 0.18);
        }

        .order-success-check {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            margin: 0 auto 14px;
            background: #28a745;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            font-weight: 700;
        }

        .order-success-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: #f3ece2;
            color: #7b3f00;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 14px;
        }

        .order-success-kicker .ar {
            font-size: 0.88em;
            text-transform: none;
        }

        .order-success-summary {
            margin: 18px 0;
            padding: 14px;
            border-radius: 14px;
            background: #f8f5ef;
            text-align: left;
        }

        .order-success-summary-row {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            padding: 7px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .order-success-summary-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .order-success-summary-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .order-success-summary-value {
            font-weight: 700;
            color: #2b2b2b;
            text-align: right;
        }

        .payment-option-copy {
            display: block;
            font-size: 0.82rem;
            color: #6c757d;
            margin-top: 2px;
        }

        .checkout-mode-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 100%;
            background: #fff;
        }

        .checkout-mode-card.active {
            border-color: #7b3f00;
            box-shadow: 0 0 0 3px rgba(123, 63, 0, 0.12);
            background: #fffaf5;
        }

        .checkout-mode-radio {
            display: none;
        }

        .manual-payment-box {
            background: #f8f9fa;
            border: 1px dashed #c8c8c8;
            border-radius: 10px;
            padding: 14px;
        }

        .cart-item-name {
            display: block;
            font-weight: 600;
            line-height: 1.35;
            color: #50301c;
        }

        .cart-item-option {
            font-size: 12px;
            color: #6c757d;
            line-height: 1.35;
            margin-top: 2px;
        }

        .cart-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
        }

        .cart-meta-label {
            color: #6c757d;
            font-size: 12px;
            line-height: 1.3;
        }

        .cart-meta-value {
            font-weight: 600;
            color: #2b2b2b;
            text-align: right;
        }

        @media (max-width: 767.98px) {
            .shop_cart_table {
                overflow: visible;
            }

            .shop_cart_table .table {
                display: block;
            }

            .shop_cart_table tbody#cart-container {
                display: grid;
                gap: 12px;
            }

            .shop_cart_table tbody#cart-container > tr.cart-item-row {
                display: grid;
                grid-template-columns: 60px minmax(0, 1fr) auto;
                grid-template-areas:
                    "thumb name remove"
                    "thumb price remove"
                    "thumb qty qty"
                    "thumb total total";
                gap: 6px 10px;
                padding: 10px 12px;
                border: 1px solid #eadfcd;
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            }

            .shop_cart_table tbody#cart-container > tr.cart-item-row td {
                display: block;
                width: auto;
                text-align: left;
                padding: 0;
                border: 0;
            }

            .shop_cart_table tbody#cart-container > tr.cart-item-row td::before {
                display: none;
            }

            .shop_cart_table td.product-thumbnail {
                grid-area: thumb;
                text-align: center;
            }

            .shop_cart_table td.product-thumbnail img {
                width: 60px;
                height: 60px;
                max-width: 60px;
                object-fit: cover;
                border-radius: 10px;
            }

            .shop_cart_table td.product-name {
                grid-area: name;
                text-align: left;
                padding-right: 4px;
            }

            .shop_cart_table td.product-price {
                grid-area: price;
            }

            .shop_cart_table td.product-quantity {
                grid-area: qty;
            }

            .shop_cart_table td.product-subtotal {
                grid-area: total;
            }

            .shop_cart_table td.product-remove {
                grid-area: remove;
                display: flex;
                justify-content: flex-end;
                align-items: flex-start;
            }

            .shop_cart_table .product-remove .btn {
                min-width: 32px;
                height: 32px;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .shop_cart_table .quantity {
                justify-content: flex-end;
                gap: 0;
            }

            .shop_cart_table .quantity input {
                height: 32px;
            }

            .shop_cart_table .quantity .minus,
            .shop_cart_table .quantity .plus {
                width: 30px;
                min-width: 30px;
                padding: 0;
            }

            .shop_cart_table .quantity .qty {
                width: 38px;
                min-width: 38px;
                padding-left: 4px;
                padding-right: 4px;
                text-align: center;
            }

            .shop_cart_table tfoot td {
                padding-top: 12px;
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

    <script>
        $(document).ready(function () {
            function syncCheckoutMode() {
                var selectedMode = $('input[name="checkout_mode"]:checked').val() || 'instore';

                $('.checkout-mode-card').removeClass('active');
                $('.checkout-mode-card[data-mode="' + selectedMode + '"]').addClass('active');

                $('#dinein-checkout-block').toggle(selectedMode === 'instore');
                $('#online-checkout-block').toggle(selectedMode === 'delivery');
            }

            function syncTransferProofRequirement() {
                var paymentMethod = $('input[name="payment_method"]:checked').val() || 'cod';
                var needsProof = paymentMethod === 'instapay' || paymentMethod === 'vodafone_cash';

                $('#transfer-payment-help').toggle(needsProof);
                $('#transfer-proof-group').toggle(needsProof);
                $('#transfer_proof').prop('required', needsProof);
            }

            function getTotalItems(cart) {
                var totalItems = 0;
                $.each(cart, function (_, item) {
                    totalItems += Number(item.quantity || 0);
                });
                return totalItems;
            }

            // Update cart UI
            function updateCartUI(cart) {
                var cartContainer = $('#cart-container');
                cartContainer.empty(); // Clear existing cart
    
                var total = 0;
                $.each(cart, function (index, item) {
                    var price = Number(item.price || 0);
                    var quantity = Number(item.quantity || 0);
                    var subtotal = quantity * price;
                    total += subtotal;
                    var lineKey = item.line_key || item.id;
                    var sauceLabelAr = item.sauce_name_ar ? (' / ' + item.sauce_name_ar) : '';
                    var sauceLabel = item.sauce_name ? `<div class="cart-item-option">Sauce: ${item.sauce_name}${sauceLabelAr}</div>` : '';
                    var sideNames = Array.isArray(item.side_names) ? item.side_names.filter(Boolean).join(', ') : '';
                    var sideNamesAr = Array.isArray(item.side_names_ar) ? item.side_names_ar.filter(Boolean).join(', ') : '';
                    var sideLabel = sideNames
                        ? `<div class="cart-item-option">Sides: ${sideNames}${sideNamesAr ? (' / ' + sideNamesAr) : ''}</div>`
                        : '';
                    // Use the Laravel route helper to generate the URLs
                    var menuItemUrl = "{{ route('menu.item', ':id') }}".replace(':id', item.id);
                
                    cartContainer.append(`
                        <tr class="cart-item-row">
                            <td class="product-thumbnail"><a href="${menuItemUrl}"><img src="${item.img_src}" alt="product1"></a></td>
                            <td class="product-name" data-title="Product"><a href="${menuItemUrl}" class="cart-item-name">${item.name}</a>${sauceLabel}${sideLabel}</td>
                            <td class="product-price" data-title="Price"><div class="cart-meta-row"><span class="cart-meta-label">Price / السعر</span><span class="cart-meta-value">${price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")} {!! $site_settings->currency_symbol !!}</span></div></td>
                            <td class="product-quantity" data-title="Quantity">
                                <div class="cart-meta-row">
                                    <span class="cart-meta-label">Quantity / الكمية</span>
                                    <div class="quantity">
                                        <input type="button" value="-" class="minus">
                                        <input type="text" min="1" name="quantity" value="${quantity}" title="Qty" class="qty quantity-input" size="4" data-line_key="${lineKey}">
                                        <input type="button" value="+" class="plus">
                                    </div>
                                </div>
                            </td>
                            <td class="product-subtotal" data-title="Total"><div class="cart-meta-row"><span class="cart-meta-label">Total / الإجمالي</span><span class="cart-meta-value">${subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")} {!! $site_settings->currency_symbol !!}</span></div></td>
                            <td class="product-remove" data-title="Remove"><button class="btn btn-danger btn-sm remove-btn" data-line_key="${lineKey}"  > <i class="ti-close"></i></button></td>
                        </tr>
                    `);
                });
    
                if (total > 0) {
                    $('#customer-cart').show();
                    $('#checkout').show();
                    $('#empty-cart').hide();
                    
                  
                } else {
                    $('#customer-cart').hide();
                    $('#checkout').hide();
                    $('#empty-cart').show();

                }
    
                // Display the total
                $('#cart-subtotal').text(total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " {!! html_entity_decode($site_settings->currency_symbol) !!}");
                $('#total').val(total.toFixed(2));
                $('#cart_count').text(getTotalItems(cart));
    
            }
    
            // Function to remove item from cart
            function removeFromCart(lineKey) {
                var currentCount = parseInt($('#cart_count').text());
                
                $.post('{{ route('customer.cart.remove') }}', { _token: "{{ csrf_token() }}", cartkey: 'customer', line_key: lineKey }, function (data) {
                    if (data.success) {
                        updateCartUI(data.cart);
                        if (currentCount > 0) {
                            $('#cart_count').text(data.total_items);
                        }
                    }
                });
            }
    

            // Function to clear cart
            $('#clear-cart').click(function () {
                $.post('{{ route('customer.cart.clear') }}', { _token: "{{ csrf_token() }}", cartkey: 'customer' }, function (data) {
                    if (data.success) {
                        updateCartUI([]);
                        $('#cart_count').text(0);

                    }
                });
            });


            // Function to update cart quantity
            function updateCartQuantity(lineKey, quantity) {
                $.post('{{ route('customer.cart.update')  }}', {   _token: "{{ csrf_token() }}",   cartkey: 'customer', line_key: lineKey, quantity: quantity }, function (data) {
                    if (data.success) {
                        updateCartUI(data.cart);
                        $('#cart_count').text(data.total_items);
                    }
                });
            }

            // Delegated listener for dynamically rendered remove buttons
            $(document).on('click', '.remove-btn', function () {
                var lineKey = $(this).data('line_key');
                removeFromCart(lineKey);
            });

            // Delegated listener for dynamically rendered quantity inputs
            $(document).on('change', '.quantity-input', function () {
                var lineKey = $(this).data('line_key');
                var newQuantity = parseInt($(this).val(), 10) || 1;
                if (newQuantity < 1) newQuantity = 1;
                $(this).val(newQuantity);
                updateCartQuantity(lineKey, newQuantity);
            });
    
            // Initial fetch of cart items
            $.get('{{ route('customer.cart.view') }}', { cartkey: 'customer' }, function (data) {
                updateCartUI(data.cart);
            });

            $(document).on('change', 'input[name="checkout_mode"]', function () {
                syncCheckoutMode();
            });

            $(document).on('change', 'input[name="payment_method"]', function () {
                syncTransferProofRequirement();
            });

            $(document).on('click', '.plus', function () {
                var input = $(this).prev();  
                if (input.val()) {
                    input.val(+input.val() + 1).trigger('change');  
                }
            });

            $(document).on('click', '.minus', function () {
                var input = $(this).next(); 
                if (input.val() > 1) {
                    input.val(+input.val() - 1).trigger('change'); 
                }
            });

            @if(session('order_success_popup'))
                $('#order-success-popup').fadeIn(150).css('display', 'flex');
            @endif

            $('#order-success-close').on('click', function () {
                $('#order-success-popup').fadeOut(120);
            });

            $('#order-success-popup').on('click', function (e) {
                if (e.target === this) {
                    $('#order-success-popup').fadeOut(120);
                }
            });

            syncCheckoutMode();
            syncTransferProofRequirement();
                        
        });
    </script>
    
@endpush


@section('title', 'Cart')


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

@php
    $checkoutUser = auth()->user();
    $defaultCustomerName = trim(implode(' ', array_filter([
        $checkoutUser->first_name ?? null,
        $checkoutUser->middle_name ?? null,
        $checkoutUser->last_name ?? null,
    ])));
@endphp

 <!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section background_bg overlay_bg_50 page_title_light" data-img-src="/assets/images/cart_bg.jpg">
    <div class="container"><!-- STRART CONTAINER -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title">
            		<h1><x-bi en="Shopping Cart" ar="سلة التسوق" /></h1>
                </div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><x-bi en="Home" ar="الرئيسية" /></a></li>
                    <li class="breadcrumb-item active"><x-bi en="Shopping Cart" ar="سلة التسوق" /></li>
                </ol>
            </div>
        </div>
    </div><!-- END CONTAINER-->
</div>
<!-- END SECTION BREADCRUMB -->

<!-- START SECTION SHOP -->
<div class="section">
	<div class="container">
    @include('partials.message-bag')

        <div class="row" id="customer-cart">
         
            <div class="col-12">
                <div class="table-responsive shop_cart_table">
                	<table class="table">
                    	<thead>
                        	<tr>
                            	<th class="product-thumbnail">&nbsp;</th>
                                <th class="product-name"><x-bi en="Product" ar="المنتج" /></th>
                                <th class="product-price"><x-bi en="Price" ar="السعر" /></th>
                                <th class="product-quantity"><x-bi en="Quantity" ar="الكمية" /></th>
                                <th class="product-subtotal"><x-bi en="Total" ar="الإجمالي" /></th>
                                <th class="product-remove"><x-bi en="Remove" ar="حذف" /></th>
                            </tr>
                        </thead>
                        <tbody id="cart-container">

                            <!-- Cart items will be inserted here -->


                        </tbody>
                        <tfoot>
                        	<tr>
                            	<td colspan="6" class="px-0">
                                	<div class="row no-gutters align-items-center">

                                    	<div class="col-lg-4 col-md-6 mb-3 mb-md-0">
                                  
                                    	</div>
                                        <div class="col-lg-8 col-md-6 text-left text-md-right">
                                            <button id="clear-cart" class="btn btn-default rounded-0" type="submit"><x-bi en="Clear Cart" ar="مسح السلة" /></button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot> 
                    </table>

                    
                </div>
            </div>
 
        </div>
        <div class="row">
            <div class="col-12">
            	<div class="medium_divider"></div>

 
            </div>
        </div>
        <div class="row" id="checkout">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><x-bi en="Checkout Details" ar="تفاصيل الطلب" /></h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="checkout-mode-card {{ old('checkout_mode', 'instore') === 'instore' ? 'active' : '' }} d-block" data-mode="instore">
                                    <input class="checkout-mode-radio" type="radio" name="checkout_mode" value="instore" {{ old('checkout_mode', 'instore') === 'instore' ? 'checked' : '' }}>
                                    <div class="fw-bold mb-1"><x-bi en="Dine In" ar="مأكولات داخل المكان" /></div>
                                    <div class="text-muted small"><x-bi en="Customer will eat at the restaurant and provide table number." ar="العميل سيتناول الطعام داخل المطعم ويحدد رقم الترابيزة." /></div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="checkout-mode-card {{ old('checkout_mode') === 'delivery' ? 'active' : '' }} d-block" data-mode="delivery">
                                    <input class="checkout-mode-radio" type="radio" name="checkout_mode" value="delivery" {{ old('checkout_mode') === 'delivery' ? 'checked' : '' }}>
                                    <div class="fw-bold mb-1"><x-bi en="Online Order" ar="طلب أونلاين" /></div>
                                    <div class="text-muted small"><x-bi en="Customer enters delivery details and chooses COD or manual transfer." ar="العميل يملأ بيانات التوصيل ويختار الدفع عند الاستلام أو التحويل اليدوي." /></div>
                                </label>
                            </div>
                        </div>

                        <div id="dinein-checkout-block">
                            <form id="checkout-form" method="POST" action="{{ route('customer.checkout.dinein') }}">
                                @csrf
                                <input type="hidden" name="checkout_mode" value="instore">
                                <div class="mb-3">
                                    <label for="table_number" class="form-label"><x-bi en="Table Number" ar="رقم الترابيزة" /> <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="table_number" name="table_number" min="1" required>
                                    <div class="invalid-feedback">
                                        <x-bi en="Please provide a valid table number." ar="من فضلك أدخل رقم ترابيزة صحيح." />
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="dinein_customer_phone" class="form-label"><x-bi en="Phone Number (Optional - for Loyalty)" ar="رقم الهاتف (اختياري - للولاء)" /></label>
                                    <input type="text" class="form-control" id="dinein_customer_phone" name="customer_phone" value="{{ old('customer_phone', $checkoutUser->phone_number ?? '') }}" placeholder="e.g. 01001234567">
                                </div>
                                <div class="mb-3">
                                    <label for="additional_info" class="form-label"><x-bi en="Additional Information (Optional)" ar="معلومات إضافية (اختياري)" /></label>
                                    <textarea class="form-control" id="additional_info" name="additional_info" rows="3" placeholder="Any special requests or notes..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-default rounded-0"><x-bi en="Place Order" ar="تأكيد الطلب" /></button>
                            </form>
                        </div>

                        <div id="online-checkout-block" style="display:none;">
                            <form method="POST" action="{{ route('customer.checkout.online') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="checkout_mode" value="delivery">
                                <div class="mb-3">
                                    <label for="customer_name" class="form-label"><x-bi en="Name" ar="الاسم" /> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ old('customer_name', $defaultCustomerName) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="customer_phone" class="form-label"><x-bi en="Phone Number" ar="رقم الهاتف" /> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $checkoutUser->phone_number ?? '') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="delivery_address" class="form-label"><x-bi en="Delivery Address" ar="عنوان التوصيل" /> <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="delivery_address" name="delivery_address" rows="3" required>{{ old('delivery_address', $checkoutUser->address ?? '') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label d-block"><x-bi en="Payment Method" ar="طريقة الدفع" /> <span class="text-danger">*</span></label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payment_cod">
                                            <x-bi en="Pay on Delivery (COD)" ar="الدفع عند الاستلام" />
                                            <span class="payment-option-copy"><x-bi en="Pay the courier when the order arrives." ar="ادفع للكابتن عند وصول الطلب." /></span>
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_instapay" value="instapay" {{ old('payment_method') === 'instapay' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payment_instapay">
                                            <x-bi en="Transfer to Instapay" ar="تحويل إلى إنستاباي" />
                                            <span class="payment-option-copy"><x-bi en="Upload your receipt before placing the order." ar="ارفع إثبات التحويل قبل تأكيد الطلب." /></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_vodafone_cash" value="vodafone_cash" {{ old('payment_method') === 'vodafone_cash' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payment_vodafone_cash">
                                            <x-bi en="Transfer to Vodafone Cash" ar="تحويل إلى فودافون كاش" />
                                            <span class="payment-option-copy"><x-bi en="Upload your receipt before placing the order." ar="ارفع إثبات التحويل قبل تأكيد الطلب." /></span>
                                        </label>
                                    </div>
                                </div>

                                <div id="transfer-payment-help" class="manual-payment-box mb-3" style="display:none;">
                                    <div class="fw-bold mb-2"><x-bi en="Transfer Details" ar="بيانات التحويل" /></div>
                                    <div class="mb-1"><strong>Instapay:</strong> {{ config('payments.manual_transfer.instapay_number') }}</div>
                                    <div><strong>Vodafone Cash:</strong> {{ config('payments.manual_transfer.vodafone_cash_number') }}</div>
                                </div>

                                <div id="transfer-proof-group" class="mb-3" style="display:none;">
                                    <label for="transfer_proof" class="form-label"><x-bi en="Upload Transfer Proof" ar="ارفع إثبات التحويل" /> <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="transfer_proof" name="transfer_proof" accept=".jpg,.jpeg,.png,.pdf,.webp">
                                    <small class="text-muted"><x-bi en="Allowed files: JPG, PNG, WEBP, PDF. Max 5 MB." ar="الملفات المسموحة: JPG و PNG و WEBP و PDF بحد أقصى 5 ميجابايت." /></small>
                                </div>

                                <div class="mb-3">
                                    <label for="online_additional_info" class="form-label"><x-bi en="Additional Information (Optional)" ar="معلومات إضافية (اختياري)" /></label>
                                    <textarea class="form-control" id="online_additional_info" name="additional_info" rows="3" placeholder="Any special requests or notes...">{{ old('additional_info') }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-default rounded-0"><x-bi en="Place Order" ar="تأكيد الطلب" /></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="empty-cart">
            <div class="col-12">
                <div class="alert alert-secondary text-center" role="alert">
                    <h4 class="alert-heading"><x-bi en="Your Cart is Empty!" ar="السلة فاضية!" /></h4>
                    <p><x-bi en="Looks like you haven't added any items to your cart yet. No worries, we've got plenty of delicious options waiting for you." ar="يبدو إنك لسه ما ضفتش أي منتجات للسلة. ولا يهمك، عندنا اختيارات لذيذة كتير مستنياك." /></p>
                    <hr>
                    <p class="mb-0"><x-bi en="Head over to our" ar="روح على" /> <a href="{{ route('menu') }}" class="alert-link"><x-bi en="menu" ar="المنيو" /></a> <x-bi en="and start exploring!" ar="وابدأ تختار اللي يعجبك!" /></p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SECTION SHOP -->

@if(session('order_success_popup'))
    <div class="order-success-overlay" id="order-success-popup">
        <div class="order-success-card">
            <div class="order-success-check">&#10003;</div>
            <div class="order-success-kicker">
                <span>Order Confirmed</span>
                <span class="ar" dir="rtl" lang="ar">تم تأكيد الطلب</span>
            </div>
            <h5 class="mb-2"><x-bi en="Order Placed Successfully" ar="تم تأكيد الطلب بنجاح" /></h5>
            @if(session('order_success_popup.order_type') === 'delivery')
                <p class="mb-2">
                    <x-bi en="Your online order has been received and is now waiting for review." ar="تم استلام طلبك الأونلاين وهو الآن بانتظار المراجعة." />
                </p>
                <div class="order-success-summary">
                    <div class="order-success-summary-row">
                        <div class="order-success-summary-label"><x-bi en="Order Number" ar="رقم الطلب" /></div>
                        <div class="order-success-summary-value">#{{ session('order_success_popup.order_no') }}</div>
                    </div>
                    <div class="order-success-summary-row">
                        <div class="order-success-summary-label"><x-bi en="Order Type" ar="نوع الطلب" /></div>
                        <div class="order-success-summary-value"><x-bi en="Online" ar="أونلاين" /></div>
                    </div>
                    <div class="order-success-summary-row">
                        <div class="order-success-summary-label"><x-bi en="Customer" ar="العميل" /></div>
                        <div class="order-success-summary-value">{{ session('order_success_popup.customer_name') }}</div>
                    </div>
                </div>
            @else
                <p class="mb-2">
                    <x-bi en="Your dine in order has been sent successfully to the restaurant." ar="تم إرسال طلبك داخل المكان بنجاح إلى المطعم." />
                </p>
                <div class="order-success-summary">
                    <div class="order-success-summary-row">
                        <div class="order-success-summary-label"><x-bi en="Order Number" ar="رقم الطلب" /></div>
                        <div class="order-success-summary-value">#{{ session('order_success_popup.order_no') }}</div>
                    </div>
                    <div class="order-success-summary-row">
                        <div class="order-success-summary-label"><x-bi en="Order Type" ar="نوع الطلب" /></div>
                        <div class="order-success-summary-value"><x-bi en="Dine In" ar="داخل المكان" /></div>
                    </div>
                    <div class="order-success-summary-row">
                        <div class="order-success-summary-label"><x-bi en="Table Number" ar="رقم الترابيزة" /></div>
                        <div class="order-success-summary-value">{{ session('order_success_popup.table_number') }}</div>
                    </div>
                </div>
            @endif
            <button type="button" class="btn btn-success" id="order-success-close"><x-bi en="OK" ar="حسناً" /></button>
        </div>
    </div>
@endif
@endsection



 
