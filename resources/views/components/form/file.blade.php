@props(['disabled' => false, 'label' => '', 'name', 'required' => false])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-700 ">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <input {{ $disabled ? 'disabled' : '' }}
           type="file"
           name="{{ $name }}"
           id="{{ $name }}"
           {{ $attributes->merge(['class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50  focus:outline-none']) }}
           @if($required) required @endif
    >
    <p class="mt-1 text-xs text-gray-500 ">
        SVG, PNG, JPG or GIF (MAX. 2MB).
    </p>

    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
