@extends('layouts.admin')

@php
    $availableStockItemsForJs = $stockItems->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'unit_label' => $item->unit_label,
        ];
    })->values();

    $menuRecipesForJs = $menus->mapWithKeys(function ($menu) {
        return [
            $menu->id => $menu->stockItems->map(function ($stockItem) {
                return [
                    'stock_item_id' => $stockItem->id,
                    'quantity_required' => (float) $stockItem->pivot->quantity_required,
                ];
            })->values(),
        ];
    });

    $usageCategories = $menus->map(function ($menu) {
        return $menu->category;
    })->filter()->unique('id')->sortBy('name')->values();

    $stockSummary = [
        'total_items' => $stockItems->count(),
        'healthy_items' => $stockItems->filter(fn ($item) => (float) $item->current_quantity > 5)->count(),
        'low_items' => $stockItems->filter(fn ($item) => (float) $item->current_quantity > 0 && (float) $item->current_quantity <= 5)->count(),
        'out_items' => $stockItems->filter(fn ($item) => (float) $item->current_quantity <= 0)->count(),
    ];

    $stockChartItems = $stockItems
        ->sortByDesc(fn ($item) => (float) $item->current_quantity)
        ->take(10)
        ->map(function ($item) {
            $quantity = (float) $item->current_quantity;

            return [
                'label' => $item->name . ' (' . $item->unit_label . ')',
                'quantity' => $quantity,
                'status' => $quantity <= 0 ? 'out' : ($quantity <= 5 ? 'low' : 'ok'),
            ];
        })->values();

    $lowStockReport = $stockItems
        ->filter(fn ($item) => (float) $item->current_quantity <= 5)
        ->sortBy('current_quantity')
        ->values();
@endphp

@push('styles')
    <link rel="stylesheet" href="/admin_resources/vendors/typicons.font/font/typicons.css">
    <link rel="stylesheet" href="/admin_resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/admin_resources/css/vertical-layout-light/style.css">
    <style>
        .stock-summary-badge {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 999px;
        }

        .stock-summary-badge.out {
            background: #f8d7da;
            color: #842029;
        }

        .stock-summary-badge.low {
            background: #fff3cd;
            color: #664d03;
        }

        .recipe-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            margin: 0 6px 6px 0;
            border-radius: 999px;
            background: #eef4ff;
            color: #1f3f73;
            font-size: 12px;
        }

        .recipe-builder-row {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(120px, 0.8fr) auto;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        .stock-help {
            font-size: 12px;
            color: #6c757d;
        }

        .usage-category-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }

        .usage-category-filter {
            border-radius: 999px;
            padding: 8px 14px;
        }

        .usage-category-filter.active {
            background: #1f3f73;
            border-color: #1f3f73;
            color: #fff;
        }

        .usage-empty-state {
            display: none;
        }

        .stock-overview-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .stock-overview-card {
            border: 1px solid #e7ebf3;
            border-radius: 16px;
            padding: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f7f9fc 100%);
        }

        .stock-overview-card h6 {
            margin: 0 0 8px;
            font-size: 13px;
            color: #6c757d;
        }

        .stock-overview-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #1f3f73;
            line-height: 1;
        }

        .stock-overview-card .meta {
            margin-top: 8px;
            font-size: 12px;
            color: #6c757d;
        }

        .stock-chart-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
            gap: 16px;
            margin-bottom: 18px;
        }

        .stock-chart-card {
            border: 1px solid #e7ebf3;
            border-radius: 16px;
            padding: 16px;
            background: #fff;
        }

        .stock-chart-card h5 {
            margin: 0 0 6px;
            font-size: 16px;
        }

        .stock-chart-card p {
            margin: 0 0 14px;
            font-size: 12px;
            color: #6c757d;
        }

        .stock-chart-wrap {
            position: relative;
            min-height: 280px;
        }

        .stock-report-list {
            display: grid;
            gap: 10px;
        }

        .stock-report-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid #edf1f7;
            border-radius: 12px;
            background: #fbfcfe;
        }

        .stock-report-item strong {
            display: block;
            color: #1f3f73;
        }

        .stock-report-item span {
            font-size: 12px;
            color: #6c757d;
        }

        @media (max-width: 767px) {
            .stock-overview-grid,
            .stock-chart-grid {
                grid-template-columns: 1fr;
            }

            .recipe-builder-row {
                grid-template-columns: 1fr;
            }
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
<script src="/admin_resources/vendors/progressbar.js/progressbar.min.js"></script>
<script src="/admin_resources/vendors/chart.js/Chart.min.js"></script>
<script src="/admin_resources/js/dashboard.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script id="available-stock-items-json" type="application/json">{!! json_encode($availableStockItemsForJs) !!}</script>
<script id="menu-recipes-json" type="application/json">{!! json_encode($menuRecipesForJs) !!}</script>
<script id="stock-summary-json" type="application/json">{!! json_encode($stockSummary) !!}</script>
<script id="stock-chart-items-json" type="application/json">{!! json_encode($stockChartItems) !!}</script>
<script>
    const availableStockItems = JSON.parse(document.getElementById('available-stock-items-json').textContent);
    const menuRecipes = JSON.parse(document.getElementById('menu-recipes-json').textContent);
    const stockSummary = JSON.parse(document.getElementById('stock-summary-json').textContent);
    const stockChartItems = JSON.parse(document.getElementById('stock-chart-items-json').textContent);

    function buildIngredientOptions(selectedId) {
        return availableStockItems.map(function (item) {
            const isSelected = Number(selectedId) === Number(item.id) ? 'selected' : '';
            return '<option value="' + item.id + '" ' + isSelected + '>' + item.name + ' (' + item.unit_label + ')</option>';
        }).join('');
    }

    function buildRecipeRow(ingredient) {
        const selectedId = ingredient && ingredient.stock_item_id ? ingredient.stock_item_id : '';
        const quantity = ingredient && ingredient.quantity_required ? ingredient.quantity_required : '';

        return '' +
            '<div class="recipe-builder-row">' +
                '<select data-field="stock_item_id" class="form-control">' +
                    '<option value="">Select stock item</option>' +
                    buildIngredientOptions(selectedId) +
                '</select>' +
                '<input type="number" step="0.001" min="0.001" data-field="quantity_required" class="form-control" placeholder="Qty per menu" value="' + quantity + '">' +
                '<button type="button" class="btn btn-outline-danger remove-ingredient-row"><i class="fa fa-trash"></i></button>' +
            '</div>';
    }

    $(function () {
        $('.edit-stock-btn').on('click', function () {
            $('#editStockName').val($(this).data('name'));
            $('#editStockQuantity').val($(this).data('quantity'));
            $('#editStockUnit').val($(this).data('unit'));
            $('#editStockForm').attr('action', $(this).data('action'));
        });

        $('.delete-stock-btn').on('click', function () {
            $('#deleteStockForm').attr('action', $(this).data('action'));
            $('#deleteStockName').text($(this).data('name'));
        });

        $('.manage-recipe-btn').on('click', function () {
            const actionUrl = $(this).data('action');
            const menuName = $(this).data('menu-name');
            const menuId = String($(this).data('menu-id'));
            const recipe = Array.isArray(menuRecipes[menuId]) ? menuRecipes[menuId] : [];

            const $container = $('#recipeRows');
            $('#recipeForm').attr('action', actionUrl);
            $('#recipeMenuName').text(menuName);
            $container.empty();

            if (recipe.length > 0) {
                recipe.forEach(function (ingredient) {
                    $container.append(buildRecipeRow(ingredient));
                });
            } else {
                $container.append(buildRecipeRow());
            }
        });

        $('#addIngredientRow').on('click', function () {
            $('#recipeRows').append(buildRecipeRow());
        });

        $('.usage-category-filter').on('click', function () {
            const selectedCategory = String($(this).data('categoryFilter'));

            $('.usage-category-filter').removeClass('active btn-primary').addClass('btn-outline-primary');
            $(this).addClass('active btn-primary').removeClass('btn-outline-primary');

            let visibleCount = 0;
            $('#menu-stock-usage-table tbody tr[data-category-id]').each(function () {
                const rowCategory = String($(this).data('categoryId'));
                const shouldShow = selectedCategory === 'all' || rowCategory === selectedCategory;
                $(this).toggle(shouldShow);
                if (shouldShow) {
                    visibleCount += 1;
                }
            });

            $('#menu-stock-usage-empty').toggle(visibleCount === 0);
        });

        $(document).on('click', '.remove-ingredient-row', function () {
            $(this).closest('.recipe-builder-row').remove();

            if ($('#recipeRows .recipe-builder-row').length === 0) {
                $('#recipeRows').append(buildRecipeRow());
            }
        });

        $('#recipeForm').on('submit', function (e) {
            $(this).find('input[name="clear_recipe"]').remove();

            const validRows = [];
            $('#recipeRows .recipe-builder-row').each(function () {
                const stockId = $(this).find('select[data-field="stock_item_id"]').val();
                const quantity = $(this).find('input[data-field="quantity_required"]').val();
                if (stockId && quantity && parseFloat(quantity) > 0) {
                    validRows.push($(this));
                }
            });

            $('#recipeRows .recipe-builder-row').remove();

            if (validRows.length === 0) {
                $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'clear_recipe')
                    .val('1')
                    .appendTo(this);
                return;
            }

            $(this).find('input[name="clear_recipe"]').remove();

            validRows.forEach(function ($row) {
                $('#recipeRows').append($row);
            });

            // Ensure PHP receives correctly paired ingredient objects.
            $('#recipeRows .recipe-builder-row').each(function (index) {
                $(this).find('select[data-field="stock_item_id"]').attr('name', 'ingredients[' + index + '][stock_item_id]');
                $(this).find('input[data-field="quantity_required"]').attr('name', 'ingredients[' + index + '][quantity_required]');
            });
        });

        if (document.getElementById('stockQuantityChart') && stockChartItems.length > 0) {
            window.stockQuantityChartInstance = new Chart(document.getElementById('stockQuantityChart'), {
                type: 'bar',
                data: {
                    labels: stockChartItems.map(function (item) { return item.label; }),
                    datasets: [{
                        label: 'Current stock',
                        data: stockChartItems.map(function (item) { return item.quantity; }),
                        backgroundColor: stockChartItems.map(function (item) {
                            if (item.status === 'out') return '#dc3545';
                            if (item.status === 'low') return '#ffc107';
                            return '#1f7a5c';
                        }),
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        if (document.getElementById('stockStatusChart')) {
            window.stockStatusChartInstance = new Chart(document.getElementById('stockStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Healthy', 'Low', 'Out'],
                    datasets: [{
                        data: [stockSummary.healthy_items, stockSummary.low_items, stockSummary.out_items],
                        backgroundColor: ['#1f7a5c', '#ffc107', '#dc3545'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    cutout: '68%'
                }
            });
        }

        // Real-time stock updates
        function formatNumber(num) {
            if (Number.isInteger(num)) return num.toString();
            const str = num.toFixed(3);
            return str.replace(/\.?0+$/, '');
        }

        function updateStockData(data) {
            // Update summary cards
            const summaryCards = document.querySelectorAll('.stock-overview-card .value');
            if (summaryCards.length >= 4) {
                summaryCards[0].textContent = data.stockSummary.total_items;
                summaryCards[1].textContent = data.stockSummary.healthy_items;
                summaryCards[2].textContent = data.stockSummary.low_items;
                summaryCards[3].textContent = data.stockSummary.out_items;
            }

            // Update quantity bar chart
            if (window.stockQuantityChartInstance) {
                window.stockQuantityChartInstance.data.labels = data.stockChartItems.map(item => item.label);
                window.stockQuantityChartInstance.data.datasets[0].data = data.stockChartItems.map(item => item.quantity);
                window.stockQuantityChartInstance.data.datasets[0].backgroundColor = data.stockChartItems.map(item => {
                    if (item.status === 'out') return '#dc3545';
                    if (item.status === 'low') return '#ffc107';
                    return '#1f7a5c';
                });
                window.stockQuantityChartInstance.update();
            }

            // Update status doughnut chart
            if (window.stockStatusChartInstance) {
                window.stockStatusChartInstance.data.datasets[0].data = [
                    data.stockSummary.healthy_items, 
                    data.stockSummary.low_items, 
                    data.stockSummary.out_items
                ];
                window.stockStatusChartInstance.update();
            }

            // Update low stock report
            const reportList = document.querySelector('.stock-report-list');
            if (reportList && data.lowStockReport.length > 0) {
                reportList.innerHTML = '';
                data.lowStockReport.forEach(item => {
                    const quantity = parseFloat(item.current_quantity);
                    const badges = quantity <= 0 
                        ? '<span class="stock-summary-badge out">Out of stock</span>'
                        : '<span class="stock-summary-badge low">Low stock</span>';
                    
                    const newItem = document.createElement('div');
                    newItem.className = 'stock-report-item';
                    newItem.innerHTML = `
                        <div>
                            <strong>${item.name}</strong>
                            <span>${item.unit_label}</span>
                        </div>
                        <div>
                            ${badges}
                            <div class="mt-1 text-end">${formatNumber(quantity)}</div>
                        </div>
                    `;
                    reportList.appendChild(newItem);
                });
            } else if (reportList && data.lowStockReport.length === 0) {
                reportList.innerHTML = `
                    <div class="stock-report-item">
                        <div>
                            <strong>All stock items are healthy</strong>
                            <span>No immediate replenishment required</span>
                        </div>
                    </div>
                `;
            }

            // Update main stock table
            const tableBody = document.querySelector('table.table tbody');
            if (tableBody && data.stockTable.length > 0) {
                const rows = tableBody.querySelectorAll('tr');
                data.stockTable.forEach((item, index) => {
                    if (rows[index] && rows[index].querySelectorAll('td').length > 1) {
                        const cells = rows[index].querySelectorAll('td');
                        if (cells.length >= 5) {
                            cells[1].textContent = formatNumber(item.current_quantity);
                            cells[3].textContent = item.menus_count;
                            
                            // Update status badge
                            let badgeHTML;
                            if (item.status === 'out') {
                                badgeHTML = '<span class="stock-summary-badge out">Out of stock</span>';
                            } else if (item.status === 'low') {
                                badgeHTML = '<span class="stock-summary-badge low">Low stock</span>';
                            } else {
                                badgeHTML = '<span class="badge bg-success">Healthy</span>';
                            }
                            cells[4].innerHTML = badgeHTML;
                        }
                    }
                });
            }
        }

        // Poll stock data every 5 seconds
        setInterval(function () {
            $.ajax({
                url: '{{ route("admin.stocks.data") }}',
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    updateStockData(data);
                },
                error: function (error) {
                    console.error('Error fetching stock data:', error);
                }
            });
        }, 5000); // 5 seconds
    });
</script>
@endpush

@section('title', 'Admin - Manage Stock')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        @include('partials.message-bag')

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><x-bi en="Manage Stock" ar="إدارة المخزون" /></span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStockModal">
                    <x-bi en="Add Stock" ar="إضافة مخزون" />
                </button>
            </div>
            <div class="card-body">
                <div class="stock-overview-grid">
                    <div class="stock-overview-card">
                        <h6><x-bi en="Total Stock Items" ar="إجمالي عناصر المخزون" /></h6>
                        <div class="value">{{ $stockSummary['total_items'] }}</div>
                        <div class="meta"><x-bi en="Tracked ingredients and supplies" ar="المكونات والمواد التي يتم تتبعها" /></div>
                    </div>
                    <div class="stock-overview-card">
                        <h6><x-bi en="Healthy Stock" ar="مخزون جيد" /></h6>
                        <div class="value">{{ $stockSummary['healthy_items'] }}</div>
                        <div class="meta"><x-bi en="Items above low-stock threshold" ar="العناصر فوق حد التحذير" /></div>
                    </div>
                    <div class="stock-overview-card">
                        <h6><x-bi en="Low Stock" ar="مخزون منخفض" /></h6>
                        <div class="value">{{ $stockSummary['low_items'] }}</div>
                        <div class="meta"><x-bi en="Items at 5 units or below" ar="العناصر عند 5 وحدات أو أقل" /></div>
                    </div>
                    <div class="stock-overview-card">
                        <h6><x-bi en="Out of Stock" ar="نفد المخزون" /></h6>
                        <div class="value">{{ $stockSummary['out_items'] }}</div>
                        <div class="meta"><x-bi en="Needs replenishment before next order" ar="تحتاج لإعادة تعبئة قبل الطلب التالي" /></div>
                    </div>
                </div>

                <div class="stock-chart-grid">
                    <div class="stock-chart-card">
                        <h5><x-bi en="Current Stock Levels" ar="مستويات المخزون الحالية" /></h5>
                        <p><x-bi en="Top stock items by available quantity. Mixed units are shown in each label." ar="أعلى عناصر المخزون حسب الكمية المتاحة. يتم إظهار الوحدة داخل اسم كل عنصر." /></p>
                        <div class="stock-chart-wrap">
                            <canvas id="stockQuantityChart"></canvas>
                        </div>
                    </div>

                    <div class="stock-chart-card">
                        <h5><x-bi en="Stock Status Split" ar="توزيع حالة المخزون" /></h5>
                        <p><x-bi en="A quick view of items that are healthy, low, or already out of stock." ar="عرض سريع للعناصر الجيدة والمنخفضة والتي نفد مخزونها." /></p>
                        <div class="stock-chart-wrap" style="min-height: 240px;">
                            <canvas id="stockStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="stock-chart-card mb-4">
                    <h5><x-bi en="Replenishment Report" ar="تقرير إعادة التزويد" /></h5>
                    <p><x-bi en="These items need attention because customer orders can continue reducing stock automatically." ar="هذه العناصر تحتاج متابعة لأن طلبات العملاء ستستمر في تقليل المخزون تلقائياً." /></p>
                    <div class="stock-report-list">
                        @forelse($lowStockReport as $reportItem)
                            @php
                                $reportQuantity = (float) $reportItem->current_quantity;
                            @endphp
                            <div class="stock-report-item">
                                <div>
                                    <strong>{{ $reportItem->name }}</strong>
                                    <span>{{ $reportItem->unit_label }}</span>
                                </div>
                                <div>
                                    @if($reportQuantity <= 0)
                                        <span class="stock-summary-badge out"><x-bi en="Out of stock" ar="نفد المخزون" /></span>
                                    @else
                                        <span class="stock-summary-badge low"><x-bi en="Low stock" ar="مخزون منخفض" /></span>
                                    @endif
                                    <div class="mt-1 text-end">{{ rtrim(rtrim(number_format($reportQuantity, 3, '.', ''), '0'), '.') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="stock-report-item">
                                <div>
                                    <strong><x-bi en="All stock items are healthy" ar="جميع عناصر المخزون بحالة جيدة" /></strong>
                                    <span><x-bi en="No immediate replenishment required" ar="لا توجد حاجة عاجلة لإعادة التزويد" /></span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th><x-bi en="Stock Name" ar="اسم المخزون" /></th>
                                <th><x-bi en="Available Qty" ar="الكمية المتاحة" /></th>
                                <th><x-bi en="Unit" ar="الوحدة" /></th>
                                <th><x-bi en="Used In Menus" ar="يستخدم في العناصر" /></th>
                                <th><x-bi en="Status" ar="الحالة" /></th>
                                <th><x-bi en="Actions" ar="الإجراءات" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockItems as $stockItem)
                                @php
                                    $stockValue = (float) $stockItem->current_quantity;
                                    $stockStatus = $stockValue <= 0 ? 'out' : ($stockValue <= 5 ? 'low' : 'ok');
                                @endphp
                                <tr>
                                    <td>{{ $stockItem->name }}</td>
                                    <td>{{ rtrim(rtrim(number_format($stockValue, 3, '.', ''), '0'), '.') }}</td>
                                    <td>{{ $stockItem->unit_label }}</td>
                                    <td>{{ $stockItem->menus_count }}</td>
                                    <td>
                                        @if($stockStatus === 'out')
                                            <span class="stock-summary-badge out">Out of stock</span>
                                        @elseif($stockStatus === 'low')
                                            <span class="stock-summary-badge low">Low stock</span>
                                        @else
                                            <span class="badge bg-success">Healthy</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm edit-stock-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editStockModal"
                                                data-name="{{ $stockItem->name }}"
                                                data-quantity="{{ $stockItem->current_quantity }}"
                                                data-unit="{{ $stockItem->unit }}"
                                                data-action="{{ route('admin.stocks.update', $stockItem) }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-stock-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteStockModal"
                                                data-name="{{ $stockItem->name }}"
                                                data-action="{{ route('admin.stocks.destroy', $stockItem) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center"><x-bi en="No stock items yet." ar="لا توجد عناصر مخزون بعد." /></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><x-bi en="Menu Stock Usage" ar="استهلاك المخزون لكل عنصر" /></span>
                <span class="stock-help">Set how much stock is consumed every time one menu item is ordered.</span>
            </div>
            <div class="card-body">
                <div class="usage-category-filters">
                    <button type="button" class="btn btn-primary usage-category-filter active" data-category-filter="all">
                        <x-bi en="All Categories" ar="كل الفئات" />
                    </button>
                    @foreach($usageCategories as $category)
                        <button type="button" class="btn btn-outline-primary usage-category-filter" data-category-filter="{{ $category->id }}">
                            <x-bi :en="$category->name" :ar="$category->name_ar ?: $category->name" />
                        </button>
                    @endforeach
                </div>

                <div class="table-responsive">
                    <table id="menu-stock-usage-table" class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th><x-bi en="Menu" ar="العنصر" /></th>
                                <th><x-bi en="Category" ar="الفئة" /></th>
                                <th><x-bi en="Stock Recipe" ar="وصفة المخزون" /></th>
                                <th><x-bi en="Action" ar="الإجراء" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menus as $menu)
                                <tr data-category-id="{{ $menu->category?->id ?? 'uncategorized' }}">
                                    <td>
                                        <x-bi :en="$menu->name" :ar="$menu->name_ar ?: '-'" />
                                    </td>
                                    <td>{{ $menu->category?->name ?? '-' }}</td>
                                    <td>
                                        @forelse($menu->stockItems as $stockItem)
                                            <span class="recipe-chip">
                                                {{ $stockItem->name }}: {{ rtrim(rtrim(number_format((float) $stockItem->pivot->quantity_required, 3, '.', ''), '0'), '.') }} {{ $stockItem->unit_label }}
                                            </span>
                                        @empty
                                            <span class="text-muted">No stock recipe yet</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <button type="button"
                                                class="btn btn-outline-primary btn-sm manage-recipe-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#recipeModal"
                                                data-menu-id="{{ $menu->id }}"
                                                data-menu-name="{{ $menu->name }}"
                                                data-action="{{ route('admin.stocks.recipes.sync', $menu) }}">
                                            <i class="fa fa-sliders"></i>
                                            <x-bi en="Manage Usage" ar="إدارة الاستهلاك" />
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            <tr id="menu-stock-usage-empty" class="usage-empty-state">
                                <td colspan="4" class="text-center">
                                    <x-bi en="No menus found in this category." ar="لا توجد عناصر في هذه الفئة." />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.stocks.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><x-bi en="Add Stock Item" ar="إضافة عنصر مخزون" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label><x-bi en="Stock Name" ar="اسم المخزون" /></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label><x-bi en="Quantity" ar="الكمية" /></label>
                            <input type="number" step="0.001" min="0" name="current_quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label><x-bi en="Unit" ar="الوحدة" /></label>
                            <select name="unit" class="form-control" required>
                                <option value="">Select unit</option>
                                @foreach($unitOptions as $unitValue => $unitLabel)
                                    <option value="{{ $unitValue }}">{{ $unitLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editStockForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><x-bi en="Edit Stock Item" ar="تعديل عنصر المخزون" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label><x-bi en="Stock Name" ar="اسم المخزون" /></label>
                            <input type="text" id="editStockName" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label><x-bi en="Quantity" ar="الكمية" /></label>
                            <input type="number" step="0.001" min="0" id="editStockQuantity" name="current_quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label><x-bi en="Unit" ar="الوحدة" /></label>
                            <select id="editStockUnit" name="unit" class="form-control" required>
                                @foreach($unitOptions as $unitValue => $unitLabel)
                                    <option value="{{ $unitValue }}">{{ $unitLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteStockForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title"><x-bi en="Delete Stock Item" ar="حذف عنصر المخزون" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Delete <strong id="deleteStockName"></strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="recipeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="recipeForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><x-bi en="Manage Stock Usage" ar="إدارة استهلاك المخزون" />: <span id="recipeMenuName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="stock-help mb-3">Use the same unit as the stock item. Example: if coffee stock uses kilogram, then 0.02 means 20 gram per cup.</p>
                        <div id="recipeRows"></div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="addIngredientRow">
                            <i class="fa fa-plus"></i>
                            <x-bi en="Add Ingredient" ar="إضافة مكون" />
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Usage</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('partials.admin.footer')
</div>
@endsection
