@props(['tour', 'customerTypes'])

<div>
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <span class="bg-emerald-100 text-emerald-600 w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-sm mr-2">3</span>
        <i class="fas fa-users text-emerald-600 mr-2"></i>
        Travelers
    </h3>

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Customer Type -->
    
    <div class="mb-6">
        <x-input-label for="customer_type" :value="__('Customer Type')" class="mb-2 text-base" />
        <select name="customer_type" id="customer_type" x-model="customerType"
                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-base py-3">
            @foreach($customerTypes as $type)
                <option value="{{ $type }}" {{ old('customer_type') == $type ? 'selected' : '' }}>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>
    </div>

        <!-- Counts -->
        <div class="grid grid-cols-2 gap-4">
            <div :class="customerType === 'individual' ? 'col-span-2' : ''">
                <x-input-label for="adults_count" :value="__('Adults')" class="mb-2" />
                <x-text-input
                       type="number"
                       id="adults_count"
                       name="adults_count"
                       min="1"
                       x-model.number="adults"
                       x-bind:readonly="customerType === 'individual'"
                       class="w-full focus:border-emerald-500 focus:ring-emerald-500"
                       x-bind:class="customerType === 'individual' ? 'bg-gray-100 cursor-not-allowed' : ''" />
            </div>

            <div x-show="customerType !== 'individual'" x-transition>
                <x-input-label for="children_count" :value="__('Children')" class="mb-2" />
                <x-text-input
                       type="number"
                       id="children_count"
                       name="children_count"
                       min="0"
                       x-model.number="children"
                       class="w-full focus:border-emerald-500 focus:ring-emerald-500" />
            </div>
        </div>
    </div>
</div>