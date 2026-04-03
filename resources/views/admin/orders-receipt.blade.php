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
            padding: 16px;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            font-size: 14px;
            color: var(--text-color);
            background: #f1f1f1;
        }

        .receipt {
            width: 100%;
            max-width: var(--paper-width);
            margin: 0 auto;
            background: #fff;
            padding: var(--paper-padding);
            border: 1px solid #ddd;
        }

        .center {
            text-align: center;
        }

        .logo {
            width: 96px;
            height: auto;
            margin-bottom: 6px;
        }

        .name {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.6px;
        }

        .name-sub {
            margin: 2px 0 8px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .divider {
            border-top: 1px dashed var(--line);
            margin: 8px 0;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .meta td {
            padding: 3px 0;
            vertical-align: top;
        }

        .meta td:nth-child(2) {
            text-align: right;
            font-weight: 700;
        }

        .meta .ar {
            display: block;
            direction: rtl;
            font-size: 12px;
            color: var(--muted);
        }

        .section-title {
            font-size: 14px;
            font-weight: 800;
            text-align: center;
            margin: 6px 0;
        }

        .section-title .ar {
            display: block;
            direction: rtl;
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 14px;
        }

        .items th,
        .items td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        .items th {
            font-size: 13px;
            text-align: center;
        }

        .items td.num {
            text-align: center;
            white-space: nowrap;
        }

        .item-name-en {
            font-weight: 700;
        }

        .item-name-ar {
            direction: rtl;
            font-size: 12px;
            color: var(--muted);
            margin-top: 1px;
        }

        .total {
            margin-top: 6px;
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 18px;
            font-weight: 800;
        }

        .total .ar {
            direction: rtl;
            font-size: 14px;
            color: var(--muted);
            font-weight: 700;
        }

        .small {
            font-size: 13px;
            color: var(--muted);
        }

        .phones {
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            margin-top: 5px;
            line-height: 1.4;
            word-break: break-word;
        }

        .thanks {
            margin-top: 8px;
            text-align: center;
            font-size: 15px;
            font-weight: 800;
        }

        .thanks .ar {
            direction: rtl;
            font-size: 13px;
            margin-top: 2px;
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
            }

            .receipt {
                border: none;
                max-width: none;
                width: 100%;
                margin: 0;
                padding: 8mm;
            }

            .logo {
                width: 44mm;
            }

            .name {
                font-size: 58px;
            }

            .name-sub {
                font-size: 34px;
            }

            .divider {
                margin: 16px 0;
            }

            .meta,
            .section-title,
            .items {
                font-size: 27px;
            }

            .meta .ar,
            .section-title .ar,
            .item-name-ar,
            .small,
            .total .ar,
            .thanks .ar {
                font-size: 22px;
            }

            .items th {
                font-size: 24px;
            }

            .items th,
            .items td {
                padding: 12px;
            }

            .total {
                font-size: 46px;
            }

            .phones,
            .thanks {
                font-size: 34px;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="center">
            <img class="logo" src="{{ $receiptData['logo_url'] }}" alt="Restaurant Logo">
            <h1 class="name">{{ $receiptData['restaurant_name'] }}</h1>
            <div class="name-sub">CAFE</div>
        </div>

        <div class="divider"></div>

        <table class="meta">
            <tr>
                <td>
                    Order No
                    <span class="ar">رقم الطلب</span>
                </td>
                <td>#{{ $order->order_sequence ?? $order->id }}</td>
            </tr>
            <tr>
                <td>
                    Date
                    <span class="ar">التاريخ</span>
                </td>
                <td>{{ $order->created_at->format('Y/m/d') }}</td>
            </tr>
            <tr>
                <td>
                    Time
                    <span class="ar">الوقت</span>
                </td>
                <td>{{ $order->created_at->format('H:i:s') }}</td>
            </tr>
            <tr>
                <td>
                    Cashier
                    <span class="ar">الكاشير</span>
                </td>
                <td>{{ $receiptData['cashier_name'] }}</td>
            </tr>
            <tr>
                <td>
                    Table No
                    <span class="ar">الترابيزة</span>
                </td>
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
                    <td>
                        Name
                        <span class="ar">الاسم</span>
                    </td>
                    <td>{{ $order->online_customer_name ?: ($order->customer?->first_name ?? '-') }}</td>
                </tr>
                <tr>
                    <td>
                        Phone
                        <span class="ar">رقم الهاتف</span>
                    </td>
                    <td>{{ $order->online_customer_phone ?: ($order->customer?->phone_number ?? '-') }}</td>
                </tr>
                <tr>
                    <td>
                        Address
                        <span class="ar">العنوان</span>
                    </td>
                    <td>{{ $order->online_delivery_address ?: ($order->deliveryAddressWithTrashed?->full_address ?? '-') }}</td>
                </tr>
                <tr>
                    <td>
                        Payment
                        <span class="ar">الدفع</span>
                    </td>
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
                    <th style="width: 48%;">Item<br><span dir="rtl">الصنف</span></th>
                    <th style="width: 14%;">Qty<br><span dir="rtl">الكمية</span></th>
                    <th style="width: 19%;">Price<br><span dir="rtl">السعر</span></th>
                    <th style="width: 19%;">Total<br><span dir="rtl">القيمة</span></th>
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
                            <div class="item-name-en">{{ $item->menu_name_en ?? $item->menu_name }}</div>
                            @if(!empty($item->menu_name_ar) && $item->menu_name_ar !== ($item->menu_name_en ?? $item->menu_name))
                                <div class="item-name-ar">{{ $item->menu_name_ar }}</div>
                            @endif
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

        <div class="small center">
            Contact Us
            <div dir="rtl">تواصل معنا</div>
        </div>

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

        <div class="thanks">
            {{ $receiptData['thank_you_message'] }}
            <div class="ar">شكرا لزيارتكم</div>
        </div>
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
