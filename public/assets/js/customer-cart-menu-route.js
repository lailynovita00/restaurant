$(document).ready(function () {

    function biText(en, ar) {
        return { en: en, ar: ar };
    }

    function showPopupMessage(message, type) {
        var popupType = type || 'error';
        var background = popupType === 'success' ? '#28a745' : '#dc3545';

        var container = $('#cart-popup-container');
        if (!container.length) {
            $('body').append('<div id="cart-popup-container" style="position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px;"></div>');
            container = $('#cart-popup-container');
        }

        var popup = $('<div></div>').attr('style',
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

     
    // Attach click event to add-to-cart buttons
    $(document).on('change', '.side-option', function () {
        var checked = $('input[name="side_ids[]"]:checked');
        if (checked.length > 2) {
            $(this).prop('checked', false);
            showPopupMessage(biText('You can select up to 2 sides.', 'يمكنك اختيار 2 سايد كحد أقصى.'), 'error');
        }
    });

    $('.add-to-cart').click(function () {
        var button = $(this);
        var id = $(this).data('id');
        var name = $(this).data('name');
        var price = $(this).data('price');
        var img_src = $(this).data('img_src');
        var requiresSauce = Number($(this).data('requires-sauce')) === 1;
        var requiresSide = Number($(this).data('requires-side')) === 1;

        var selectedSauce = $('input[name="sauce_id"]:checked');
        var sauceId = selectedSauce.val() || null;
        var sauceName = selectedSauce.data('sauce-name') || null;
        var sauceNameAr = selectedSauce.data('sauce-name-ar') || null;
        var selectedSides = $('input[name="side_ids[]"]:checked');
        var sideIds = [];
        var sideNames = [];
        var sideNamesAr = [];

        selectedSides.each(function () {
            sideIds.push(Number($(this).val()));
            sideNames.push($(this).data('side-name'));
            sideNamesAr.push($(this).data('side-name-ar') || null);
        });

        if (requiresSauce && !sauceId) {
            showPopupMessage(biText('Please choose a sauce first.', 'من فضلك اختر الصوص أولاً.'), 'error');
            return;
        }

        if (requiresSide && sideIds.length !== 2) {
            showPopupMessage(biText('Please choose exactly 2 sides.', 'من فضلك اختر 2 سايد بالضبط.'), 'error');
            return;
        }

        $.ajax({
            url: addToCartUrl, // Defined globally in the blade file
            type: 'POST',
            data: {
                _token: csrfToken, // Defined globally in the blade file
                cartkey: 'customer',
                id: id,
                name: name,
                price: price,
                img_src: img_src,
                sauce_id: sauceId,
                sauce_name: sauceName,
                sauce_name_ar: sauceNameAr,
                side_ids: sideIds,
                side_names: sideNames,
                side_names_ar: sideNamesAr
            },
            success: function (data) {
                if (data.success) {
                    $('#cart_count').text(data.total_items);
                    if (!requiresSauce && !requiresSide) {
                        $('.quantity-input').val(1);
                    }
                    $('.checkout-btn').removeClass('d-none').addClass('d-block');
                    if (!requiresSauce && !requiresSide) {
                        $('.quantity').removeClass('d-none').addClass('d-block');
                        $('.add-to-cart').removeClass('d-block').addClass('d-none');
                    } else {
                        button.removeClass('d-none').addClass('d-block');
                    }
                } else {
                    showPopupMessage(data.message || biText('Failed to add item to cart.', 'فشل إضافة المنتج إلى السلة.'), 'error');
                }
            },
            error: function (xhr) {
                var message = xhr?.responseJSON?.message || biText('An error occurred while adding the item to the cart.', 'حدث خطأ أثناء إضافة المنتج إلى السلة.');
                showPopupMessage(message, 'error');
            }
        });
    });
 

    // Listener to quantity inputs
    $('.quantity-input').change(function () {
        var id = $(this).data('id');
        var quantity = $(this).val();

        if (quantity == 0) {
            // Remove
            $.post(removeFromCartUrl, { _token: csrfToken, cartkey: 'customer', id: id }, function (data) {
                if (data.success) {
                    $('#cart_count').text(data.total_items);
                    $('.add-to-cart').removeClass('d-none').addClass('d-block');
                    $('.quantity').removeClass('d-block').addClass('d-none');
                    $('.checkout-btn').removeClass('d-block').addClass('d-none');
                }
            });
        } else {
            // Update
            $.post(updateCartUrl, { _token: csrfToken, cartkey: 'customer', id: id, quantity: quantity }, function (data) {
                if (data.success) {
                    $('#cart_count').text(data.total_items);
                }
            });
        }
    });

    
    // Plus button listener
    $('.plus').on('click', function () {
        if ($(this).prev().val()) {
            $(this).prev().val(+$(this).prev().val() + 1).trigger('change');
        }
    });

    // Minus button listener
    $('.minus').on('click', function () {
        if ($(this).next().val() > 0) {
            $(this).next().val(+$(this).next().val() - 1).trigger('change');
        }
    });


});
