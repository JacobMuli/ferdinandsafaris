@props(['tour'])

<div class="lg:col-span-1">
    <div class="bg-white rounded-xl shadow-lg sticky top-24 overflow-hidden">
        <div class="h-48 relative">
            <img src="{{ $tour->display_image }}"
                 alt="{{ $tour->name }}"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <h2 class="absolute bottom-4 left-4 text-white text-xl font-bold">{{ $tour->name }}</h2>
        </div>
        <div class="p-6">
            <div class="space-y-3 text-sm text-gray-700 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-clock w-6 text-emerald-600"></i>
                    <span>{{ $tour->duration_days }} Days</span>
                </div>
                <div class="flex items-center" x-show="adults > 0">
                    <i class="fas fa-users w-6 text-emerald-600"></i>
                    <span x-text="adults + ' Adult' + (adults > 1 ? 's' : '') + (children > 0 ? ' + ' + children + ' Child' + (children > 1 ? 'ren' : '') : '')"></span>
                </div>
            </div>

            <hr class="border-gray-200 mb-4">

            <!-- Dynamic Total Price Display -->
            <div class="space-y-2 mb-6" x-show="pricing && pricing.final_price > 0">
                <div class="flex justify-between text-gray-600">
                    <span>Base Price</span>
                    <span x-text="'$' + Number(pricing.base_price).toLocaleString(undefined, {minimumFractionDigits: 2})"></span>
                </div>
                <div class="flex justify-between text-emerald-600" x-show="pricing.discount > 0">
                    <span>Discount</span>
                    <span x-text="'-$' + Number(pricing.discount).toLocaleString(undefined, {minimumFractionDigits: 2})"></span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Tax (16%)</span>
                    <span x-text="'$' + Number(pricing.tax).toLocaleString(undefined, {minimumFractionDigits: 2})"></span>
                </div>
                <div class="flex justify-between text-xl font-bold text-gray-900 pt-2 border-t">
                    <span>Total</span>
                    <span x-text="'$' + Number(pricing.total).toLocaleString(undefined, {minimumFractionDigits: 2})"></span>
                </div>
            </div>

            <div x-show="!pricing || pricing.final_price === 0" class="mb-6 p-4 bg-emerald-50 rounded-lg">
                <p class="text-sm text-emerald-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    The final price will be calculated based on your participants.
                </p>
            </div>

            <div class="text-sm text-gray-500">
                <p class="mb-2"><i class="fas fa-info-circle mr-1"></i> Payment required after confirmation</p>
                <p><i class="fas fa-check-circle mr-1"></i> Free cancellation 48h prior</p>
            </div>
        </div>
    </div>
</div>
