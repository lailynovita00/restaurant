
@extends('layouts.admin')

@push('styles')
    <!-- base:css -->
    <link rel="stylesheet" href="/admin_resources/vendors/typicons.font/font/typicons.css">
    <link rel="stylesheet" href="/admin_resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/admin_resources/css/vertical-layout-light/style.css">
  
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="/admin_resources/css/small-box.css">
    <style>
      .sales-chart-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
      }

      .sales-chart-toolbar .form-control {
        min-width: 130px;
      }

      .sales-chart-meta {
        font-size: 0.85rem;
        color: #6c757d;
      }

      .sales-chart-canvas-wrap {
        position: relative;
        min-height: 360px;
      }

      .report-chart-wrap {
        position: relative;
        min-height: 360px;
      }

      .report-menu-name {
        display: inline-flex;
        flex-direction: column;
        line-height: 1.15;
      }

      .report-menu-name-ar {
        font-size: 0.78em;
        opacity: 0.85;
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 
<script id="sales-chart-config" type="application/json">{!! json_encode(['dataUrl' => route('admin.dashboard.sales-data'), 'initialPayload' => $salesChartPayload, 'isAdmin' => $loggedInUser->role === 'global_admin']) !!}</script>
<script id="sold-items-chart-config" type="application/json">{!! json_encode($soldItemsReport['chart']) !!}</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
  var salesChartConfig = JSON.parse(document.getElementById('sales-chart-config').textContent);
  var soldItemsChartConfig = JSON.parse(document.getElementById('sold-items-chart-config').textContent);
  var chartDataUrl = salesChartConfig.dataUrl;
  var initialChartPayload = salesChartConfig.initialPayload;
    var isAdmin = salesChartConfig.isAdmin;
    var autoRefreshIntervalMs = 3 * 60 * 1000;
    var chartYearSelect = document.getElementById('sales-chart-year-select');
    var chartYearLabel = document.getElementById('sales-chart-year');
    var chartUpdatedAt = document.getElementById('sales-chart-updated-at');
    var downloadButton = document.getElementById('downloadSalesChart');
    var refreshIndicator = document.getElementById('sales-chart-refresh-indicator');
    var ctx = document.getElementById('salesBarChart').getContext('2d');
    var soldItemsCanvas = document.getElementById('soldItemsBarChart');
    var activeYear = initialChartPayload.year;
    var isFetchingChart = false;

    var chartColors = {
      backgroundColor: [
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 99, 132, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 99, 132, 0.2)',
        'rgba(255, 159, 64, 0.2)'
      ],
      borderColor: [
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(255, 159, 64, 1)'
      ]
    };

    function syncYearOptions(years, selectedYear) {
      if (!chartYearSelect) {
        return;
      }

      var nextOptions = (years || []).map(function(year) {
        return '<option value="' + year + '"' + (Number(year) === Number(selectedYear) ? ' selected' : '') + '>' + year + '</option>';
      }).join('');

      if (chartYearSelect.innerHTML !== nextOptions) {
        chartYearSelect.innerHTML = nextOptions;
      }

      chartYearSelect.value = String(selectedYear);
    }

    function setRefreshState(message) {
      refreshIndicator.textContent = message;
    }

    function applyChartPayload(payload) {
      activeYear = Number(payload.year);
      chartYearLabel.textContent = payload.year;
      chartUpdatedAt.textContent = payload.updated_at;
      syncYearOptions(payload.available_years, payload.year);

      salesBarChart.data.labels = payload.labels;
      salesBarChart.data.datasets[0].data = payload.data;
      salesBarChart.update();
    }

    async function fetchChartData(year, reason) {
      if (!chartYearSelect || !downloadButton) {
        return;
      }

      if (isFetchingChart) {
        return;
      }

      isFetchingChart = true;
      chartYearSelect.disabled = true;
      downloadButton.disabled = true;
      setRefreshState(reason || 'Refreshing chart data...');

      try {
        var response = await fetch(chartDataUrl + '?year=' + encodeURIComponent(year), {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (!response.ok) {
          throw new Error('Failed to refresh chart data.');
        }

        var payload = await response.json();
        applyChartPayload(payload);
        setRefreshState('Auto-refresh every 3 minutes.');
      } catch (error) {
        console.error(error);
        setRefreshState('Unable to refresh chart data right now.');
      } finally {
        isFetchingChart = false;
        chartYearSelect.disabled = false;
        downloadButton.disabled = false;
      }
    }

    var salesBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
        labels: initialChartPayload.labels,
                datasets: [{
          label: 'Monthly Sales | المبيعات الشهرية',
          data: initialChartPayload.data,
          backgroundColor: chartColors.backgroundColor,
          borderColor: chartColors.borderColor,
                    borderWidth: 1
                }]
            },
            options: {
        responsive: true,
        maintainAspectRatio: false,
                scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true,
              precision: 0
            }
          }]
                }
            }
        });

    syncYearOptions(initialChartPayload.available_years, initialChartPayload.year);
    setRefreshState('Auto-refresh every 3 minutes.');

    if (chartYearSelect && downloadButton) {
      chartYearSelect.addEventListener('change', function() {
        fetchChartData(chartYearSelect.value, 'Loading selected year...');
      });

      downloadButton.addEventListener('click', function() {
        var sourceCanvas = salesBarChart.canvas;
        var exportTitle = 'Sales Bar Chart - ' + activeYear;
        var headerHeight = 56;
        var exportCanvas = document.createElement('canvas');
        exportCanvas.width = sourceCanvas.width;
        exportCanvas.height = sourceCanvas.height + headerHeight;

        var exportCtx = exportCanvas.getContext('2d');
        exportCtx.fillStyle = '#ffffff';
        exportCtx.fillRect(0, 0, exportCanvas.width, exportCanvas.height);

        exportCtx.fillStyle = '#50301c';
        exportCtx.font = 'bold 24px Roboto, Arial, sans-serif';
        exportCtx.textAlign = 'center';
        exportCtx.textBaseline = 'middle';
        exportCtx.fillText(exportTitle, exportCanvas.width / 2, headerHeight / 2);

        exportCtx.drawImage(sourceCanvas, 0, headerHeight);

        var link = document.createElement('a');
        link.href = exportCanvas.toDataURL('image/png');
        link.download = 'sales-bar-chart-' + activeYear + '.png';
        link.click();
      });

      window.setInterval(function() {
        fetchChartData(chartYearSelect.value, 'Refreshing latest chart data...');
      }, autoRefreshIntervalMs);

      document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
          fetchChartData(chartYearSelect.value, 'Syncing latest chart data...');
        }
      });
    }

    if (soldItemsCanvas && soldItemsChartConfig.labels && soldItemsChartConfig.labels.length) {
      new Chart(soldItemsCanvas.getContext('2d'), {
        type: 'horizontalBar',
        data: {
          labels: soldItemsChartConfig.labels,
          datasets: [{
            label: 'Qty Sold | الكمية المباعة',
            data: soldItemsChartConfig.data,
            backgroundColor: 'rgba(80, 48, 28, 0.18)',
            borderColor: 'rgba(80, 48, 28, 0.9)',
            borderWidth: 1.5
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: true
          },
          scales: {
            xAxes: [{
              ticks: {
                beginAtZero: true,
                precision: 0
              }
            }],
            yAxes: [{
              ticks: {
                fontSize: 11
              }
            }]
          }
        }
      });
    }
    });
</script>

@endpush


@section('title', 'Admin - Dashboard')




@section('content')

<div class="main-panel">
    <div class="content-wrapper">
      @include('partials.message-bag')

    

      @include('partials.order-stats')


      <div class="row">
        <div class="col-lg-12 d-flex grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap: 12px;">
                <h4 class="card-title mb-0"><x-bi en="Sales Bar Chart" ar="مخطط المبيعات" /> (<span id="sales-chart-year">{{ $salesChartPayload['year'] }}</span>)</h4>
                @if($loggedInUser->role === 'global_admin')
                <div class="sales-chart-toolbar">
                  <label for="sales-chart-year-select" class="mb-0 font-weight-bold">Year</label>
                  <select id="sales-chart-year-select" class="form-control form-control-sm"></select>
                  <button type="button" id="downloadSalesChart" class="btn btn-outline-primary btn-sm">Download Chart</button>
                </div>
                @endif
              </div>
              <div class="sales-chart-meta mb-3">
                <span id="sales-chart-refresh-indicator">Auto-refresh every 3 minutes.</span>
                <span class="mx-2">|</span>
                <span>Last updated: <strong id="sales-chart-updated-at">{{ $salesChartPayload['updated_at'] }}</strong></span>
              </div>
              <hr/>

              <div class="sales-chart-canvas-wrap">
                <canvas id="salesBarChart"></canvas>
              </div>

          

            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12 d-flex grid-margin stretch-card">
          <div class="card w-100">
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap: 12px;">
                <h4 class="card-title mb-0"><x-bi en="Sold Items Report" ar="تقرير العناصر المباعة" /></h4>
                <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                  <select name="report_year" class="form-control form-control-sm" style="min-width: 110px;">
                    @foreach($soldItemsReport['available_years'] as $reportYear)
                    <option value="{{ $reportYear }}" {{ (int) $soldItemsReport['selected_year'] === (int) $reportYear ? 'selected' : '' }}>{{ $reportYear }}</option>
                    @endforeach
                  </select>

                  <select name="report_month" class="form-control form-control-sm" style="min-width: 140px;">
                    <option value="">All Months</option>
                    @foreach($soldItemsReport['available_months'] as $reportMonth)
                    <option value="{{ $reportMonth }}" {{ (int) $soldItemsReport['selected_month'] === (int) $reportMonth ? 'selected' : '' }}>
                      {{ \Carbon\Carbon::create()->month($reportMonth)->format('F') }}
                    </option>
                    @endforeach
                  </select>

                  <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                </form>
              </div>

              <div class="mb-3 text-muted" style="font-size: 0.9rem;">
                Total sold items: <strong>{{ $soldItemsReport['total_items_sold'] }}</strong>
                <span class="mx-2">|</span>
                Menu variants sold: <strong>{{ $soldItemsReport['total_menus_sold'] }}</strong>
                <span class="mx-2">|</span>
                Last updated: <strong>{{ $soldItemsReport['updated_at'] }}</strong>
              </div>

              <div class="mb-4">
                <h6 class="font-weight-bold mb-2">Top Sold Items Chart</h6>
                @if(!empty($soldItemsReport['chart']['labels']))
                <div class="report-chart-wrap">
                  <canvas id="soldItemsBarChart"></canvas>
                </div>
                @else
                <div class="text-muted">No sold-item chart data found for this period.</div>
                @endif
              </div>

              <div class="row">
                <div class="col-lg-5 mb-3 mb-lg-0">
                  <h6 class="font-weight-bold mb-2">Top Menu Items</h6>
                  <div class="table-responsive">
                    <table class="table table-striped table-sm mb-0">
                      <thead>
                        <tr>
                          <th>Menu Item</th>
                          <th class="text-right">Qty Sold</th>
                          <th class="text-right">Orders</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($soldItemsReport['summary'] as $row)
                        <tr>
                          <td>
                            <span class="report-menu-name">
                              <span>{{ $row->menu_name_en }}</span>
                              <span class="report-menu-name-ar" dir="rtl" lang="ar">{{ $row->menu_name_ar }}</span>
                            </span>
                          </td>
                          <td class="text-right">{{ (int) $row->total_quantity }}</td>
                          <td class="text-right">{{ (int) $row->total_orders }}</td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="3" class="text-center text-muted">No sold-item data found for this period.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="col-lg-7">
                  <h6 class="font-weight-bold mb-2">Daily Item Sales History</h6>
                  <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Menu Item</th>
                          <th class="text-right">Qty Sold</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($soldItemsReport['history'] as $history)
                        <tr>
                          <td>{{ \Carbon\Carbon::parse($history->sold_date)->format('d M Y') }}</td>
                          <td>
                            <span class="report-menu-name">
                              <span>{{ $history->menu_name_en }}</span>
                              <span class="report-menu-name-ar" dir="rtl" lang="ar">{{ $history->menu_name_ar }}</span>
                            </span>
                          </td>
                          <td class="text-right">{{ (int) $history->total_quantity }}</td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="3" class="text-center text-muted">No history data found for this period.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
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



 
