@props(['type' => 'success', 'message' => ''])

@if($message)
<div class="alert alert-{{ $type }} alert-dismissible mb-3 mx-2" role="alert">
    <button type="button" class="btn-close close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    @if ($type === 'success')
        <i class="fa fa-check-circle"></i>
    @elseif ($type === 'danger')
        <i class="fa fa-exclamation-circle"></i>
    @endif
    <span>{{ $message }}</span>
</div>
@endif
