@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'block py-2 text-emerald-600 font-bold'
            : 'block py-2 text-gray-700 hover:text-emerald-600 font-medium';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>