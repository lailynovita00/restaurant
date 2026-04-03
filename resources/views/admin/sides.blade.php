@extends('layouts.admin')

@push('styles')
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
<script src="/admin_resources/vendors/progressbar.js/progressbar.min.js"></script>
<script src="/admin_resources/vendors/chart.js/Chart.min.js"></script>
<script src="/admin_resources/js/dashboard.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        $('.edit-btn').on('click', function () {
            const id = $(this).data('id');
            const actionUrl = "{{ route('admin.sides.update', ':id') }}".replace(':id', id);

            $('#editName').val($(this).data('name'));
            $('#editNameAr').val($(this).data('name_ar'));
            $('#editSortOrder').val($(this).data('sort_order'));
            $('#editIsActive').prop('checked', Number($(this).data('is_active')) === 1);
            $('#editForm').attr('action', actionUrl);
        });

        $('.delete-btn').on('click', function () {
            const id = $(this).data('id');
            const actionUrl = "{{ route('admin.sides.destroy', ':id') }}".replace(':id', id);
            $('#deleteForm').attr('action', actionUrl);
        });
    });
</script>
@endpush

@section('title', 'Admin - Settings - Sides')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">

        @include('partials.message-bag')

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><x-bi :en="'Sides (' . $sides->count() . ')'" :ar="'الأصناف الجانبية (' . $sides->count() . ')'" /></span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                    <x-bi en="Add New Side" ar="إضافة سايد جديد" />
                </button>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th><x-bi en="Name (EN)" ar="الاسم (إنجليزي)" /></th>
                            <th><x-bi en="Name (AR)" ar="الاسم (عربي)" /></th>
                            <th><x-bi en="Sort" ar="الترتيب" /></th>
                            <th><x-bi en="Status" ar="الحالة" /></th>
                            <th><x-bi en="Actions" ar="الإجراءات" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sides as $side)
                            <tr>
                                <td>{{ $side->name }}</td>
                                <td dir="rtl" lang="ar">{{ $side->name_ar ?? '-' }}</td>
                                <td>{{ $side->sort_order }}</td>
                                <td>
                                    @if($side->is_active)
                                        <span class="badge bg-success text-white"><x-bi en="Active" ar="نشط" /></span>
                                    @else
                                        <span class="badge bg-secondary text-white"><x-bi en="Inactive" ar="غير نشط" /></span>
                                    @endif
                                </td>
                                <td>
                                    <button
                                        class="m-1 btn btn-success btn-sm edit-btn"
                                        data-id="{{ $side->id }}"
                                        data-name="{{ $side->name }}"
                                        data-name_ar="{{ $side->name_ar }}"
                                        data-sort_order="{{ $side->sort_order }}"
                                        data-is_active="{{ $side->is_active ? 1 : 0 }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button
                                        class="m-1 btn btn-danger btn-sm delete-btn"
                                        data-id="{{ $side->id }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center"><x-bi en="No sides available." ar="لا توجد أصناف جانبية متاحة." /></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.sides.store') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><x-bi en="Add New Side" ar="إضافة سايد جديد" /></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name"><x-bi en="Side Name (EN)" ar="اسم السايد (إنجليزي)" /></label>
                                <input type="text" name="name" class="form-control" id="name" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="name_ar"><x-bi en="Side Name (AR)" ar="اسم السايد (عربي)" /></label>
                                <input type="text" name="name_ar" class="form-control" id="name_ar" dir="rtl" lang="ar">
                            </div>
                            <div class="form-group mt-3">
                                <label for="sort_order"><x-bi en="Sort Order" ar="الترتيب" /></label>
                                <input type="number" name="sort_order" class="form-control" id="sort_order" min="0" value="0">
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active"><x-bi en="Active" ar="نشط" /></label>
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

        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" id="editForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><x-bi en="Edit Side" ar="تعديل السايد" /></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="editName"><x-bi en="Side Name (EN)" ar="اسم السايد (إنجليزي)" /></label>
                                <input type="text" name="name" class="form-control" id="editName" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="editNameAr"><x-bi en="Side Name (AR)" ar="اسم السايد (عربي)" /></label>
                                <input type="text" name="name_ar" class="form-control" id="editNameAr" dir="rtl" lang="ar">
                            </div>
                            <div class="form-group mt-3">
                                <label for="editSortOrder"><x-bi en="Sort Order" ar="الترتيب" /></label>
                                <input type="number" name="sort_order" class="form-control" id="editSortOrder" min="0" value="0">
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive" value="1">
                                <label class="form-check-label" for="editIsActive"><x-bi en="Active" ar="نشط" /></label>
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

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" id="deleteForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><x-bi en="Delete Side" ar="حذف السايد" /></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <x-bi en="Are you sure you want to delete this side?" ar="هل أنت متأكد أنك تريد حذف هذا السايد؟" />
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
    @include('partials.admin.footer')
</div>
@endsection
