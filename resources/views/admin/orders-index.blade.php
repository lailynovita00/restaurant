
@extends('layouts.admin')

@push('styles')
    <!-- base:css -->
    <link rel="stylesheet" href="/admin_resources/vendors/typicons.font/font/typicons.css">
    <link rel="stylesheet" href="/admin_resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/admin_resources/css/vertical-layout-light/style.css">
    <!-- DataTables   CSS -->

    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="/admin_resources/css/small-box.css">
        <style>
            .orders-toolbar {
                gap: 12px;
            }

            .orders-toolbar .form-group {
                margin-bottom: 0;
            }

            .orders-toolbar .form-control {
                min-width: 180px;
            }

            #orders-table th .bi-text {
                display: inline-flex;
                flex-direction: column;
                line-height: 1.1;
            }

            #orders-table th .bi-text .bi-ar,
            #orders-table th .bi-text .bi-ar-inline {
                font-size: 0.78em;
                margin-top: 2px;
                opacity: 0.9;
            }

            .bilingual-stack {
                display: inline-flex;
                flex-direction: column;
                line-height: 1.1;
                text-align: left;
            }

            .bilingual-stack .bi-ar {
                font-size: 0.78em;
                margin-top: 2px;
                opacity: 0.85;
            }

            .order-item-stack {
                margin-bottom: 8px;
                line-height: 1.15;
            }

            .order-item-stack:last-child {
                margin-bottom: 0;
            }

            .order-item-en {
                font-weight: 600;
            }

            .order-item-ar {
                font-size: 0.78em;
                opacity: 0.85;
                margin-top: 2px;
            }

            .order-item-meta {
                font-size: 0.78em;
                color: #6c757d;
                margin-top: 3px;
            }

            .order-item-meta-ar {
                font-size: 0.95em;
                margin-top: 2px;
            }

            .order-cancel-reason {
                margin-top: 6px;
                line-height: 1.2;
            }

            .order-cancel-reason-en {
                font-size: 0.8em;
                color: #6c757d;
            }

            .order-cancel-reason-ar {
                font-size: 0.78em;
                color: #6c757d;
                margin-top: 2px;
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

 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 
    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
 
<script id="orders-page-config" type="application/json">{!! json_encode([
    'isPendingFilter' => $filter === 'pending',
    'isAllOrdersPage' => $filter === null,
    'canManageAllOrdersReports' => $canManageAllOrdersReports,
    'ajaxUrl' => route('admin.orders.index', ['filter' => $filter]),
    'downloadUrl' => route('admin.orders.download'),
    'selectedYear' => $selectedYear,
    'selectedMonth' => $selectedMonth,
    'selectedOrderType' => $selectedOrderType ?? null,
    'availableMonths' => $availableMonths ?? [],
]) !!}</script>

<script type="text/javascript">
        var ordersDataTable = null;
        var ordersPageConfig = JSON.parse(document.getElementById('orders-page-config').textContent);
        var isPendingFilter = ordersPageConfig.isPendingFilter;
        var isAllOrdersPage = ordersPageConfig.isAllOrdersPage;
        var canManageAllOrdersReports = ordersPageConfig.canManageAllOrdersReports;

    function buildOrdersDownloadUrl() {
        var downloadUrl = new URL(ordersPageConfig.downloadUrl, window.location.origin);
        var yearFilter = document.getElementById('all-orders-year-filter');
        var monthFilter = document.getElementById('all-orders-month-filter');
        var orderTypeFilter = document.getElementById('all-orders-order-type-filter');
        var selectedYear = yearFilter ? yearFilter.value : '';
        var selectedMonth = monthFilter ? monthFilter.value : '';
        var selectedOrderType = orderTypeFilter ? orderTypeFilter.value : '';

        if (selectedYear) {
            downloadUrl.searchParams.set('year', selectedYear);
        }

        if (selectedMonth) {
            downloadUrl.searchParams.set('month', selectedMonth);
        }

        if (selectedOrderType) {
            downloadUrl.searchParams.set('order_type', selectedOrderType);
        }

        return downloadUrl.toString();
    }

    function syncOrdersDownloadButton() {
        var downloadButton = document.getElementById('download-all-orders-button');
        if (!downloadButton) {
            return;
        }

        downloadButton.setAttribute('href', buildOrdersDownloadUrl());
    }

    function showOrdersNotice(type, message) {
        var iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        var noticeHtml = '' +
            '<div class="alert alert-' + type + ' alert-dismissible mb-3 mx-2 orders-inline-notice">' +
                '<button type="button" class="close" aria-label="Close" onclick="this.closest(\'.orders-inline-notice\').remove();">' +
                    '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '<i class="fa ' + iconClass + '"></i> ' + message +
            '</div>';

        $('#orders-inline-notification-container').html(noticeHtml);
    }

    $(function () {
            var tableColumns = isPendingFilter
                ? [
                        { data: 'created_at', name: 'created_at' },
                        { data: 'order_type', name: 'order_type' },
                        { data: 'payment_method', name: 'payment_method' },
                        { data: 'table_number', name: 'table_number' },
                        { data: 'status', name: 'status' },
                        { data: 'total_price', name: 'total_price' },
                        { data: 'ordered_items', name: 'ordered_items', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'update_order', name: 'update_order', orderable: false, searchable: false }
                    ]
                : [
                        { data: 'created_at', name: 'created_at' },
                        { data: 'order_type', name: 'order_type' },
                    { data: 'payment_method', name: 'payment_method' },
                        { data: 'table_number', name: 'table_number' },
                        { data: 'status', name: 'status' },
                        { data: 'total_price', name: 'total_price' },
                        { data: 'ordered_items', name: 'ordered_items', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'update_order', name: 'update_order', orderable: false, searchable: false }
                    ];

            if (isAllOrdersPage && canManageAllOrdersReports) {
                syncOrdersDownloadButton();

                $('#all-orders-year-filter').on('change', function () {
                    // Reset month filter when year changes
                    $('#all-orders-month-filter').val('');
                    syncOrdersDownloadButton();
                });

                $('#all-orders-month-filter').on('change', function () {
                    syncOrdersDownloadButton();
                });

                $('#all-orders-order-type-filter').on('change', function () {
                    syncOrdersDownloadButton();
                });

                $('#apply-orders-filter-button').on('click', function () {
                    if (ordersDataTable) {
                        ordersDataTable.ajax.reload();
                    }
                });
            }
          
            ordersDataTable = $('.data-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
              url: ordersPageConfig.ajaxUrl,
              data: function (requestData) {
                  if (!isAllOrdersPage) {
                      return;
                  }

                  var yearFilter = document.getElementById('all-orders-year-filter');
                  var monthFilter = document.getElementById('all-orders-month-filter');
                  var orderTypeFilter = document.getElementById('all-orders-order-type-filter');
                  requestData.year = yearFilter ? yearFilter.value : '';
                  requestData.month = monthFilter ? monthFilter.value : '';
                  requestData.order_type = orderTypeFilter ? orderTypeFilter.value : '';
              }
          },
                    language: {
                        lengthMenu: 'Show _MENU_ entries',
                        search: 'Search | بحث:',
                        processing: 'Processing... | جاري المعالجة...',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries | عرض _START_ إلى _END_ من _TOTAL_ عنصر',
                        infoEmpty: 'Showing 0 to 0 of 0 entries | عرض 0 إلى 0 من 0 عنصر',
                        zeroRecords: 'No matching records found | لا توجد نتائج مطابقة',
                        paginate: {
                            previous: 'Previous | السابق',
                            next: 'Next | التالي'
                        }
                    },
                    columns: tableColumns
      });

      window.addEventListener('admin:order-stats-updated', function (event) {
          if (!ordersDataTable) {
              return;
          }

          var previous = event.detail && event.detail.previous ? event.detail.previous : null;
          var current = event.detail && event.detail.current ? event.detail.current : null;

          if (!current) {
              return;
          }

          // Refresh table only when there is a real order count change.
          if (!previous || Number(previous.all_orders_count) !== Number(current.all_orders_count)) {
              ordersDataTable.ajax.reload(null, false);
          }
      });
          
    });
 
    $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);  
            var id = button.data('id');  
            var actionUrl = "{{ route('admin.orders.destroy', ':id') }}".replace(':id', id);
            $('#deleteForm').attr('action', actionUrl);
        });
    });

    function markAsCompleted(id) {
        if (confirm('Are you sure you want to mark this order as completed?')) {
            $.post('{{ route("admin.orders.complete", ":id") }}'.replace(':id', id), {
                _token: '{{ csrf_token() }}'
            })
            .done(function(data) {
                if (data.success) {
                    showOrdersNotice('success', 'Order marked as completed successfully');
                    if (ordersDataTable) {
                        ordersDataTable.ajax.reload(null, false);
                    }
                } else {
                    showOrdersNotice('danger', data.message || 'Unable to update order status.');
                }
            })
            .fail(function() {
                showOrdersNotice('danger', 'An error occurred while updating the order');
            });
        }
    }

    function updateOrderStatus(id) {
        var selectedStatus = $('#order-status-select-' + id).val();
        var cancellationReason = '';

        if (!selectedStatus) {
            showOrdersNotice('danger', 'Please select a status first.');
            return;
        }

        if (selectedStatus === 'cancelled') {
            cancellationReason = String($('#order-cancellation-reason-input-' + id).val() || '').trim();
        }

        $.post('{{ route("admin.orders.update", ":id") }}'.replace(':id', id), {
            _token: '{{ csrf_token() }}',
            status: selectedStatus,
            cancellation_reason: cancellationReason
        })
        .done(function() {
            showOrdersNotice('success', 'Order status updated successfully');
            if (ordersDataTable) {
                ordersDataTable.ajax.reload(null, false);
            }
        })
        .fail(function(xhr) {
            var message = 'An error occurred while updating the order.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showOrdersNotice('danger', message);
        });
    }

    $(document).on('click', '.order-status-option', function() {
        var orderId = $(this).data('order-id');
        var status = $(this).data('status');
        var labelEn = $(this).data('label-en');
        var labelAr = $(this).data('label-ar');

        $('#order-status-select-' + orderId).val(status);

        $('#order-status-dropdown-' + orderId).html(
            '<span class="bi-en">' + labelEn + '</span>' +
            '<span class="bi-ar">' + labelAr + '</span>'
        );

        var reasonWrap = $('#order-cancellation-reason-wrap-' + orderId);
        var reasonInput = $('#order-cancellation-reason-input-' + orderId);
        if (status === 'cancelled') {
            reasonWrap.show();
        } else {
            reasonWrap.hide();
            reasonInput.val('');
        }
    });
</script>

@endpush


@section('title', 'Admin - Manage Orders')




@section('content')

<div class="main-panel">
    <div class="content-wrapper">
 
    @include('partials.message-bag')

    <div id="orders-inline-notification-container"></div>

    @include('partials.order-stats')

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                @if($filter)
                    <x-bi :en="ucfirst($filter).' Orders'" ar="الطلبات" />
                @else
                    <x-bi en="All Orders" ar="كل الطلبات" />
                @endif
            </h5>

            @if($filter === null && $canManageAllOrdersReports)
            <div class="d-flex flex-wrap align-items-end orders-toolbar">
                <div class="form-group">
                    <label for="all-orders-year-filter" class="mb-1">
                        <x-bi en="Filter by Year" ar="تصفية حسب السنة" />
                    </label>
                    <select id="all-orders-year-filter" class="form-control">
                        <option value="">All Years | كل السنوات</option>
                        @foreach($availableYears as $year)
                        <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="all-orders-month-filter" class="mb-1">
                        <x-bi en="Filter by Month" ar="تصفية حسب الشهر" />
                    </label>
                    <select id="all-orders-month-filter" class="form-control">
                        <option value="">All Months | كل الأشهر</option>
                        @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" @selected($selectedMonth === $month)>{{ \Carbon\Carbon::createFromDate(null, $month, 1)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <select id="all-orders-order-type-filter" class="form-control form-control-sm">
                        <option value=""><x-bi en="All Order Types" ar="كل أنواع الطلبات" /></option>
                        @foreach(($availableOrderTypes ?? []) as $orderTypeValue => $orderTypeLabel)
                            <option value="{{ $orderTypeValue }}" {{ ($selectedOrderType ?? null) === $orderTypeValue ? 'selected' : '' }}>{{ $orderTypeLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button id="apply-orders-filter-button" type="button" class="btn btn-primary">
                        <i class="fa fa-filter"></i>
                        <x-bi en="Filter" ar="تصفية" />
                    </button>
                </div>

                <div class="form-group">
                    <a id="download-all-orders-button" href="{{ route('admin.orders.download', array_filter(['year' => $selectedYear, 'month' => $selectedMonth])) }}" class="btn btn-success">
                        <i class="fa fa-download"></i>
                        <x-bi en="Download Table" ar="تنزيل الجدول" />
                    </a>
                </div>
            </div>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive pb-5">
                <table class="table table-bordered data-table" id="orders-table">
                    <thead>
                        <tr>
                            @if($filter === 'pending')
                            <th><x-bi en="Date" ar="التاريخ" /></th>
                            <th><x-bi en="Order Type" ar="نوع الطلب" /></th>
                            <th><x-bi en="Payment" ar="الدفع" /></th>
                            <th><x-bi en="Table Number" ar="رقم الترابيزة" /></th>
                            <th><x-bi en="Status" ar="الحالة" /></th>
                            <th><x-bi en="Price" ar="السعر" /></th>
                            <th><x-bi en="Ordered Items" ar="الأصناف المطلوبة" /></th>
                            <th><x-bi en="Actions" ar="الإجراءات" /></th>
                            <th><x-bi en="Update Order" ar="تحديث الطلب" /></th>
                            @else
                            <th><x-bi en="Date" ar="التاريخ" /></th>
                            <th><x-bi en="Order Type" ar="نوع الطلب" /></th>
                            <th><x-bi en="Payment" ar="الدفع" /></th>
                            <th><x-bi en="Table Number" ar="رقم الترابيزة" /></th>
                            <th><x-bi en="Status" ar="الحالة" /></th>
                            <th><x-bi en="Price" ar="السعر" /></th>
                            <th><x-bi en="Ordered Items" ar="الأصناف المطلوبة" /></th>
                            <th><x-bi en="Actions" ar="الإجراءات" /></th>
                            <th><x-bi en="Update Order" ar="تحديث الطلب" /></th>
                            @endif
                        </tr>
                    </thead>
                </table>
              
            </div>
        </div>
    </div>

    @if ($loggedInUser->role == "global_admin")

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
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>    
    @endif

    </div>
    <!-- content-wrapper ends -->
    @include('partials.admin.footer')
  </div>
  <!-- main-panel ends -->
@endsection



 
