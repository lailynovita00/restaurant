<!-- Display success message -->
@if (session('success'))
    <x-alert type="success" :message="session('success')" />
@elseif (request()->query('status'))
    @if(request()->query('action') === 'toggle')
        <x-alert type="success" :message="request()->query('status') === 'hidden' ? 'Menu has been hidden from the customer website.' : 'Menu is visible again on the customer website.'" />
    @endif
@endif

<!-- Display custom error message -->
@if (session('error'))
    <x-alert type="danger" :message="session('error')" />
@endif

<!-- Display validation errors -->
@if ($errors->any())
    <x-alert type="danger" :message="$errors->first()" />
@endif

<!-- Debug: Show all session keys -->
@php
$sessionKeys = array_keys(session()->all());
@endphp
@if(config('app.debug') && in_array(Auth::user()->role ?? null, ['global_admin', 'cashier']))
    {{-- Uncomment below for debugging --}}
    {{-- <div class="alert alert-info" style="position: fixed; top: 70px; right: 10px; z-index: 9999; min-width: 300px;">Session keys: {{ implode(', ', $sessionKeys) }}</div> --}}
@endif

<style>
.alert {
    position: relative;
    z-index: 1000;
}
</style>
