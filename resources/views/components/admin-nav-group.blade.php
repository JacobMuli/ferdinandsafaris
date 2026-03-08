@props([
    'label',
    'icon',
    'active' => false,
    'key'
])

<div class="mb-1">
    <button @click="activeGroup = (activeGroup === '{{ $key }}' ? null : '{{ $key }}')"
            type="button"
            class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200"
            :class="activeGroup === '{{ $key }}' ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/5'">
        <div class="flex items-center gap-3">
            <i class="{{ $icon }} w-5 text-center"></i>
            <span class="font-medium">{{ $label }}</span>
        </div>
        <i class="fas fa-chevron-right text-xs transition-transform duration-200"
           :class="{ 'rotate-90': activeGroup === '{{ $key }}' }"></i>
    </button>

    <div x-show="activeGroup === '{{ $key }}'"
         x-collapse
         class="mt-1 ml-4 space-y-1 pl-4 border-l border-white/10"
         style="display: none;">
        {{ $slot }}
    </div>
</div>
