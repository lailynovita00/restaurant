@props([
    'en' => '',
    'ar' => '',
    'tag' => 'span',
    'class' => '',
    'stack' => true,
])

<{{ $tag }} {{ $attributes->merge(['class' => 'bi-text ' . $class]) }}>
    @if($stack)
        <span class="bi-en">{{ $en }}</span>
        <span class="bi-ar" dir="rtl" lang="ar">{{ $ar }}</span>
    @else
        <span class="bi-en">{{ $en }}</span>
        <span class="bi-sep"> / </span>
        <span class="bi-ar-inline" dir="rtl" lang="ar">{{ $ar }}</span>
    @endif
</{{ $tag }}>
