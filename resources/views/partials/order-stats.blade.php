<div class="row">
    <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-info">
        <div class="inner">
          <h3 data-realtime-field="pending_orders_count">{{ $pending_orders_count ?? 0 }}</h3>

          <p><x-bi en="Pending Orders" ar="الطلبات المعلقة" /></p>
        </div>
        <div class="icon">
          <i class="ion ion-help-circled"></i>
        </div>
        <a href="{{ route('admin.orders.index', ['filter' => 'pending']) }}" class="small-box-footer"><x-bi en="View" ar="عرض" /> <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-danger">
        <div class="inner">
          <h3 data-realtime-field="all_orders_count">{{ $all_orders_count ?? 0 }}</h3>

          <p><x-bi en="All Orders" ar="كل الطلبات" /></p>
        </div>
        <div class="icon">
          <i class="ion ion-pie-graph"></i>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="small-box-footer"><x-bi en="View" ar="عرض" /> <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
  </div>
 
