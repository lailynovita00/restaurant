<!DOCTYPE html>
<html lang="en">
  <head>
     <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>

    @stack('styles')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <style>
      :root {
        --brand-brown: #92824e;
        --brand-brown-dark: #765e39;
        --brand-brown-soft: #f6f1e7;
        --brand-brown-soft-alt: #efe7d8;
      }

      .navbar .navbar-brand-wrapper .navbar-brand.brand-logo {
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .navbar .navbar-brand-wrapper .navbar-brand.brand-logo img {
        width: auto !important;
        max-width: 210px;
        height: 46px !important;
        object-fit: contain;
      }

      .navbar .navbar-brand-wrapper {
        border-bottom: 1px solid #efe7d8;
      }

      .navbar .navbar-menu-wrapper .nav-link,
      a {
        color: var(--brand-brown-dark);
      }

      a:hover {
        color: var(--brand-brown);
      }

      .btn-primary,
      .btn-primary:focus,
      .btn-primary.focus {
        background-color: var(--brand-brown) !important;
        border-color: var(--brand-brown) !important;
        color: #fff !important;
      }

      .btn-primary:hover,
      .btn-primary:active,
      .btn-primary:not(:disabled):not(.disabled):active {
        background-color: var(--brand-brown-dark) !important;
        border-color: var(--brand-brown-dark) !important;
      }

      .btn-outline-primary {
        color: var(--brand-brown) !important;
        border-color: var(--brand-brown) !important;
      }

      .btn-outline-primary:hover {
        background-color: var(--brand-brown) !important;
        color: #fff !important;
      }

      .form-control:focus,
      .select2-container--default .select2-selection--single:focus {
        border-color: var(--brand-brown) !important;
        box-shadow: 0 0 0 0.2rem rgba(146, 130, 78, 0.2) !important;
      }

      .card-header {
        background-color: var(--brand-brown-soft) !important;
        border-bottom: 1px solid #eadfca !important;
        color: var(--brand-brown-dark) !important;
      }

      .page-item.active .page-link {
        background-color: var(--brand-brown) !important;
        border-color: var(--brand-brown) !important;
      }

      .page-link {
        color: var(--brand-brown-dark) !important;
      }

      .page-link:hover {
        color: var(--brand-brown) !important;
      }

      .sidebar .nav:not(.sub-menu) > .nav-item:hover > .nav-link,
      .sidebar .nav:not(.sub-menu) > .nav-item.active-nav > .nav-link {
        background: var(--brand-brown-soft-alt) !important;
      }

      .sidebar {
        background: var(--brand-brown-soft) !important;
        border-right: 1px solid #eadfca;
      }

      .sidebar .sidebar-profile {
        background: transparent !important;
        border-bottom: 1px solid #eadfca;
      }

      .sidebar .nav:not(.sub-menu) > .nav-item.active-nav {
        background: transparent !important;
      }

      .sidebar .nav .nav-item .nav-link .menu-title,
      .sidebar .nav .nav-item .nav-link i.menu-icon {
        color: var(--brand-brown-dark) !important;
      }

      .sidebar .nav .nav-item.active-nav > .nav-link .menu-title,
      .sidebar .nav .nav-item.active-nav > .nav-link i.menu-icon {
        color: var(--brand-brown) !important;
        font-weight: 600;
      }

      .sidebar .sidebar-profile .sidebar-profile-name .sidebar-designation {
        color: var(--brand-brown-dark) !important;
      }

      .sidebar .sidebar-profile .sidebar-profile-name .sidebar-name {
        color: var(--brand-brown-dark) !important;
      }

      .sidebar .nav.sub-menu .nav-item .nav-link.active,
      .sidebar .nav.sub-menu .nav-item .nav-link:hover {
        color: var(--brand-brown) !important;
      }

      .bi-text {
        display: inline-flex;
        flex-direction: column;
        line-height: 1.15;
      }

      .bi-text .bi-ar,
      .bi-text .bi-ar-inline {
        margin-top: 2px;
        font-size: 0.9em;
      }

      .bi-text .bi-sep {
        display: none;
      }

      .sidebar .menu-title .bi-text {
        display: inline-flex;
        flex-direction: column;
        line-height: 1.1;
      }

      .sidebar .menu-title .bi-text .bi-en {
        font-size: 1em;
      }

      .sidebar .menu-title .bi-text .bi-ar,
      .sidebar .menu-title .bi-text .bi-ar-inline {
        font-size: 0.78em;
        margin-top: 2px;
        opacity: 0.9;
      }

    </style>

    <!-- endinject -->
    <link rel="shortcut icon" type="image/png" href="/assets/images/favicon.png?v=2" />
  </head>
  <body>
 
    <div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
          <a class="navbar-brand brand-logo" href="{{ route('admin.dashboard') }}"><img src="/assets/images/palombini-logo.png" alt="Palombini Cafe Logo"/></a>
          <button class="navbar-toggler navbar-toggler align-self-center d-none d-lg-flex" type="button" data-toggle="minimize">
            <span class="typcn typcn-th-menu"></span>
          </button>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
 
          <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item d-none d-lg-flex  mr-2">  <a class="nav-link" href="{{ route('admin.view.myprofile') }}"> <i class="typcn typcn-user-outline mr-0"></i> {{ $loggedInUser->first_name }}  </a> </li>
            <li class="nav-item d-none d-lg-flex  mr-2">  <a class="nav-link" href="{{ route('auth.logout') }}"> <i class="typcn typcn-power-outline mr-0"></i> Logout  </a> </li>
         
         

          </ul>

          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="typcn typcn-th-menu"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">


        @include('partials.admin.sidebar')
        
        @yield('content')

        @include('partials.logout')

      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
 
    @stack('scripts')

    <script id="admin-realtime-config" type="application/json">{!! json_encode(['orderStatsUrl' => route('admin.realtime.order-stats')]) !!}</script>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const realtimeConfig = JSON.parse(document.getElementById('admin-realtime-config').textContent);
        const realtimeUrl = realtimeConfig.orderStatsUrl;
        const pollingIntervalMs = 1000;
        let isPolling = false;
        let latestSnapshot = null;

        function applyRealtimeStats(payload) {
          const realtimeNodes = document.querySelectorAll('[data-realtime-field]');
          realtimeNodes.forEach(function (node) {
            const field = node.getAttribute('data-realtime-field');
            if (field && Object.prototype.hasOwnProperty.call(payload, field)) {
              node.textContent = payload[field];
            }
          });
        }

        async function pollRealtimeStats() {
          if (isPolling) {
            return;
          }

          isPolling = true;
          try {
            const response = await fetch(realtimeUrl, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            if (!response.ok) {
              return;
            }

            const payload = await response.json();
            applyRealtimeStats(payload);

            window.dispatchEvent(new CustomEvent('admin:order-stats-updated', {
              detail: {
                previous: latestSnapshot,
                current: payload
              }
            }));

            latestSnapshot = payload;
          } catch (error) {
            console.error('Realtime order stats polling failed:', error);
          } finally {
            isPolling = false;
          }
        }

        pollRealtimeStats();
        window.setInterval(pollRealtimeStats, pollingIntervalMs);
      });
    </script>

 
  </body>
</html>
