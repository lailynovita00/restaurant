
@extends('layouts.admin')

@push('styles')
    <!-- base:css -->
    <link rel="stylesheet" href="/admin_resources/vendors/typicons.font/font/typicons.css">
    <link rel="stylesheet" href="/admin_resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/admin_resources/css/vertical-layout-light/style.css">
    <style>
        .category-select option {
            white-space: pre-line;
        }

        .menu-category-quick-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .menu-category-quick-nav .btn {
            max-width: 260px;
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

 

<script>
    $(document).ready(function() {
        // Edit Modal
        $('.edit-btn').on('click', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let name_ar = $(this).data('name_ar');
            let description = $(this).data('description');
            let description_ar = $(this).data('description_ar');
            let video_url = $(this).data('video_url');
            let price = $(this).data('price');
            let category_id = $(this).data('category_id');
            
            let actionUrl = "{{ route('admin.menus.update', ':id') }}".replace(':id', id);

            $('#editName').val(name);
            $('#editNameAr').val(name_ar);
            $('#editDescription').val(description);
            $('#editDescriptionAr').val(description_ar);
            $('#editVideoUrl').val(video_url);
            $('#editPrice').val(price);
            $('#editCategory').val(category_id);
            $('#editForm').attr('action', actionUrl);
        });

        // Delete Modal
        $('.delete-btn').on('click', function() {
            let id = $(this).data('id');
            let actionUrl = "{{ route('admin.menus.destroy', ':id') }}".replace(':id', id);

            $('#deleteForm').attr('action', actionUrl);
        });
    });
</script>
 

<script>
    $(document).ready(function() {
        // When a thumbnail image is clicked
        $('.trigger-lightbox').click(function() {
            // Get the image URL from the data-image attribute
            var imageUrl = $(this).data('image');
            
            // Set the source of the modal image to the clicked image's URL
            $('#modalImage').attr('src', imageUrl);
        });

        $('.menu-category-jump').on('click', function(e) {
            const target = $(this).attr('href');
            if (!target || !target.startsWith('#')) {
                return;
            }

            const $target = $(target);
            if (!$target.length) {
                return;
            }

            e.preventDefault();
            const offsetTop = $target.offset().top - 90;
            $('html, body').animate({ scrollTop: offsetTop }, 250);
        });
    });
</script>

@endpush


@section('title', 'Admin - Menu')




@section('content')

<div class="main-panel">
    <div class="content-wrapper">
 
      @include('partials.message-bag')

 
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><x-bi :en="'Menus (' . $categories->sum(fn($category) => $category->menus->count()) . ')'" :ar="'القائمة (' . $categories->sum(fn($category) => $category->menus->count()) . ')'" /></span>
            @if($canManageMenuCrud)
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                <x-bi en="Add Menu" ar="إضافة عنصر" />
            </button>
            @endif
        </div>
        <div class="card-body">
            @if($categories->isNotEmpty())
                <div class="menu-category-quick-nav">
                    @foreach ($categories as $category)
                        <a href="#menu-category-{{ $category->id }}" class="btn btn-outline-primary btn-sm menu-category-jump">
                            <x-bi :en="$category->name" :ar="$category->name_ar ?: $category->name" />
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="row">
                @forelse ($categories as $category)
                    <div class="col-md-12 mb-4" id="menu-category-{{ $category->id }}">
                        <h4><x-bi :en="'CATEGORY: ' . $category->name" :ar="'الفئة: ' . ($category->name_ar ?: $category->name)" /></h4>
                        <hr style="border:1px solid #000">
                        <div class="table-responsive pt-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width:30%"><x-bi en="Name" ar="الاسم" /></th>
                                        <th style="width:35%"><x-bi en="Description" ar="الوصف" /></th>
                                        <th style="width:18%"><x-bi en="Video URL" ar="رابط الفيديو" /></th>
                                        <th><x-bi en="Price" ar="السعر" /></th>
                                        <th><x-bi en="Status" ar="الحالة" /></th>
                                        <th><x-bi en="Actions" ar="الإجراءات" /></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($category->menus as $menu)
                                        <tr>
                                            <td>
                                                <!-- Trigger for Lightbox Modal -->
                                                <img src="{{ $menu->image_url }}" alt="Menu Image" width="50" class="img-thumbnail trigger-lightbox" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="{{ $menu->image_url }}">
                                                <x-bi :en="$menu->name" :ar="$menu->name_ar ?: '-'" />
                                            </td>
                                            <td><x-bi :en="$menu->description ?: '-'" :ar="$menu->description_ar ?: '-'" /></td>
                                            <td>
                                                @if($menu->video_url)
                                                    <a href="{{ $menu->video_url }}" target="_blank" rel="noopener noreferrer" title="{{ $menu->video_url }}">
                                                        {{ \Illuminate\Support\Str::limit($menu->video_url, 45) }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $menu->price }} {!! $site_settings->currency_symbol !!}</td>
                                            <td>
                                                @if($menu->is_hidden)
                                                    <span class="badge bg-warning text-dark"><x-bi en="Hidden" ar="مخفي" /></span>
                                                @else
                                                    <span class="badge bg-success"><x-bi en="Visible" ar="ظاهر" /></span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.menus.toggle-visibility', $menu->id) }}" method="POST" class="d-inline toggle-visibility-form" id="toggle-form-{{ $menu->id }}">
                                                    @csrf
                                                    <button type="submit"
                                                            class="m-1 btn btn-sm {{ $menu->is_hidden ? 'btn-success' : 'btn-warning' }}"
                                                            title="{{ $menu->is_hidden ? 'Show menu' : 'Hide menu' }}"
                                                            aria-label="{{ $menu->is_hidden ? 'Show menu' : 'Hide menu' }}">
                                                        <i class="fa {{ $menu->is_hidden ? 'fa-eye' : 'fa-eye-slash' }}" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                                @if($canManageMenuCrud)
                                                <button class="m-1 btn btn-primary btn-sm edit-btn"
                                                        data-id="{{ $menu->id }}"
                                                        data-name="{{ $menu->name }}"
                                                        data-name_ar="{{ $menu->name_ar }}"
                                                        data-description="{{ $menu->description }}"
                                                        data-description_ar="{{ $menu->description_ar }}"
                                                        data-video_url="{{ $menu->video_url }}"
                                                        data-price="{{ $menu->price }}"
                                                        data-category_id="{{ $menu->category_id }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal">
                                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                                </button>
                                                <button class="m-1 btn btn-danger btn-sm delete-btn"
                                                        data-id="{{ $menu->id }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center"><x-bi en="No menus available." ar="لا توجد عناصر متاحة." /></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <p><x-bi en="No categories available." ar="لا توجد فئات متاحة." /></p>
                @endforelse
            </div>
        </div>
    </div>
    
  


<!-- Lightbox Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel"><x-bi en="Menu Image" ar="صورة العنصر" /></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="menu image" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
@if($canManageMenuCrud)
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel"><x-bi en="Add Menu" ar="إضافة عنصر" /></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label"><x-bi en="Name" ar="الاسم" /></label>
                        <input type="text" name="name" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="name_ar" class="form-label"><x-bi en="Name (Arabic)" ar="الاسم (عربي)" /></label>
                        <input type="text" name="name_ar" class="form-control" id="name_ar">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label"><x-bi en="Description" ar="الوصف" /></label>
                        <textarea name="description" class="form-control" id="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="description_ar" class="form-label"><x-bi en="Description (Arabic)" ar="الوصف (عربي)" /></label>
                        <textarea name="description_ar" class="form-control" id="description_ar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label"><x-bi :en="'Price (' . $site_settings->currency_symbol . ')'" :ar="'السعر (' . $site_settings->currency_symbol . ')'" /></label>
                        <input type="number" step="0.01" name="price" class="form-control" id="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="video_url" class="form-label"><x-bi en="Process Video URL (YouTube/Google Drive)" ar="رابط فيديو التحضير (يوتيوب/جوجل درايف)" /></label>
                        <input type="url" name="video_url" class="form-control" id="video_url" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                    <div class="alert alert-danger" role="alert">
                        <x-bi en="Recommended image size is" ar="المقاس الموصى به للصورة هو" /> <strong>500 x 400</strong>. <x-bi en="Uploaded images will be cropped to recommended size." ar="سيتم قص الصور المرفوعة إلى المقاس الموصى به." />
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label"><x-bi en="Image" ar="الصورة" /></label>
                        <input type="file" name="image" class="form-control" id="image" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label"><x-bi en="Category" ar="الفئة" /></label>
                        <select name="category_id" class="form-control category-select" id="category_id" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}@if($category->name_ar)&#10;{{ $category->name_ar }}@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Close" ar="إغلاق" /></button>
                    <button type="submit" class="btn btn-primary"><x-bi en="Save" ar="حفظ" /></button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel"><x-bi en="Edit Menu" ar="تعديل العنصر" /></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label"><x-bi en="Name" ar="الاسم" /></label>
                        <input type="text" name="name" class="form-control" id="editName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editNameAr" class="form-label"><x-bi en="Name (Arabic)" ar="الاسم (عربي)" /></label>
                        <input type="text" name="name_ar" class="form-control" id="editNameAr">
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label"><x-bi en="Description" ar="الوصف" /></label>
                        <textarea name="description" class="form-control" id="editDescription"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editDescriptionAr" class="form-label"><x-bi en="Description (Arabic)" ar="الوصف (عربي)" /></label>
                        <textarea name="description_ar" class="form-control" id="editDescriptionAr"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editPrice" class="form-label"><x-bi :en="'Price (' . $site_settings->currency_symbol . ')'" :ar="'السعر (' . $site_settings->currency_symbol . ')'" /></label>
                        <input type="number" step="0.01" name="price" class="form-control" id="editPrice" required>
                    </div>
                    <div class="mb-3">
                        <label for="editVideoUrl" class="form-label"><x-bi en="Process Video URL (YouTube/Google Drive)" ar="رابط فيديو التحضير (يوتيوب/جوجل درايف)" /></label>
                        <input type="url" name="video_url" class="form-control" id="editVideoUrl" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                    <div class="alert alert-danger" role="alert">
                        <x-bi en="Recommended image size is" ar="المقاس الموصى به للصورة هو" /> <strong>500 x 400</strong>. <x-bi en="Uploaded images will be cropped to recommended size." ar="سيتم قص الصور المرفوعة إلى المقاس الموصى به." />
                    </div>
                    <div class="mb-3">
                        <label for="editImage" class="form-label"><x-bi en="Image" ar="الصورة" /></label>
                        <input type="file" name="image" class="form-control" id="editImage">
                    </div>
                    <div class="mb-3">
                        <label for="editCategory" class="form-label"><x-bi en="Category" ar="الفئة" /></label>
                        <select name="category_id" class="form-control category-select" id="editCategory" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}@if($category->name_ar)&#10;{{ $category->name_ar }}@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Close" ar="إغلاق" /></button>
                    <button type="submit" class="btn btn-primary"><x-bi en="Update" ar="تحديث" /></button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel"><x-bi en="Delete Menu" ar="حذف العنصر" /></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <x-bi en="Are you sure you want to delete this menu?" ar="هل أنت متأكد أنك تريد حذف هذا العنصر؟" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Close" ar="إغلاق" /></button>
                    <button type="submit" class="btn btn-danger"><x-bi en="Delete" ar="حذف" /></button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

   
    </div>
    <!-- content-wrapper ends -->
    @include('partials.admin.footer')
  </div>
  <!-- main-panel ends -->
@endsection



 
