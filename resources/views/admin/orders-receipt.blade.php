<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $order->order_sequence ?? $order->id }}</title>
    <style>
        :root {
            --paper-width: 80mm;
            --paper-padding: 3mm;
            --text-color: #111;
            --muted: #555;
            --line: #333;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 12px;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            font-size: 10px;
            color: var(--text-color);
            background: #f1f1f1;
            line-height: 1.2;
        }

        .receipt {
            width: 100%;
            max-width: var(--paper-width);
            margin: 0 auto;
            background: #fff;
            padding: var(--paper-padding);
            border: 1px solid #ddd;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 2px 0;
        }

        .logo {
            width: 44px;
            height: auto;
            flex-shrink: 0;
        }

        .header-text {
            flex: 0 0 auto;
        }

        .name {
            margin: 0;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.4px;
            line-height: 1.1;
        }

        .name-sub {
            margin: 1px 0 0;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            color: var(--muted);
        }

        .center {
            text-align: center;
        }

        .divider {
            border-top: 1px dashed var(--line);
            margin: 3px 0;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .meta td {
            padding: 0;
            vertical-align: middle;
            line-height: 1.3;
        }

        .meta td:first-child {
            width: 34%;
            text-align: left;
        }

        .meta td:nth-child(2) {
            width: 28%;
            text-align: center;
        }

        .meta td:nth-child(3) {
            width: 38%;
            text-align: right;
            font-weight: 700;
        }

        .meta .meta-ar {
            direction: rtl;
            font-size: 8px;
            color: var(--muted);
            text-align: center;
            line-height: 1;
        }

        .section-title {
            font-size: 9px;
            font-weight: 800;
            text-align: center;
            margin: 2px 0;
            line-height: 1.2;
        }

        .section-title .ar {
            display: block;
            direction: rtl;
            font-size: 8px;
            color: var(--muted);
            text-align: center;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
            font-size: 9px;
        }

        .items th,
        .items td {
            border: 1px solid #000;
            padding: 2px 3px;
            vertical-align: top;
        }

        .items th {
            font-size: 8px;
            text-align: center;
            line-height: 1.2;
        }

        .items td.num {
            text-align: center;
            white-space: nowrap;
        }

        .item-name-en {
            font-weight: 700;
        }

        .item-name-main {
            direction: rtl;
            font-weight: 700;
            text-align: center;
            line-height: 1.1;
        }

        .item-name-ar {
            direction: rtl;
            font-size: 8px;
            color: var(--muted);
            margin-top: 0;
            line-height: 1.1;
            text-align: center;
        }

        .total {
            margin-top: 3px;
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 9px;
            font-weight: 800;
            line-height: 1.2;
        }

        .total .ar {
            direction: rtl;
            font-size: 7px;
            color: var(--muted);
            font-weight: 700;
            text-align: center;
        }

        .small {
            font-size: 8px;
            color: var(--muted);
            text-align: center;
            line-height: 1.2;
        }

        .phones {
            text-align: center;
            font-size: 6px;
            font-weight: 700;
            margin-top: 1px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
        }

        .thanks {
            margin-top: 2px;
            text-align: center;
            font-size: 8px;
            font-weight: 800;
            line-height: 1.2;
        }

        .items th,
        .small[dir="rtl"],
        .thanks[dir="rtl"] {
            direction: rtl;
            text-align: center;
        }

        .items th span[dir="rtl"] {
            display: block;
            text-align: center;
            line-height: 1;
        }

        .actions {
            max-width: var(--paper-width);
            margin: 10px auto 0;
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .actions button,
        .actions a {
            border: 1px solid #222;
            background: #fff;
            color: #111;
            padding: 6px 10px;
            font-size: 12px;
            text-decoration: none;
            cursor: pointer;
        }

        /* Samakan ukuran seluruh teks nota seperti ukuran nomor telepon */
        .receipt,
        .receipt * {
            font-size: 6px !important;
            line-height: 1.2;
        }

        .receipt .name {
            font-size: 14px !important;
            line-height: 1.1;
        }

        .receipt .name-sub {
            font-size: 11px !important;
            line-height: 1.1;
        }

        @media print {
            @page {
                size: auto;
                margin: 0;
            }

            body {
                background: #fff;
                padding: 0;
                width: 100%;
                margin: 0;
                font-size: 16px;
                line-height: 1.15;
            }

            .receipt {
                border: none;
                max-width: none;
                width: 100%;
                margin: 0;
                padding: 3mm;
            }

            .header {
                justify-content: center;
                gap: 8px;
            }

            .logo {
                width: 18mm;
            }

            .name {
                font-size: 19px;
            }

            .name-sub {
                font-size: 13px;
            }

            .divider {
                margin: 4px 0;
            }

            .meta,
            .section-title,
            .items {
                font-size: 15px;
            }

            .meta td {
                padding: 0;
            }

            .meta td:first-child {
                text-align: left;
            }

            .meta .ar,
            .section-title .ar,
            .item-name-ar,
            .small,
            .total .ar,
            .thanks .ar {
                font-size: 12px;
            }

            .section-title {
                margin: 2px 0;
            }

            .items th {
                font-size: 13px;
            }

            .items th,
            .items td {
                padding: 4px 5px;
            }

            .total {
                font-size: 15px;
                margin-top: 3px;
            }

            .phones {
                font-size: 9px;
                margin-top: 2px;
                white-space: nowrap;
            }

            .thanks {
                font-size: 12px;
                margin-top: 2px;
            }

            .actions {
                display: none;
            }

            /* Saat print, samakan ukuran seluruh teks nota seperti nomor telepon print */
            .receipt,
            .receipt * {
                font-size: 9px !important;
                line-height: 1.15;
            }

            .receipt .name {
                font-size: 20px !important;
                line-height: 1.1;
            }

            .receipt .name-sub {
                font-size: 14px !important;
                line-height: 1.1;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <img class="logo" src="{{ $receiptData['logo_url'] }}" alt="Restaurant Logo">
            <div class="header-text">
                <h1 class="name">{{ $receiptData['restaurant_name'] }}</h1>
                <div class="name-sub">CAFE</div>
            </div>
        </div>

        <div class="divider"></div>

        <table class="meta">
            <tr>
                <td>Order No</td>
                <td class="meta-ar">رقم الطلب</td>
                <td>#{{ $order->order_sequence ?? $order->id }}</td>
            </tr>
            <tr>
                <td>Date</td>
                <td class="meta-ar">التاريخ</td>
                <td>{{ $order->created_at->format('Y/m/d') }}</td>
            </tr>
            <tr>
                <td>Time</td>
                <td class="meta-ar">الوقت</td>
                <td>{{ $order->created_at->format('H:i:s') }}</td>
            </tr>
            <tr>
                <td>Cashier</td>
                <td class="meta-ar">الكاشير</td>
                <td>{{ $receiptData['cashier_name'] }}</td>
            </tr>
            <tr>
                <td>Table No</td>
                <td class="meta-ar">الترابيزة</td>
                <td>{{ $receiptData['table_number'] ?: '-' }}</td>
            </tr>
        </table>

        @if($order->order_type === 'delivery')
            <div class="divider"></div>

            <div class="section-title">
                Customer Details
                <span class="ar">بيانات العميل</span>
            </div>

            <table class="meta">
                <tr>
                    <td>Name</td>
                    <td class="meta-ar">الاسم</td>
                    <td>{{ $order->online_customer_name ?: ($order->customer?->first_name ?? '-') }}</td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td class="meta-ar">رقم الهاتف</td>
                    <td>{{ $order->online_customer_phone ?: ($order->customer?->phone_number ?? '-') }}</td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td class="meta-ar">العنوان</td>
                    <td>{{ $order->online_delivery_address ?: ($order->deliveryAddressWithTrashed?->full_address ?? '-') }}</td>
                </tr>
                <tr>
                    <td>Payment</td>
                    <td class="meta-ar">الدفع</td>
                    <td>
                        @php
                            $paymentLabel = strtoupper(str_replace('_', ' ', (string) $order->payment_method));
                            if (strtolower((string) $order->payment_method) === 'cod') {
                                $paymentLabel = 'CASH';
                            }
                        @endphp
                        {{ $paymentLabel ?: '-' }}
                    </td>
                </tr>
            </table>
        @endif

        <div class="divider"></div>

        <table class="items">
            <thead>
                <tr>
                    <th style="width: 48%;">الصنف</th>
                    <th style="width: 14%;">الكمية</th>
                    <th style="width: 19%;">السعر</th>
                    <th style="width: 19%;">القيمة</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                    @php
                        $qty = max(1, (int) $item->quantity);
                        $unitPrice = (float) $item->subtotal / $qty;
                    @endphp
                    <tr>
                        <td>
                            <div class="item-name-main">{{ $item->menu_name_ar ?: ($item->menu_name_en ?? $item->menu_name) }}</div>
                        </td>
                        <td class="num">{{ $qty }}</td>
                        <td class="num">{{ number_format($unitPrice, 2) }}</td>
                        <td class="num">{{ number_format((float) $item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            <div>
                TOTAL
                <div class="ar">الإجمالي</div>
            </div>
            <div>{{ number_format((float) $order->total_price, 2) }} {{ $receiptData['currency_symbol'] }}</div>
        </div>

        <div class="divider"></div>

        <div class="small center" dir="rtl">تواصل معنا</div>

        <div class="phones">
            @if(!empty($receiptData['phone_numbers']))
                {{ implode(' - ', $receiptData['phone_numbers']) }}
            @else
                {{ config('site.phone') }}
            @endif
        </div>

        <div class="small center" style="margin-top: 6px;">
            {{ $receiptData['full_address'] }}
        </div>

        <div class="thanks" dir="rtl">شكرا لزيارتكم</div>
    </div>

    <div class="actions">
        <button type="button" onclick="window.print()">Print / Save PDF</button>
        <a href="{{ route('admin.order.show', $order->id) }}">Back</a>
    </div>

    @if(request()->boolean('autoprint'))
    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
    @endif
</body>
</html>
