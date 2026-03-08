@props([
    'active' => false,
    'icon' => null,
    'label' => null,
    'badge' => null,
])

@php
    $base =
        'relative flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200';

    $inactive =
        'text-white/80 hover:text-white hover:bg-white/10';

    $activeClass =
        'bg-white/15 text-white font-semibold shadow-inner';
@endphp

<a {{ $attributes->merge([
    'class' => $base . ' ' . ($active ? $activeClass : $inactive),
]) }}>
    @if($active)
        <span class="absolute left-0 top-0 h-full w-1 bg-emerald-400 rounded-r"></span>
    @endif

    @if($icon)
        <i class="{{ $icon }} w-5 text-white/90"></i>
    @endif

    <span class="whitespace-nowrap flex-1">{{ $label }}</span>

    @if(isset($badge) && $badge > 0)
        <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-sm">
            {{ $badge }}
        </span>
    @endif
</a>
