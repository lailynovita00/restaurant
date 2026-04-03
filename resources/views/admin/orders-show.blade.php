
@extends('layouts.admin')

@push('styles')
    <!-- base:css -->
    <link rel="stylesheet" href="/admin_resources/vendors/typicons.font/font/typicons.css">
    <link rel="stylesheet" href="/admin_resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/admin_resources/css/vertical-layout-light/style.css">
    <style>
        .proof-preview-wrap {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .proof-preview-thumb {
            width: 180px;
            max-width: 100%;
            border-radius: 12px;
            border: 1px solid #dee2e6;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        }

        .proof-preview-frame {
            width: 100%;
            max-width: 520px;
            height: 360px;
            border: 1px solid #dee2e6;
            border-radius: 12px;
        }
    </style>
    
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
<script src="/admin_resources/js/dashboard.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<link rel="stylesheet" href="/admin_resources/css/small-box.css">


@endpush


@section('title', 'Admin - View Order')




@section('content')

<div class="main-panel">
    <div class="content-wrapper">
 
      @include('partials.message-bag')

      @include('partials.order-stats')

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Order Details - #{{ $order->order_sequence ?? $order->id }} </span>

                <div class="d-flex align-items-center" style="gap: 8px;">
                    <a href="{{ route('admin.order.receipt', $order->id) }}" target="_blank" class="btn btn-outline-dark btn-sm">
                        <i class="fa fa-print"></i> Print Receipt
                    </a>

                    @if ($order->status !== 'completed' && $order->status !== 'cancelled')
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal"><x-bi en="Update Order" ar="تحديث الطلب" /></button>
                    @endif
                </div>
            
        
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered mt-2">    
                            <tr>
                                <th>Order No.</th>
                                <td>#{{ $order->order_sequence ?? $order->id }}</td>
                            </tr>                               
                 
                            <tr>
                                <th>Total Paid</th>
                                <td>{{ number_format($order->total_price + ($order->delivery_fee ?? 0), 2) }} {!! $site_settings->currency_symbol !!}</td>
                            </tr>
                            <tr>
                                <th>Delivery Fee</th>
                                <td>{{ $order->delivery_fee === null ? 'N/A' : number_format($order->delivery_fee, 2) . ' ' . html_entity_decode($site_settings->currency_symbol) }}</td>
                            </tr>
                            <tr>
                                <th>Delivery Distance</th>
                                <td> {{ $order->delivery_distance === null ? 'N/A' : $order->delivery_distance . ' miles' }}</td>                              
                            </tr>
                            <tr>
                                <th>Price Per Mile</th>
                                <td> {{ $order->price_per_mile === null ? 'N/A' : number_format($order->price_per_mile,2) . ' ' . html_entity_decode($site_settings->currency_symbol) }}</td>                              
                            </tr>
                            
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered mt-2">     
                            <tr>
                                <th>Created At</th>
                                <td>{{ $order->created_at->format('g:i A -  j M, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $order->updated_at->format('g:i A -  j M, Y') }}</td>
                            </tr>                             
                            <tr>
                                <th>Order Type</th>
                                <td>
                                    @if($order->order_type === 'delivery')
                                        Online
                                    @elseif($order->order_type === 'instore')
                                        Dine In
                                    @else
                                        {{ ucfirst($order->order_type) }}
                                    @endif
                                </td>
                            </tr>                  

                            <tr>
                                <th>Payment Method</th>
                                <td>{{ strtoupper(str_replace('_', ' ', (string) $order->payment_method)) ?: 'N/A' }}</td>
                            </tr>

                            @if($order->order_type === 'delivery')
                                <tr>
                                    <th>Customer Name</th>
                                    <td>{{ $order->online_customer_name ?: ($order->customer?->first_name ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Phone Number</th>
                                    <td>{{ $order->online_customer_phone ?: ($order->customer?->phone_number ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Delivery Address</th>
                                    <td>{{ $order->online_delivery_address ?: ($order->deliveryAddressWithTrashed?->full_address ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Transfer Proof</th>
                                    <td>
                                        @if($order->transfer_proof_path)
                                            @php
                                                $proofUrl = route('admin.order.transfer-proof', $order->id);
                                                $proofExtension = strtolower(pathinfo($order->transfer_proof_path, PATHINFO_EXTENSION));
                                                $isImageProof = in_array($proofExtension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                            @endphp

                                            <div class="proof-preview-wrap">
                                                @if($isImageProof)
                                                    <a href="{{ $proofUrl }}" target="_blank">
                                                        <img src="{{ $proofUrl }}" alt="Transfer Proof" class="proof-preview-thumb">
                                                    </a>
                                                @else
                                                    <a href="{{ $proofUrl }}" target="_blank" class="btn btn-sm btn-outline-primary" style="width: fit-content;">Open PDF Proof</a>
                                                @endif

                                                <a href="{{ $proofUrl }}" target="_blank" class="btn btn-sm btn-outline-primary" style="width: fit-content;">View Full Proof</a>

                                                @if($isImageProof)
                                                    <img src="{{ $proofUrl }}" alt="Transfer Proof Preview" class="proof-preview-frame" style="object-fit: contain; background:#f8f9fa;">
                                                @else
                                                    <iframe src="{{ $proofUrl }}" class="proof-preview-frame"></iframe>
                                                @endif
                                            </div>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <th>Status</th>
                                <td>


                                    @switch($order->status)
                                        @case('pending')
                                            <span class="badge badge-danger"><i class="fa fa-exclamation-circle"></i> {{ ucfirst($order->status) }}</span>
                                            @break
                                        @case('completed')
                                            <span class="badge badge-success"><i class="fa fa-check"></i> {{ ucfirst($order->status) }}</span>
                                            @break
                                        @case('active')
                                            <span class="badge badge-warning"><i class="fa fa-clock"></i> {{ ucfirst($order->status) }}</span>
                                            @break
                                        @default
                                            {{ ucfirst($order->status) }}
                                    @endswitch
                                                     
                                </td>
                                
                            </tr>

                            @if(!empty($order->cancellation_reason))
                                <tr>
                                    <th>Cancellation Reason</th>
                                    <td>{{ $order->cancellation_reason }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>


            </div>
            
        </div>
   


        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Order Items </span>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderItems as $item)
                            <tr>
                                <td>
                                    <i class="fa fa-circle"></i> {{ $item->menu_name_en ?? $item->menu_name }}
                                    @if(!empty($item->menu_name_ar) && $item->menu_name_ar !== ($item->menu_name_en ?? $item->menu_name))
                                        <div dir="rtl" lang="ar" style="font-size: 0.8em; margin-top: 2px;">{{ $item->menu_name_ar }}</div>
                                    @endif
                                    @if(!empty($item->sauce_name))
                                        <div style="font-size: 0.8em; margin-top: 2px; color:#6c757d;">
                                            Sauce: {{ $item->sauce_name }}
                                            @if(!empty($item->sauce_name_ar)) / <span dir="rtl" lang="ar">{{ $item->sauce_name_ar }}</span>@endif
                                        </div>
                                    @endif
                                    @if(!empty($item->side_names) && is_array($item->side_names))
                                        <div style="font-size: 0.8em; margin-top: 2px; color:#6c757d;">
                                            Sides: {{ implode(', ', array_filter($item->side_names)) }}
                                            @if(!empty($item->side_names_ar) && is_array($item->side_names_ar)) / <span dir="rtl" lang="ar">{{ implode('، ', array_filter($item->side_names_ar)) }}</span>@endif
                                        </div>
                                    @endif
                                </td>
                                <td>x {{ $item->quantity }}</td>
                                <td>{{ number_format($item->subtotal, 2) }} {!! $site_settings->currency_symbol !!}</td>
                            </tr>
                        @endforeach
                        <tr style="border:2px solid #000">
                            <td><b>TOTAL</b></td>
                            <td> </td>
                            <td><b>{{ number_format($order->total_price, 2)  }} {!! $site_settings->currency_symbol !!}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {!! $order->additional_info   ? '<span class="badge badge-danger"><i class="fa fa-exclamation-circle"></i> Additional Info:</span>  ' . e($order->additional_info)    : '' !!}
            </div>
        </div>
        
   



   
        
     <hr/>

     @if ($loggedInUser->role == "global_admin")
 
        <!-- Delete Button to trigger modal -->
        <button type="button" class="btn-sm btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
            <i class="fa fa-trash"></i> Delete Order
        </button>




        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this order?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    @endif










        <!-- Update Order Modal -->
        <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update Order Status | تحديث حالة الطلب</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="orderStatus">Order Status | حالة الطلب</label>
                                <select class="form-control" id="orderStatus" name="status">
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed | مكتمل</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled | ملغي</option>
                                </select>
                            </div>
                            <div class="form-group mt-2" id="cancellationReasonWrap" style="display: none;">
                                <label for="cancellationReason">Cancellation Reason (Optional) | سبب الإلغاء (اختياري)</label>
                                <textarea class="form-control" id="cancellationReason" name="cancellation_reason" rows="3" maxlength="500" placeholder="Write reason here (optional)">{{ old('cancellation_reason', $order->cancellation_reason) }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary"><x-bi en="Update" ar="تحديث" /></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const statusSelect = document.getElementById('orderStatus');
                const reasonWrap = document.getElementById('cancellationReasonWrap');
                const reasonInput = document.getElementById('cancellationReason');

                if (!statusSelect || !reasonWrap || !reasonInput) {
                    return;
                }

                function toggleCancellationReason() {
                    const isCancelled = statusSelect.value === 'cancelled';
                    reasonWrap.style.display = isCancelled ? 'block' : 'none';

                    if (!isCancelled) {
                        reasonInput.value = '';
                    }
                }

                statusSelect.addEventListener('change', toggleCancellationReason);
                toggleCancellationReason();
            })();
        </script>











    </div>
    <!-- content-wrapper ends -->
    @include('partials.admin.footer')
  </div>
  <!-- main-panel ends -->
@endsection



 
