@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'font-bold'
            : 'font-medium';
@endphp

<a {{ $attributes->merge(['class' => $classes . ' transition']) }}>
    {{ $slot }}
</a>