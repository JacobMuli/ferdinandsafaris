@props(['disabled' => false, 'label' => '', 'name', 'rows' => 4, 'required' => false])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-700 ">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <textarea {{ $disabled ? 'disabled' : '' }}
              name="{{ $name }}"
              id="{{ $name }}"
              rows="{{ $rows }}"
              {{ $attributes->merge(['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5']) }}
              @if($required) required @endif
    >{{ $slot }}</textarea>

    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
