
@extends('layouts.admin')

@push('styles')
    <!-- base:css -->
    <link rel="stylesheet" href="/admin_resources/vendors/typicons.font/font/typicons.css">
    <link rel="stylesheet" href="/admin_resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/admin_resources/css/vertical-layout-light/style.css">

<!-- DataTables   CSS -->

    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

    
@endpush

@push('scripts')
 
<script src="/admin_resources/vendors/js/vendor.bundle.base.js"></script>
<script src="/admin_resources/js/off-canvas.js"></script>
<script src="/admin_resources/js/hoverable-collapse.js"></script>
<script src="/admin_resources/js/template.js"></script>
<script src="/admin_resources/js/settings.js"></script>
<script src="/admin_resources/js/todolist.js"></script>
<!-- plugin js for this page -->
<script src="/admin_resources/vendors/progressbar.js/progressbar.min.js"></script>
<script src="/admin_resources/vendors/chart.js/Chart.min.js"></script>
<!-- Custom js for this page-->
 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 
<!-- DataTables JS  -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable for the menu table
        $('#menu-table').DataTable({
            "paging": true,        
            "searching": true,      
            "ordering": false,      
            "info": false,          
            "lengthChange": false, 
            "processing": true,     
            "bPaginate": true,      
            "bSort": false,         
     
        });
    });
</script>

<script>  

$(document).ready(function () {
    var pendingMenuSelection = null;

    // Function to add item to cart
    function addToCart(payload) {
        $.post("{{ route('admin.cart.add') }}", payload, function (data) {
            if (data.success) {
                updateCartUI(data.cart);
 
            }
        }).fail(function (xhr) {
            var message = 'Unable to add item to cart.';
            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            alert(message);
        });
    }

    // Function to remove item from cart
    function removeFromCart(lineKey) {
        $.post("{{ route('admin.cart.remove') }}", {  _token: "{{ csrf_token() }}", cartkey: 'admin', line_key: lineKey }, function (data) {
            if (data.success) {
                updateCartUI(data.cart);
            }
        });
    }

    // Function to clear cart
    $('#clear-cart').click(function () {
        $.post("{{ route('admin.cart.clear') }}", { _token: "{{ csrf_token() }}", cartkey: 'admin' }, function (data) {
            if (data.success) {
                updateCartUI([]);
            }
        });
    });

    // Update cart UI
    function updateCartUI(cart) {
        var cartContainer = $('#cart-container');
        cartContainer.empty(); // Clear existing cart

        var total = 0;
        $.each(cart, function (index, item) {
            var subtotal = item.quantity * item.price;
            total += subtotal;
            var lineKey = item.line_key || item.id;
            var sauceLabelAr = item.sauce_name_ar ? (' / ' + item.sauce_name_ar) : '';
            var sauceLabel = item.sauce_name ? `<div style="font-size:12px;color:#6c757d;">Sauce: ${item.sauce_name}${sauceLabelAr}</div>` : '';
            var sideNames = Array.isArray(item.side_names) ? item.side_names.filter(Boolean).join(', ') : '';
            var sideNamesAr = Array.isArray(item.side_names_ar) ? item.side_names_ar.filter(Boolean).join(', ') : '';
            var sideLabel = sideNames ? `<div style="font-size:12px;color:#6c757d;">Sides: ${sideNames}${sideNamesAr ? (' / ' + sideNamesAr) : ''}</div>` : '';

            cartContainer.append(`
                <tr class="cart-item">
                    <td>${item.name}${sauceLabel}${sideLabel}</td>
                    <td>${Number(item.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")} {!! $site_settings->currency_symbol !!}</td>
                    <td><input type="number" value="${item.quantity}" min="1" data-line_key="${lineKey}" class="quantity-input" style="width: 4.5em;"></td>
                    <td>${subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")} {!! $site_settings->currency_symbol !!}</td>
                    <td><button class="btn btn-danger btn-sm remove-btn" data-line_key="${lineKey}"> <i class="fa fa-times" aria-hidden="true"></i></button></td>
                </tr> 

            `);
        });

                if(total > 0){
                        $('#clear-cart').show();
                        $('#checkout-btn').show();
                } else {
          $('#clear-cart').hide();  
          $('#checkout-btn').hide();         
        }

        // Display the total
        $('#cart-total').text('Total: ' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' {!! html_entity_decode($site_settings->currency_symbol) !!}');
        $('#total').val(total.toFixed(2))
        
        // listener to remove buttons
        $('.remove-btn').click(function () {
            var lineKey = $(this).data('line_key');
            removeFromCart(lineKey);
        });

      // listener to quantity inputs
      $('.quantity-input').change(function () {
          var lineKey = $(this).data('line_key');
          var newQuantity = $(this).val();
          updateCartQuantity(lineKey, newQuantity);
      });

    }

    // Attach addToCart function to buttons
    $('.add-to-cart').click(function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var price = $(this).data('price');
        var requiresSauce = Number($(this).data('requires_sauce')) === 1;
        var requiresSide = Number($(this).data('requires_side')) === 1;
        var sauces = $(this).data('sauces') || [];
        var sides = $(this).data('sides') || [];

        pendingMenuSelection = {
            _token: "{{ csrf_token() }}",
            cartkey: 'admin',
            id: id,
            name: name,
            price: price,
            requiresSauce: requiresSauce,
            requiresSide: requiresSide,
            sauces: sauces,
            sides: sides,
        };

        if (!requiresSauce && !requiresSide) {
            addToCart({
                _token: "{{ csrf_token() }}",
                cartkey: 'admin',
                id: id,
                name: name,
                price: price
            });
            return;
        }

        $('#posSauceOptions').empty();
        $('#posSideOptions').empty();
        $('#posSauceGroup').toggle(requiresSauce);
        $('#posSideGroup').toggle(requiresSide);

        if (requiresSauce) {
            if (!Array.isArray(sauces) || sauces.length === 0) {
                alert('No sauce options configured for this category.');
                return;
            }

            sauces.forEach(function (sauce) {
                const label = `${sauce.name}${sauce.name_ar ? ' / ' + sauce.name_ar : ''}`;
                $('#posSauceOptions').append(`
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="pos_sauce_id" id="pos-sauce-${sauce.id}" value="${sauce.id}" data-sauce-name="${sauce.name}" data-sauce-name-ar="${sauce.name_ar || ''}">
                        <label class="form-check-label" for="pos-sauce-${sauce.id}">${label}</label>
                    </div>
                `);
            });
        }

        if (requiresSide) {
            if (!Array.isArray(sides) || sides.length < 2) {
                alert('At least 2 side options are required for this category.');
                return;
            }

            sides.forEach(function (side) {
                const label = `${side.name}${side.name_ar ? ' / ' + side.name_ar : ''}`;
                $('#posSideOptions').append(`
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="pos_side_ids[]" id="pos-side-${side.id}" value="${side.id}" data-side-name="${side.name}" data-side-name-ar="${side.name_ar || ''}">
                        <label class="form-check-label" for="pos-side-${side.id}">${label}</label>
                    </div>
                `);
            });
        }

        $('#posOptionsModal').modal('show');
    });

    $('#confirmPosOptions').click(function () {
        if (!pendingMenuSelection) {
            return;
        }

        var payload = {
            _token: pendingMenuSelection._token,
            cartkey: pendingMenuSelection.cartkey,
            id: pendingMenuSelection.id,
            name: pendingMenuSelection.name,
            price: pendingMenuSelection.price,
        };

        if (pendingMenuSelection.requiresSauce) {
            var selectedSauce = $('input[name="pos_sauce_id"]:checked');
            if (!selectedSauce.length) {
                alert('Please choose a sauce.');
                return;
            }
            payload.sauce_id = Number(selectedSauce.val());
            payload.sauce_name = selectedSauce.data('sauce-name');
            payload.sauce_name_ar = selectedSauce.data('sauce-name-ar') || null;
        }

        if (pendingMenuSelection.requiresSide) {
            var selectedSides = $('input[name="pos_side_ids[]"]:checked');
            if (selectedSides.length !== 2) {
                alert('Please choose exactly 2 sides.');
                return;
            }

            payload.side_ids = selectedSides.map(function () { return Number($(this).val()); }).get();
            payload.side_names = selectedSides.map(function () { return $(this).data('side-name'); }).get();
            payload.side_names_ar = selectedSides.map(function () { return $(this).data('side-name-ar') || null; }).get();
        }

        $('#posOptionsModal').modal('hide');
        addToCart(payload);
    });


      // Function to update cart quantity
    function updateCartQuantity(lineKey, quantity) {
        $.post("{{ route('admin.cart.update')  }}", {   _token: "{{ csrf_token() }}",   cartkey: 'admin', line_key: lineKey, quantity: quantity }, function (data) {
            if (data.success) {
                updateCartUI(data.cart);
            }
        });
    }


    // Initial fetch of cart items
    $.get("{{ route('admin.cart.view') }}", { cartkey: 'admin' }, function (data) {
        updateCartUI(data.cart);
    });
});

 
 

     $('#checkout-btn').click(function(event) {
        event.preventDefault();
        $('#confirmationModal').modal('show');
    });

     $('#confirmSubmit').click(function() {
        $('#checkout-form').submit();
    });

</script>
 
@endpush


@section('title', 'Admin - POS')




@section('content')

<div class="main-panel">
    <div class="content-wrapper">
 
      @include('partials.message-bag')
 

      <div class="row">
        <div class="col-lg-6 d-flex grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                  <div class="d-flex flex-wrap justify-content-between">
                    <h4 class="card-title mb-3">Menus</h4>
                  </div>
                  <div class="table-responsive">
                    <table class="table" id="menu-table">
                        <thead style="display: none;">
                            <tr>
                                <th>Menu Item</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                      <tbody>
                        @forelse ($menus as $menu)
                        <tr>
                            <td>
                                <!-- Trigger for Lightbox Modal -->
                                <img src="{{ $menu->image_url }}" alt="Menu Image" width="50" class="img-thumbnail trigger-lightbox" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="{{ $menu->image_url }}">  {{ $menu->name }}
                            </td>
                            <td>{{ $menu->price }} {!! $site_settings->currency_symbol !!}</td>
                            <td>
     
                                <button class="m-1 btn btn-secondary btn-sm add-to-cart"
                                data-id="{{ $menu->id }}"
                                data-name="{{ $menu->name }}"
                                data-price="{{ $menu->price }}"
                                data-requires_sauce="{{ ($menu->category && $menu->category->requires_sauce) ? 1 : 0 }}"
                                data-requires_side="{{ ($menu->category && $menu->category->requires_side) ? 1 : 0 }}"
                                data-sauces='@json(($menu->category ? $menu->category->sauces->where("is_active", true)->values()->map(fn($sauce) => ["id" => $sauce->id, "name" => $sauce->name, "name_ar" => $sauce->name_ar]) : collect())->values())'
                                data-sides='@json(($menu->category ? $menu->category->sides->where("is_active", true)->values()->map(fn($side) => ["id" => $side->id, "name" => $side->name, "name_ar" => $side->name_ar]) : collect())->values())'>
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No menus available.</td>
                        </tr>
                    @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
        </div>
        <div class="col-lg-6 d-flex grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between">
                <h4 class="card-title mb-3">Cart</h4>
              </div>
 

              <div style="overflow-x: auto;">
              <table class="table" >
                <thead >
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th style="width:20%;">Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="cart-container">
                    <!-- Cart items will be inserted here -->
                </tbody>
            </table>
            </div>

            <hr/>
            <div id="cart-total" class="mt-3"></div>
            <hr/>

 


            </div>
            <div class="card-footer">
              <button id="clear-cart" style="display: none;" class="btn-block btn btn-warning mt-3"> Clear Cart</button>

            </div>

          </div>
        </div>
      </div>

    @if ($menus->count() != 0)
       <div class="card mb-4">
        <div class="card-body">
          <form id="checkout-form" method="POST" action="{{ route('admin.order.store') }}">
            <input type="hidden"   id="total" value="0">
            <input type="hidden"   name="cartkey" value="admin">
                        <input type="hidden" name="payment_method" value="INSTORE">
            @csrf
 

              <hr>
              <table class="table table-bordered"> 
                <tbody>
                    <tr>
                        <td><strong>Additional Info</strong></td>
                        <td>
                            <textarea class="form-control" id="additional_info" name="additional_info" rows="2" placeholder=""></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            


            </form>
        </div>
        <div class="card-footer text-right">
            <button type="button" style="display:none;" id="checkout-btn" form="checkout-form" class="btn btn-primary">Checkout</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>

        </div>
    </div>     
    @endif

    
 



<!-- POS Options Modal -->
<div class="modal fade" id="posOptionsModal" tabindex="-1" role="dialog" aria-labelledby="posOptionsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="posOptionsModalLabel">Select Options</h5>
              <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                  <i class="fa fa-times" aria-hidden="true"></i>
              </button>
          </div>
          <div class="modal-body">
              <div id="posSauceGroup" class="mb-3" style="display:none;">
                  <label><strong>Choose Sauce</strong></label>
                  <div id="posSauceOptions"></div>
              </div>
              <div id="posSideGroup" class="mb-3" style="display:none;">
                  <label><strong>Choose 2 Sides</strong></label>
                  <div id="posSideOptions"></div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" id="confirmPosOptions">Add To Cart</button>
          </div>
      </div>
  </div>
</div>


<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="confirmationModalLabel">Confirm Order</h5>
              <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                  <i class="fa fa-times" aria-hidden="true"></i>
              </button>
          </div>
          <div class="modal-body">
              Are you sure you want to submit this order?
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" id="confirmSubmit">Confirm</button>
          </div>
      </div>
  </div>
</div>




    </div>
    <!-- content-wrapper ends -->
    @include('partials.admin.footer')
  </div>
  <!-- main-panel ends -->
@endsection



 
