@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="/admin_resources/vendors/typicons.font/font/typicons.css">
<link rel="stylesheet" href="/admin_resources/vendors/css/vendor.bundle.base.css">
<link rel="stylesheet" href="/admin_resources/css/vertical-layout-light/style.css">
<style>
    .loyalty-summary-card {
        border-radius: 12px;
        border: 1px solid #e6e6e6;
        background: #fff;
        padding: 18px;
    }

    .loyalty-summary-value {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.1;
    }

    .loyalty-whatsapp-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .loyalty-status-positive {
        color: #198754;
    }

    .loyalty-status-negative {
        color: #dc3545;
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var singleDeleteModal = document.getElementById('loyaltyDeleteModal');

        if (singleDeleteModal) {
            singleDeleteModal.addEventListener('show.bs.modal', function (event) {
                var trigger = event.relatedTarget;
                var exclusionKey = trigger ? trigger.getAttribute('data-exclusion-key') : '';
                var customerName = trigger ? trigger.getAttribute('data-customer-name') : '';

                var input = singleDeleteModal.querySelector('input[name="exclusion_key"]');
                if (input) {
                    input.value = exclusionKey || '';
                }

                var nameHolder = singleDeleteModal.querySelector('[data-role="customer-name"]');
                if (nameHolder) {
                    nameHolder.textContent = customerName || '-';
                }
            });
        }
    });
</script>
@endpush

@section('title', 'Admin - Loyalty')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        @include('partials.message-bag')

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><x-bi en="Eligible Customers (Auto)" ar="العملاء المؤهلون تلقائياً" /></span>
                <div class="d-flex align-items-center" style="gap:8px;">
                    @if(!$eligibleCustomers->isEmpty())
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#loyaltyDeleteAllModal">
                            <x-bi en="Delete All" ar="حذف الكل" />
                        </button>
                    @endif
                    <span class="badge bg-success text-white">{{ $eligibleCustomers->count() }}</span>
                </div>
            </div>
            <div class="card-body">
                @if($eligibleCustomers->isEmpty())
                    <div class="alert alert-warning mb-0"><x-bi en="No eligible customers yet. Customers with total spending >= 1000 LE (same phone across multiple orders) will appear here automatically." ar="لا يوجد عملاء مؤهلون بعد. العملاء الذين يصل إجمالي إنفاقهم إلى 1000 جنيه أو أكثر (بنفس رقم الهاتف عبر عدة طلبات) سيظهرون هنا تلقائياً." /></div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><x-bi en="Customer" ar="العميل" /></th>
                                    <th><x-bi en="Phone" ar="رقم الهاتف" /></th>
                                    <th><x-bi en="Orders" ar="عدد الطلبات" /></th>
                                    <th><x-bi en="Total Spending" ar="إجمالي الإنفاق" /></th>
                                    <th><x-bi en="Last Order No" ar="آخر رقم طلب" /></th>
                                    <th><x-bi en="Last Order Date" ar="تاريخ آخر طلب" /></th>
                                    <th><x-bi en="Action" ar="الإجراء" /></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($eligibleCustomers as $customer)
                                    <tr>
                                        <td>{{ $customer['customer_name'] }}</td>
                                        <td>{{ $customer['phone_display'] }}</td>
                                        <td>{{ $customer['orders_count'] }}</td>
                                        <td>{{ number_format((float) $customer['total_spent'], 2) }} {{ $currencySymbol }}</td>
                                        <td>#{{ $customer['last_order_no'] }}</td>
                                        <td>{{ optional($customer['last_order_at'])->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap" style="gap:6px;">
                                                @if(!empty($customer['whatsapp_url']))
                                                    <form action="{{ route('admin.loyalty.send') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="exclusion_key" value="{{ $customer['exclusion_key'] }}">
                                                        <input type="hidden" name="wa_phone" value="{{ $customer['wa_phone'] }}">
                                                        <button type="submit" class="btn btn-success btn-sm loyalty-whatsapp-btn">
                                                            <i class="fa fa-whatsapp"></i>
                                                            <span><x-bi en="Send Loyalty" ar="إرسال الولاء" /></span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-secondary btn-sm" disabled><x-bi en="No WhatsApp Number" ar="لا يوجد رقم واتساب" /></button>
                                                @endif

                                                <button
                                                    type="button"
                                                    class="btn btn-outline-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#loyaltyDeleteModal"
                                                    data-exclusion-key="{{ $customer['exclusion_key'] }}"
                                                    data-customer-name="{{ $customer['customer_name'] }}">
                                                    <x-bi en="Delete" ar="حذف" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="loyaltyDeleteModal" tabindex="-1" aria-labelledby="loyaltyDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.loyalty.destroy') }}" method="POST">
                @csrf
                <input type="hidden" name="exclusion_key" value="">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loyaltyDeleteModalLabel"><x-bi en="Delete Eligible Customer" ar="حذف العميل المؤهل" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">
                            <x-bi en="Are you sure you want to remove" ar="هل أنت متأكد أنك تريد إزالة" />
                            <strong data-role="customer-name">-</strong>
                            <x-bi en="from loyalty list?" ar="من قائمة الولاء؟" />
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                        <button type="submit" class="btn btn-danger"><x-bi en="Delete" ar="حذف" /></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="loyaltyDeleteAllModal" tabindex="-1" aria-labelledby="loyaltyDeleteAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.loyalty.destroy-all') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loyaltyDeleteAllModalLabel"><x-bi en="Delete All Eligible Customers" ar="حذف كل العملاء المؤهلين" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0"><x-bi en="Are you sure you want to remove all eligible customers from loyalty list?" ar="هل أنت متأكد أنك تريد إزالة كل العملاء المؤهلين من قائمة الولاء؟" /></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                        <button type="submit" class="btn btn-danger"><x-bi en="Delete All" ar="حذف الكل" /></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('partials.admin.footer')
</div>
@endsection
