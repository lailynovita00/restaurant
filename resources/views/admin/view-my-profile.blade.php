
@extends('layouts.admin')

@push('styles')
    <!-- base:css -->
    <link rel="stylesheet" href="/admin_resources/vendors/typicons.font/font/typicons.css">
    <link rel="stylesheet" href="/admin_resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/admin_resources/css/vertical-layout-light/style.css">
        <style>
            .profile-bi.bi-text {
                display: inline-flex;
                flex-direction: column;
                line-height: 1.1;
            }

            .profile-bi.bi-text .bi-ar,
            .profile-bi.bi-text .bi-ar-inline {
                font-size: 0.78em;
                margin-top: 2px;
                opacity: 0.9;
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
 

@endpush


@section('title', 'Admin - Settings - Categories')




@section('content')

<div class="main-panel">
    <div class="content-wrapper">
 
      @include('partials.message-bag')
 

      <div class="card card-info">
        <div class="card-header">
            <i class="fa fa-user"></i> <x-bi class="profile-bi" en="My Profile" ar="ملفي الشخصي" />
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-center align-items-center">
                <!-- Profile Photo Preview -->
                <div class="mb-3 text-center">
                    <img src="{{ $user->profile_picture ? asset('storage/profile-picture/' . $user->profile_picture) : asset('assets/images/user-icon.png') }}" 
                         alt="Profile Preview" 
                         class="img-thumbnail" 
                         style="width: 150px; height: 150px;">
                    <div class="mt-2"><x-bi class="profile-bi" en="Profile Preview" ar="معاينة الملف الشخصي" /></div>
                </div>
            </div>
            <hr/>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td><b><x-bi class="profile-bi" en="First Name:" ar="الاسم الأول:" /></b></td>
                        <td>{{ $user->first_name }}</td>
                    </tr>
                    <tr>
                        <td><b><x-bi class="profile-bi" en="Middle Name:" ar="الاسم الأوسط:" /></b></td>
                        <td>{{ $user->middle_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><b><x-bi class="profile-bi" en="Last Name:" ar="اسم العائلة:" /></b></td>
                        <td>{{ $user->last_name }}</td>
                    </tr>
                    <tr>
                        <td><b><x-bi class="profile-bi" en="Email:" ar="البريد الإلكتروني:" /></b></td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td><b><x-bi class="profile-bi" en="Role:" ar="الدور:" /></b></td>
                        <td>{{ ucwords(str_replace('_', ' ', $user->role)) }}</td>
                    </tr>
                    <tr>
                        <td><b><x-bi class="profile-bi" en="Phone Number:" ar="رقم الهاتف:" /></b></td>
                        <td>{{ $user->phone_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><b><x-bi class="profile-bi" en="Address:" ar="العنوان:" /></b></td>
                        <td>{{ $user->address ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
            
        </div>
        <div class="card-footer">
            <button type="button" onclick="window.location='{{ route('admin.myprofile.edit') }}'" class="btn btn-info"><x-bi en="Edit My Profile" ar="تعديل الملف الشخصي" /></button>
            <button type="button" onclick="window.location='{{ route('admin.dashboard') }}'" class="btn btn-primary float-right"><x-bi en="Dashboard" ar="لوحة التحكم" /></button>
        </div>
    </div>

    
  


   
    </div>
    <!-- content-wrapper ends -->
    @include('partials.admin.footer')
  </div>
  <!-- main-panel ends -->
@endsection



 
