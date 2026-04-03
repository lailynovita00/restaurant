
@extends('layouts.admin')

@push('styles')
    <!-- base:css -->
    <link rel="stylesheet" href="/admin_resources/vendors/typicons.font/font/typicons.css">
    <link rel="stylesheet" href="/admin_resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/admin_resources/css/vertical-layout-light/style.css">
    
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

<script>
    $(document).ready(function () {
       function toggleSaucePicker(prefix) {
           const requires = $(`#${prefix}RequiresSauce`).is(':checked');
           $(`#${prefix}SaucePicker`).toggleClass('d-none', !requires);
       }

       function toggleSidePicker(prefix) {
           const requires = $(`#${prefix}RequiresSide`).is(':checked');
           $(`#${prefix}SidePicker`).toggleClass('d-none', !requires);
       }

       $('.edit-btn').on('click', function () {
           let categoryId = $(this).data('id');
           let categoryName = $(this).data('name');
           let categoryNameAr = $(this).data('name_ar');
           let requiresSauce = Number($(this).data('requires_sauce')) === 1;
           let requiresSide = Number($(this).data('requires_side')) === 1;
           let sauceIds = String($(this).data('sauce_ids') || '')
               .split(',')
               .filter(Boolean)
               .map(String);
           let sideIds = String($(this).data('side_ids') || '')
               .split(',')
               .filter(Boolean)
               .map(String);
   
           $('#editName').val(categoryName);
           $('#editNameAr').val(categoryNameAr);
           $('#editRequiresSauce').prop('checked', requiresSauce);
           $('#editRequiresSide').prop('checked', requiresSide);
           $('#editSauceIds option').prop('selected', false);
           sauceIds.forEach(function (id) {
               $(`#editSauceIds option[value="${id}"]`).prop('selected', true);
           });
           $('#editSideIds option').prop('selected', false);
           sideIds.forEach(function (id) {
               $(`#editSideIds option[value="${id}"]`).prop('selected', true);
           });
           toggleSaucePicker('edit');
           toggleSidePicker('edit');
   
           let actionUrl = "{{ route('admin.categories.update', ':id') }}".replace(':id', categoryId);
           $('#editForm').attr('action', actionUrl);
   
       });

       $('#createRequiresSauce').on('change', function () {
           toggleSaucePicker('create');
       });

       $('#editRequiresSauce').on('change', function () {
           toggleSaucePicker('edit');
       });

       $('#createRequiresSide').on('change', function () {
           toggleSidePicker('create');
       });

       $('#editRequiresSide').on('change', function () {
           toggleSidePicker('edit');
       });

       toggleSaucePicker('create');
       toggleSidePicker('create');
   });
   </script>
   
   <script>
     $(document).ready(function() {
         $('.delete-btn').on('click', function() {
             let id = $(this).data('id');
             let actionUrl = "{{ route('admin.categories.destroy', ':id') }}".replace(':id', id);
             $('#deleteForm').attr('action', actionUrl);
         });
     });
   </script>
   

@endpush


@section('title', 'Admin - Settings - Categories')




@section('content')

<div class="main-panel">
    <div class="content-wrapper">
 
      @include('partials.message-bag')

    
 


        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><x-bi :en="'Categories (' . $categories->count() . ')'" :ar="'الفئات (' . $categories->count() . ')'" /></span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                    <x-bi en="Add New Category" ar="إضافة فئة جديدة" />
                </button>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:40%;"><x-bi en="Name (EN)" ar="الاسم (إنجليزي)" /></th>
                            <th style="width:40%;"><x-bi en="Name (AR)" ar="الاسم (عربي)" /></th>
                            <th style="width:20%;"><x-bi en="Sauce Rule" ar="إعداد الصوص" /></th>
                            <th style="width:20%;"><x-bi en="Side Rule" ar="إعداد السايد" /></th>
                            <th><x-bi en="Actions" ar="الإجراءات" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                        <tr>
                            <td><i class="typcn typcn-th-large mr-0"></i> {{ $category->name }}</td>
                            <td dir="rtl" lang="ar">{{ $category->name_ar ?? '-' }}</td>
                            <td>
                                @if($category->requires_sauce)
                                    <span class="badge bg-success text-white"><x-bi en="Required" ar="إجباري" /></span>
                                @else
                                    <span class="badge bg-secondary text-white"><x-bi en="Not Required" ar="غير إجباري" /></span>
                                @endif
                            </td>
                            <td>
                                @if($category->requires_side)
                                    <span class="badge bg-success text-white"><x-bi en="Required (Choose 2)" ar="إجباري (اختر 2)" /></span>
                                @else
                                    <span class="badge bg-secondary text-white"><x-bi en="Not Required" ar="غير إجباري" /></span>
                                @endif
                            </td>
                            <td>
                                <button 
                                    class="m-2 btn btn-success btn-sm edit-btn" 
                                    data-id="{{ $category->id }}" 
                                    data-name="{{ $category->name }}" 
                                    data-name_ar="{{ $category->name_ar }}"
                                    data-requires_sauce="{{ $category->requires_sauce ? 1 : 0 }}"
                                    data-requires_side="{{ $category->requires_side ? 1 : 0 }}"
                                    data-sauce_ids="{{ $category->sauces->pluck('id')->implode(',') }}"
                                    data-side_ids="{{ $category->sides->pluck('id')->implode(',') }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal"><i class="fa fa-edit"></i></button>

                                <button 
                                    class="m-2 btn btn-danger btn-sm delete-btn" 
                                    data-id="{{ $category->id }}" 
                                    data-name="{{ $category->name }}" 
                                    data-name_ar="{{ $category->name_ar }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center"><x-bi en="No categories available." ar="لا توجد فئات متاحة." /></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
   
    
    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel"><x-bi en="Add New Category" ar="إضافة فئة جديدة" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name"><x-bi en="Category Name (EN)" ar="اسم الفئة (إنجليزي)" /></label>
                            <input type="text" name="name" class="form-control" id="name" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="name_ar"><x-bi en="Category Name (AR)" ar="اسم الفئة (عربي)" /></label>
                            <input type="text" name="name_ar" class="form-control" id="name_ar" dir="rtl" lang="ar">
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" value="1" id="createRequiresSauce" name="requires_sauce">
                            <label class="form-check-label" for="createRequiresSauce">
                                <x-bi en="Require sauce selection for this category" ar="إلزام اختيار صوص لهذه الفئة" />
                            </label>
                        </div>
                        <div class="form-group mt-3 d-none" id="createSaucePicker">
                            <label for="createSauceIds"><x-bi en="Available Sauces" ar="الصوصات المتاحة" /></label>
                            <select class="form-control" id="createSauceIds" name="sauce_ids[]" multiple>
                                @foreach($sauces as $sauce)
                                    <option value="{{ $sauce->id }}">{{ $sauce->name }}@if($sauce->name_ar) / {{ $sauce->name_ar }}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" value="1" id="createRequiresSide" name="requires_side">
                            <label class="form-check-label" for="createRequiresSide">
                                <x-bi en="Require selecting 2 sides for this category" ar="إلزام اختيار 2 سايد لهذه الفئة" />
                            </label>
                        </div>
                        <div class="form-group mt-3 d-none" id="createSidePicker">
                            <label for="createSideIds"><x-bi en="Available Sides" ar="الأصناف الجانبية المتاحة" /></label>
                            <select class="form-control" id="createSideIds" name="side_ids[]" multiple>
                                @foreach($sides as $side)
                                    <option value="{{ $side->id }}">{{ $side->name }}@if($side->name_ar) / {{ $side->name_ar }}@endif</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                        <button type="submit" class="btn btn-primary"><x-bi en="Create" ar="إنشاء" /></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="editForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel"><x-bi en="Edit Category" ar="تعديل الفئة" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editName"><x-bi en="Category Name (EN)" ar="اسم الفئة (إنجليزي)" /></label>
                            <input type="text" name="name" class="form-control" id="editName" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="editNameAr"><x-bi en="Category Name (AR)" ar="اسم الفئة (عربي)" /></label>
                            <input type="text" name="name_ar" class="form-control" id="editNameAr" dir="rtl" lang="ar">
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" value="1" id="editRequiresSauce" name="requires_sauce">
                            <label class="form-check-label" for="editRequiresSauce">
                                <x-bi en="Require sauce selection for this category" ar="إلزام اختيار صوص لهذه الفئة" />
                            </label>
                        </div>
                        <div class="form-group mt-3 d-none" id="editSaucePicker">
                            <label for="editSauceIds"><x-bi en="Available Sauces" ar="الصوصات المتاحة" /></label>
                            <select class="form-control" id="editSauceIds" name="sauce_ids[]" multiple>
                                @foreach($sauces as $sauce)
                                    <option value="{{ $sauce->id }}">{{ $sauce->name }}@if($sauce->name_ar) / {{ $sauce->name_ar }}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" value="1" id="editRequiresSide" name="requires_side">
                            <label class="form-check-label" for="editRequiresSide">
                                <x-bi en="Require selecting 2 sides for this category" ar="إلزام اختيار 2 سايد لهذه الفئة" />
                            </label>
                        </div>
                        <div class="form-group mt-3 d-none" id="editSidePicker">
                            <label for="editSideIds"><x-bi en="Available Sides" ar="الأصناف الجانبية المتاحة" /></label>
                            <select class="form-control" id="editSideIds" name="side_ids[]" multiple>
                                @foreach($sides as $side)
                                    <option value="{{ $side->id }}">{{ $side->name }}@if($side->name_ar) / {{ $side->name_ar }}@endif</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                        <button type="submit" class="btn btn-primary"><x-bi en="Save Changes" ar="حفظ التغييرات" /></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <form method="POST" id="deleteForm">
          @csrf
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="deleteModalLabel"><x-bi en="Delete Category" ar="حذف الفئة" /></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
              </div>
              <div class="modal-body">
                  <p><x-bi en="Are you sure you want to delete" ar="هل أنت متأكد أنك تريد حذف" /> <strong id="deleteCategoryName"></strong>?</p>
                  <p class="text-warning"><x-bi en="Warning: Deleting this category will also delete all related menus and orders. This action cannot be undone." ar="تحذير: حذف هذه الفئة سيؤدي أيضًا إلى حذف جميع القوائم والطلبات المرتبطة بها. لا يمكن التراجع عن هذا الإجراء." /></p>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                  <button type="submit" class="btn btn-danger"><x-bi en="Delete" ar="حذف" /></button>
              </div>
          </div>
      </form>
  </div>
</div>





   
    </div>
    <!-- content-wrapper ends -->
    @include('partials.admin.footer')
  </div>
  <!-- main-panel ends -->
@endsection



 
