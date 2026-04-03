
@extends('layouts.admin')

@push('styles')
    <!-- base:css -->
    <link rel="stylesheet" href="/admin_resources/vendors/typicons.font/font/typicons.css">
    <link rel="stylesheet" href="/admin_resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/admin_resources/css/vertical-layout-light/style.css">

    <style>

        /* Make Google Places suggestions appear above Bootstrap modals */
 
.pac-container {
    z-index: 20000 !important;
    background-color: #fff;
    border: 1px solid #ccc;
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
    $(document).ready(function () {
        const biLabel = (en, ar) => `<span class="bi-text"><span class="bi-en">${en}</span><span class="bi-ar" dir="rtl" lang="ar">${ar}</span></span>`;

        // Phone Number Modal
        function resetPhoneNumberModal() {
            $('#phoneNumberForm')[0].reset();
            $('#phoneNumberForm').attr('action', "{{ route('admin.phone-number.store') }}");
            $('#phoneNumberFormMethod').val('');
        }

        window.createPhoneNumber = function () {
            resetPhoneNumberModal();
            $('#phoneNumberModalLabel').html(biLabel('Add Phone Number', 'إضافة رقم هاتف'));
        };

        window.editPhoneNumber = function (id, phoneNumber, useWhatsapp) {
            resetPhoneNumberModal();
            $('#phone_number').val(phoneNumber);
            $('#use_whatsapp').prop('checked', useWhatsapp === 1); // Set checked if useWhatsapp is 1
            let actionUrl = "{{ route('admin.phone-number.update', ':id') }}".replace(':id', id);
            $('#phoneNumberForm').attr('action', actionUrl);
            $('#phoneNumberFormMethod').val('PUT');
            $('#phoneNumberModalLabel').html(biLabel('Edit Phone Number', 'تعديل رقم الهاتف'));
        };

        // Address Modal
         function resetAddressModal() {
            $('#addressForm')[0].reset();

            // Default to store route for "Add"
            $('#addressForm').attr('action', "{{ route('admin.address.store') }}");
            $('#addressFormMethod').val('');

            $('#addressModalLabel').html(biLabel('Add Address', 'إضافة عنوان'));
        }

        window.createAddress = function () {
            resetAddressModal();
            $('#addressModalLabel').html(biLabel('Add Address', 'إضافة عنوان'));
        };

        window.editAddress = function (button) {
            resetAddressModal();

            var $btn = $(button);

            var id          = $btn.data('id');
            var street      = $btn.data('street') || '';
            var city        = $btn.data('city') || '';
            var state       = $btn.data('state') || '';
            var postalCode  = $btn.data('postal_code') || '';
            var country     = $btn.data('country') || '';
            var latitude    = $btn.data('latitude') || '';
            var longitude   = $btn.data('longitude') || '';
            var fullAddress = $btn.data('full_address') || '';

            // Fill the modal fields
            $('#address').val(fullAddress);     // search box
            $('#street').val(street);
            $('#city').val(city);
            $('#state').val(state);
            $('#postal_code').val(postalCode);
            $('#country').val(country);
            $('#latitude').val(latitude);
            $('#longitude').val(longitude);

            // Change form to use update route
            let actionUrl = "{{ route('admin.address.update', ':id') }}".replace(':id', id);
            $('#addressForm').attr('action', actionUrl);
            $('#addressFormMethod').val('PUT');
            $('#addressModalLabel').html(biLabel('Edit Address', 'تعديل العنوان'));
        };

        // Working Hour Modal
        function resetWorkingHourModal() {
            $('#workingHourForm')[0].reset();
            $('#workingHourForm').attr('action', "{{ route('admin.working-hour.store') }}");
            $('#workingHourFormMethod').val(''); // clear _method
            $('#is_closed').prop('checked', false);
            toggleWorkingHourTimeInputs(false);
        }

        window.createWorkingHour = function () {
            resetWorkingHourModal();
            $('#workingHourModalLabel').html(biLabel('Add Working Hour', 'إضافة ساعات عمل'));
        };

        window.editWorkingHour = function (button) {
            resetWorkingHourModal();

            var $btn      = $(button);
            var id        = $btn.data('id');
            var day       = $btn.data('day');
            var opens     = $btn.data('opens');
            var closes    = $btn.data('closes');
            var isClosed  = $btn.data('is_closed') == 1;

            $('#day_of_week').val(day);
            $('#opens_at').val(opens || '');
            $('#closes_at').val(closes || '');
            $('#is_closed').prop('checked', isClosed);

            toggleWorkingHourTimeInputs(isClosed);

            let actionUrl = "{{ route('admin.working-hour.update', ':id') }}".replace(':id', id);
            $('#workingHourForm').attr('action', actionUrl);
            $('#workingHourFormMethod').val('PUT');
            $('#workingHourModalLabel').html(biLabel('Edit Working Hour', 'تعديل ساعات العمل'));
        };

        function toggleWorkingHourTimeInputs(isClosed) {
            const disabled = !!isClosed;
            $('#opens_at').prop('disabled', disabled);
            $('#closes_at').prop('disabled', disabled);
        }

        $('#is_closed').on('change', function () {
            toggleWorkingHourTimeInputs(this.checked);
        });

        // Social Media Handle Modal
        function resetSocialMediaModal() {
            $('#socialMediaForm')[0].reset();
            $('#socialMediaForm').attr('action', "{{ route('admin.social-media-handles.store') }}");
            $('#handle').val('');
            $('#socialMediaFormMethod').val('');
        }

        window.createSocialMediaHandle = function () {
            resetSocialMediaModal();
            $('#socialMediaModalLabel').html(biLabel('Add Social Media Handle', 'إضافة معرّف وسائل التواصل'));
        };

        window.editSocialMediaHandle = function (id, handle, socialMedia) {
            resetSocialMediaModal();
            $('#handle').val(handle);
            $('#social_media').val(socialMedia);
            let actionUrl = "{{ route('admin.social-media-handles.update', ':id') }}".replace(':id', id);
            $('#socialMediaForm').attr('action', actionUrl);
            $('#socialMediaFormMethod').val('PUT');
            $('#socialMediaModalLabel').html(biLabel('Edit Social Media Handle', 'تعديل معرّف وسائل التواصل'));
        };      

        // Phone Number Delete
        window.deletePhoneNumber = function (id) {
            let actionUrl = "{{ route('admin.phone-number.delete', ':id') }}".replace(':id', id);
            $('#deletePhoneNumberForm').attr('action', actionUrl);
            $('#deletePhoneNumberModal').modal('show');
        };

        // Address Delete
        window.deleteAddress = function (id) {
            let actionUrl = "{{ route('admin.address.delete', ':id') }}".replace(':id', id);
            $('#deleteAddressForm').attr('action', actionUrl);
            $('#deleteAddressModal').modal('show');
        };

        // Working Hour Delete
        window.deleteWorkingHour = function (id) {
            let actionUrl = "{{ route('admin.working-hour.delete', ':id') }}".replace(':id', id);
            $('#deleteWorkingHourForm').attr('action', actionUrl);
            $('#deleteWorkingHourModal').modal('show');
        };

        // Social Media Handle Delete
        window.deleteSocialMediaHandle = function (id) {
            let actionUrl = "{{ route('admin.social-media-handles.delete', ':id') }}".replace(':id', id);
            $('#deleteSocialMediaHandleForm').attr('action', actionUrl);
            $('#deleteSocialMediaHandleModal').modal('show');
        };
    });
</script>



<script>
 
    function initAddressModalPlaces() {
        var input = document.getElementById('address');
        if (!input || !window.google || !google.maps || !google.maps.places) {
            return;
        }

         input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        var autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['geocode'],
            fields: ['address_components', 'geometry', 'formatted_address']
        });

        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place || !place.address_components) {
                return;
            }

            var components = place.address_components;

            // Helper to pull a component by its type
            function findComponent(type) {
                var comp = components.find(function (c) {
                    return c.types.indexOf(type) !== -1;
                });
                return comp ? comp.long_name : '';
            }

            var streetNumber = findComponent('street_number');
            var route        = findComponent('route');
            var line1        = [streetNumber, route].filter(Boolean).join(' ');

             document.getElementById('line1').value        = line1;
             document.getElementById('city').value         = findComponent('locality')
                                                          || findComponent('postal_town')
                                                          || findComponent('sublocality')
                                                          || '';
            document.getElementById('state').value        = findComponent('administrative_area_level_1');
            document.getElementById('postal_code').value  = findComponent('postal_code');
            document.getElementById('country').value      = findComponent('country');

            if (place.geometry && place.geometry.location) {
                document.getElementById('latitude').value  = place.geometry.location.lat();
                document.getElementById('longitude').value = place.geometry.location.lng();
            }
        });
    }

     document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('addressModal');
        if (!modalEl) return;

        modalEl.addEventListener('shown.bs.modal', function () {
             if (window.google && google.maps && google.maps.places) {
                initAddressModalPlaces();
            }
        });
    });
</script>




<script src="https://maps.googleapis.com/maps/api/js?key={{  config('services.google_maps.api_key') }}&libraries=places&callback=initCheckoutDeliveryLookups" async defer></script>

<script>
    (function() {
        function updateCurrencyFields() {
            const select = document.getElementById('country_id');
            const option = select.options[select.selectedIndex];

            if (!option) return;

            const symbol = option.getAttribute('data-currency-symbol') || '';
            const code   = option.getAttribute('data-currency-code') || '';

            document.getElementById('decoded_symbol').value = symbol;
            document.getElementById('currency_code').value  = code;

             const hiddenSymbol = document.getElementById('currency_symbol');
            if (hiddenSymbol) hiddenSymbol.value = symbol;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('country_id');
            if (!select) return;

            select.addEventListener('change', updateCurrencyFields);

             updateCurrencyFields();
        });
    })();
</script>

@endpush


@section('title', 'Admin - Settings - General')




@section('content')

<div class="main-panel">
    <div class="content-wrapper">
 
      @include('partials.message-bag')

 
      <hr/>
    <h1><x-bi en="General Settings" ar="الإعدادات العامة" /></h1>
      




      <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <!-- Phone Numbers -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><x-bi en="Restaurant Phone Numbers" ar="أرقام هاتف المطعم" /></span>
                    <button class="btn-sm btn btn-primary" data-bs-toggle="modal" data-bs-target="#phoneNumberModal" onclick="createPhoneNumber()">
                        <i class="fa fa-plus"></i> <x-bi en="Add Phone Number" ar="إضافة رقم هاتف" />
                    </button>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="col-8"><x-bi en="Phone Number" ar="رقم الهاتف" /></th>
                                <th class="col-4 text-end"><x-bi en="Actions" ar="الإجراءات" /></th>
                            </tr>
                        </thead>
                        <tbody id="phoneNumbersTable">
                            @forelse($phoneNumbers as $phoneNumber)
                                <tr>
                                    <td>
                                        <i class="fa fa-phone" aria-hidden="true"></i> 
                                        {{ $phoneNumber->phone_number }}
                                        @if($phoneNumber->use_whatsapp == 1)
                                            <span class="badge bg-success"><i class="fab fa-whatsapp"></i></span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#phoneNumberModal" onclick="editPhoneNumber({{ $phoneNumber->id }}, '{{ $phoneNumber->phone_number }}', {{ $phoneNumber->use_whatsapp }})">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deletePhoneNumber({{ $phoneNumber->id }})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center"><x-bi en="No phone numbers available. Please add a new phone number." ar="لا توجد أرقام هاتف متاحة. يرجى إضافة رقم هاتف جديد." /></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
    
        <div class="col-md-6 grid-margin stretch-card">
            <!-- Addresses -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><x-bi en="Restaurant Addresses" ar="عناوين المطعم" /></span>
                    <button class="btn-sm btn btn-primary" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="createAddress()">
                        <i class="fa fa-plus"></i> <x-bi en="Add Address" ar="إضافة عنوان" />
                    </button>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="col-8"><x-bi en="Address" ar="العنوان" /></th>
                                <th class="col-4 text-end"><x-bi en="Actions" ar="الإجراءات" /></th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($addresses as $address)
                            <tr>
                                <td>
                                    <i class="fa fa-map-marker" aria-hidden="true"></i> 
                                    {{ $address->full_address }}
                                </td>
                                <td class="text-end">
                                    <button
                                        class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addressModal"

                                        {{-- ID for update route --}}
                                        data-id="{{ $address->id }}"

                                        {{-- Individual fields --}}
                                        data-street="{{ e($address->street) }}"
                                        data-city="{{ e($address->city) }}"
                                        data-state="{{ e($address->state) }}"
                                        data-postal_code="{{ e($address->postal_code) }}"
                                        data-country="{{ e($address->country) }}"
                                        data-latitude="{{ $address->latitude }}"
                                        data-longitude="{{ $address->longitude }}"

                                        {{-- What we want to show in the search box when editing --}}
                                        data-full_address="{{ e($address->full_address) }}"

                                        onclick="editAddress(this)"
                                    >
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm" onclick="deleteAddress({{ $address->id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center"><x-bi en="No addresses available. Please add a new address." ar="لا توجد عناوين متاحة. يرجى إضافة عنوان جديد." /></td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <!-- Social Media Handles -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><x-bi en="Social Media Handles" ar="معرّفات وسائل التواصل" /></span>
                    <button class="btn-sm btn btn-primary" data-bs-toggle="modal" data-bs-target="#socialMediaModal" onclick="createSocialMediaHandle()">
                        <i class="fa fa-plus"></i> <x-bi en="Add Handle" ar="إضافة معرّف" />
                    </button>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><x-bi en="Handle" ar="المعرّف" /></th>
                                <th><x-bi en="Social Media" ar="وسائل التواصل" /></th>
                                <th><x-bi en="Actions" ar="الإجراءات" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($socialMediaHandles as $handle)
                                <tr>
                                    <td>
                                        @if($handle->social_media === 'facebook')
                                            <i class="fab fa-facebook-square"></i>
                                        @elseif($handle->social_media === 'instagram')
                                            <i class="fab fa-instagram"></i>
                                        @elseif($handle->social_media === 'youtube')
                                            <i class="fab fa-youtube-square"></i>         
                                        @elseif($handle->social_media === 'tiktok')
                                            <i class="fab fa-tiktok"></i>                                        
                                        @else
                                            <i class="fa fa-globe"></i> 
                                        @endif
                                        {{ $handle->handle }}</td>
                                    <td>{{ ucfirst($handle->social_media) }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#socialMediaModal" onclick="editSocialMediaHandle({{ $handle->id }}, '{{ $handle->handle }}', '{{ $handle->social_media }}')">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteSocialMediaHandle({{ $handle->id }})"> <i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center"><x-bi en="No social media handles available. Please add new handles." ar="لا توجد معرّفات وسائل تواصل متاحة. يرجى إضافة معرّفات جديدة." /></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    
        <div class="col-md-6 grid-margin stretch-card">
            <!-- Working Hours -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><x-bi en="Restaurant Working Hours" ar="ساعات عمل المطعم" /></span>
                    <button class="btn-sm btn btn-primary" data-bs-toggle="modal" data-bs-target="#workingHourModal" onclick="createWorkingHour()">
                        <i class="fa fa-plus"></i> <x-bi en="Add Working Hours" ar="إضافة ساعات عمل" />
                    </button>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><x-bi en="Day" ar="اليوم" /></th>
                                <th><x-bi en="Opens At" ar="يفتح عند" /></th>
                                <th><x-bi en="Closes At" ar="يغلق عند" /></th>
                                <th><x-bi en="Status" ar="الحالة" /></th>
                                <th class="text-end"><x-bi en="Actions" ar="الإجراءات" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workingHours as $workingHour)
                                <tr>
                                    <td>{{ $workingHour->day_of_week }}</td>
                                    <td>
                                        @if(!$workingHour->is_closed && $workingHour->opens_at)
                                            {{ \Carbon\Carbon::parse($workingHour->opens_at)->format('H:i') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$workingHour->is_closed && $workingHour->closes_at)
                                            {{ \Carbon\Carbon::parse($workingHour->closes_at)->format('H:i') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($workingHour->is_closed)
                                            <span class="badge bg-danger"><x-bi en="Closed" ar="مغلق" /></span>
                                        @else
                                            <span class="badge bg-success"><x-bi en="Open" ar="مفتوح" /></span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button
                                            class="btn btn-warning btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#workingHourModal"

                                            data-id="{{ $workingHour->id }}"
                                            data-day="{{ $workingHour->day_of_week }}"
                                            data-opens="{{ $workingHour->opens_at ? \Carbon\Carbon::parse($workingHour->opens_at)->format('H:i') : '' }}"
                                            data-closes="{{ $workingHour->closes_at ? \Carbon\Carbon::parse($workingHour->closes_at)->format('H:i') : '' }}"
                                            data-is_closed="{{ $workingHour->is_closed ? 1 : 0 }}"

                                            onclick="editWorkingHour(this)"
                                        >
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteWorkingHour({{ $workingHour->id }})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <x-bi en="No working hours available. Please add new working hours." ar="لا توجد ساعات عمل متاحة. يرجى إضافة ساعات عمل جديدة." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>




    
    <div class="row">
        <div class="col-lg-6 d-flex grid-margin stretch-card">
            <form method="POST" action="{{ $script ? route('admin.livechat.update', $script->id) : route('admin.livechat.store') }}">
                <div class="card">
                    <div class="card-header">
                        @if($script)
                            <span><x-bi en="Edit Live Chat Script" ar="تعديل سكربت الدردشة المباشرة" /></span>
                        @else
                            <span><x-bi en="Add Live Chat Script" ar="إضافة سكربت دردشة مباشرة" /></span>
                        @endif
                    </div>
                    <div class="card-body">
                        @csrf
                        @if($script)
                            @method('PUT')
                        @endif
                        <div class="alert alert-danger" role="alert">
                            <i class="fa fa-exclamation-triangle"></i> <b>Please ensure you enter a valid live chat script code. Make sure the code is copied from a reliable third-party live chat provider.</b>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label for="name">Live Chat Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Tawk.to" value="{{ $script->name ?? '' }}" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="script_code">Script Code</label>
                            <textarea class="form-control" id="script_code" name="script_code" rows="2" placeholder="Paste the script code here..." required>{{ $script->script_code ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between mt-4">
                        @if($script)
                            <button type="submit" class="btn btn-primary"><x-bi en="Update" ar="تحديث" /></button>
                            <button type="button" class="btn btn-danger" onclick="if(confirm('Are you sure you want to delete this script?')) { document.getElementById('form-delete-livechat').submit(); }"><x-bi en="Remove Live Chat" ar="إزالة الدردشة المباشرة" /></button>
                        @else
                            <button type="submit" class="btn btn-primary"><x-bi en="Add Live Chat" ar="إضافة دردشة مباشرة" /></button>
                        @endif
                    </div>
                </div>
            </form>
    @if($script)
        <form method="POST" id="form-delete-livechat" action="{{ route('admin.livechat.destroy', $script->id) }}">
            @csrf
            @method('DELETE')
        </form>
    @endif
        </div>
        <div class="col-lg-6 d-flex grid-margin stretch-card">
 
        <div class="card">
            <div class="card-header">
                <x-bi en="Other Settings" ar="إعدادات أخرى" />
            </div>

            <form action="{{ route('site-settings.save') }}" method="POST" style="display: contents;">
                @csrf

                {{-- Hidden actual symbol that will be saved (set from selected country server-side anyway) --}}
                <input type="hidden" id="currency_symbol" name="currency_symbol">

                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            {{-- Country Selection --}}
                            <tr>
                                <td><strong><x-bi en="Country" ar="الدولة" /></strong></td>
                                <td>
                                    <select required class="form-control" id="country_id" name="country_id">
                                        <option value="" disabled {{ empty($site_settings?->country) ? 'selected' : '' }}>
                                            Select a country
                                        </option>

                                        @foreach ($countries as $country)
                                            <option
                                                value="{{ $country->id }}"
                                                data-currency-symbol="{{ $country->currency_symbol }}"
                                                data-currency-code="{{ $country->currency_code }}"
                                                {{ $site_settings?->country === $country->name ? 'selected' : '' }}
                                            >
                                                {{ $country->name }} ({{ $country->currency_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            {{-- Currency Details (display only) --}}
                            <tr>
                                <td><strong><x-bi en="Currency Symbol" ar="رمز العملة" /></strong></td>
                                <td>
                                    <input
                                        value="{!! $site_settings->currency_symbol ?? '' !!}"
                                        type="text"
                                        id="decoded_symbol"
                                        class="form-control"
                                        placeholder="Currency Symbol"
                                        readonly
                                    >
                                </td>
                            </tr>
                            <tr>
                                <td><strong><x-bi en="Currency Code" ar="رمز العملة" /></strong></td>
                                <td>
                                    <input
                                        value="{{ $site_settings->currency_code ?? '' }}"
                                        type="text"
                                        id="currency_code"
                                        class="form-control"
                                        placeholder="Currency Code"
                                        readonly
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><x-bi en="Save" ar="حفظ" /></button>
                </div>
            </form>
        </div>

        
   
        </div>
      </div>

    

      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
                        <span><x-bi en="Customer Order Settings" ar="إعدادات طلبات العملاء" /></span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.order-settings.update') }}" method="POST">
                @csrf
    
                <div class="form-group">
                    <label for="price_per_mile"><x-bi :en="'Price per Mile (' . $site_settings->currency_symbol . ')'" :ar="'السعر لكل ميل (' . $site_settings->currency_symbol . ')'" /></label>
                    <input type="number" name="price_per_mile" id="price_per_mile" class="form-control" value="{{ $order_settings->price_per_mile ?? '' }}" step="0.01" required>
                </div>
    
                <div class="form-group">
                    <label for="distance_limit_in_miles"><x-bi en="Distance Limit in Miles" ar="حد المسافة بالأميال" /></label>
                    <input type="number" name="distance_limit_in_miles" id="distance_limit_in_miles" class="form-control" value="{{ $order_settings->distance_limit_in_miles ?? '' }}" required>
                </div>
    
                <button type="submit" class="btn btn-primary"><x-bi en="Save" ar="حفظ" /></button>
            </form>
        </div>
    </div>






<div class="modal fade" id="socialMediaModal" tabindex="-1" aria-labelledby="socialMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="socialMediaForm" method="POST">
                @csrf
                <input type="hidden" id="socialMediaFormMethod" name="_method" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="socialMediaModalLabel"><x-bi en="Social Media Handle" ar="معرّف وسائل التواصل" /></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="handle" class="form-label"><x-bi en="Handle" ar="المعرّف" /></label>
                        <input type="text" class="form-control" id="handle" name="handle" required>
                    </div>
                    <div class="mb-3">
                        <label for="social_media" class="form-label"><x-bi en="Social Media" ar="وسائل التواصل" /></label>
                        <select class="form-control" id="social_media" name="social_media" required>
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>
                            <option value="youtube">YouTube</option>
                            <option value="tiktok">TikTok</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><x-bi en="Save" ar="حفظ" /></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                </div>
            </form>
        </div>
    </div>
</div>






    <div class="modal fade" id="phoneNumberModal" tabindex="-1" aria-labelledby="phoneNumberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="phoneNumberForm" method="POST">
                    @csrf
                    <input type="hidden" id="phoneNumberFormMethod" name="_method" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="phoneNumberModalLabel"><x-bi en="Phone Number" ar="رقم الهاتف" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="phone_number" class="form-label"><x-bi en="Phone Number" ar="رقم الهاتف" /></label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Example: +44 123 456 7654" required>
                        </div>

 
                        
                        <div class="form-check form-check-flat form-check-primary">

                            <label class="form-check-label" for="use_whatsapp">
                            <input type="checkbox" class="form-check-input"  id="use_whatsapp" name="use_whatsapp" value="1">  <x-bi en="Use WhatsApp" ar="استخدام واتساب" /> <i class="input-helper"></i>
                            </label>
                        
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><x-bi en="Save" ar="حفظ" /></button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    






<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addressForm" method="POST">
                @csrf
                <input type="hidden" id="addressFormMethod" name="_method" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalLabel"><x-bi en="Address" ar="العنوان" /></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                     <div class="mb-3">
                        <label for="address" class="form-label"><x-bi en="Search Address" ar="البحث عن العنوان" /></label>
                        <input type="text"
                               class="form-control"
                               id="address"
                               name="address"
                               placeholder="Start typing your address..."
                               autocomplete="off"
                               required>
                    </div>

                     <div class="mb-2">
                        <label for="line1" class="form-label"><x-bi en="Street" ar="الشارع" /></label>
                        <input type="text" class="form-control" id="line1" name="line1" readonly>
                    </div>

                    <div class="mb-2">
                        <label for="line2" class="form-label"><x-bi en="Apt / Suite" ar="الشقة / الجناح" /></label>
                        <input type="text" class="form-control" id="line2" name="line2" readonly>
                    </div>

                    <div class="mb-2">
                        <label for="city" class="form-label"><x-bi en="City" ar="المدينة" /></label>
                        <input type="text" class="form-control" id="city" name="city" readonly>
                    </div>

                    <div class="mb-2">
                        <label for="state" class="form-label"><x-bi en="State / Province" ar="الولاية / المقاطعة" /></label>
                        <input type="text" class="form-control" id="state" name="state" readonly>
                    </div>

                    <div class="mb-2">
                        <label for="postal_code" class="form-label"><x-bi en="Postal Code" ar="الرمز البريدي" /></label>
                        <input type="text" class="form-control" id="postal_code" name="postal_code" readonly>
                    </div>

                    <div class="mb-2">
                        <label for="country" class="form-label"><x-bi en="Country" ar="الدولة" /></label>
                        <input type="text" class="form-control" id="country" name="country" readonly>
                    </div>

                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><x-bi en="Save" ar="حفظ" /></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="workingHourModal" tabindex="-1" aria-labelledby="workingHourModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="workingHourForm" method="POST">
                @csrf
                <input type="hidden" id="workingHourFormMethod" name="_method" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="workingHourModalLabel"><x-bi en="Working Hour" ar="ساعات العمل" /></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    {{-- Day of Week --}}
                    <div class="mb-3">
                        <label for="day_of_week" class="form-label"><x-bi en="Day of Week" ar="اليوم" /></label>
                        <select class="form-control" id="day_of_week" name="day_of_week" required>
                            <option value="" disabled selected>Select day</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>

                    {{-- Opens At --}}
                    <div class="mb-3">
                        <label for="opens_at" class="form-label"><x-bi en="Opens At" ar="يفتح عند" /></label>
                        <input type="time" class="form-control" id="opens_at" name="opens_at">
                    </div>

                    {{-- Closes At --}}
                    <div class="mb-3">
                        <label for="closes_at" class="form-label"><x-bi en="Closes At" ar="يغلق عند" /></label>
                        <input type="time" class="form-control" id="closes_at" name="closes_at">
                    </div>

                    {{-- Closed Checkbox --}}
                    <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label" for="is_closed">
                            <input type="checkbox" class="form-check-input" id="is_closed" name="is_closed" value="1">
                            <x-bi en="Closed all day" ar="مغلق طوال اليوم" /> <i class="input-helper"></i>
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><x-bi en="Save" ar="حفظ" /></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                </div>
            </form>
        </div>
    </div>
</div>

 
    






    <div class="modal fade" id="deletePhoneNumberModal" tabindex="-1" aria-labelledby="deletePhoneNumberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deletePhoneNumberForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePhoneNumberModalLabel"><x-bi en="Delete Phone Number" ar="حذف رقم الهاتف" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <x-bi en="Are you sure you want to delete this phone number?" ar="هل أنت متأكد أنك تريد حذف رقم الهاتف هذا؟" />
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger"><x-bi en="Delete" ar="حذف" /></button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    


    <div class="modal fade" id="deleteAddressModal" tabindex="-1" aria-labelledby="deleteAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteAddressForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteAddressModalLabel"><x-bi en="Delete Address" ar="حذف العنوان" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <x-bi en="Are you sure you want to delete this address?" ar="هل أنت متأكد أنك تريد حذف هذا العنوان؟" />
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger"><x-bi en="Delete" ar="حذف" /></button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
   






    <div class="modal fade" id="deleteWorkingHourModal" tabindex="-1" aria-labelledby="deleteWorkingHourModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteWorkingHourForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteWorkingHourModalLabel"><x-bi en="Delete Working Hour" ar="حذف ساعات العمل" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <x-bi en="Are you sure you want to delete this working hour?" ar="هل أنت متأكد أنك تريد حذف ساعات العمل هذه؟" />
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger"><x-bi en="Delete" ar="حذف" /></button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="deleteSocialMediaHandleModal" tabindex="-1" aria-labelledby="deleteAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteSocialMediaHandleForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteAddressModalLabel"><x-bi en="Delete Social Media Handle" ar="حذف معرّف وسائل التواصل" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <x-bi en="Are you sure you want to delete this social media handle?" ar="هل أنت متأكد أنك تريد حذف معرّف وسائل التواصل هذا؟" />
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger"><x-bi en="Delete" ar="حذف" /></button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    
    </div>
    <!-- content-wrapper ends -->
    @include('partials.admin.footer')
  </div>
  <!-- main-panel ends -->
@endsection



 
